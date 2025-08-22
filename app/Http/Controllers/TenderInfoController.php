<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Emds;
use App\Models\Item;
use App\Models\User;
use App\Models\Pqr;
use App\Models\Finance;
use App\Models\Status;
use App\Mail\TlApproval;
use App\Models\Location;
use App\Models\Websites;
use App\Models\TenderDoc;
use App\Models\VendorOrg;
use App\Models\WorkOrder;
use App\Mail\TenderUpdate;
use App\Models\TenderInfo;
use App\Models\TenderItem;
use App\Helpers\MailHelper;
use App\Mail\TenderCreated;
use App\Models\EligibleDoc;
use App\Mail\TenderRejected;
use App\Models\Organization;
use App\Models\TenderClient;
use Illuminate\Http\Request;
use App\Mail\TenderinfoFilled;
use App\Models\Clintdirectory;
use App\Services\TimerService;
use Yajra\DataTables\DataTables;
use App\Models\TenderInformation;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;
use App\Mail\TenderStatusUpdateMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenderInfoController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public $reason = [
        9 => 'OEM Bidders only',
        10 => 'Not allowed by OEM',
        11 => 'Not Eligible',
        12 => 'Product type bid',
        13 => 'Small Value Tender',
        14 => 'Product not available',
        15 => 'An electrical Contractor license needed',
    ];
    
    public $commercial = [
        1 => 'Item Wise GST Inclusive',
        2 => 'Item Wise Pre GST',
        3 => 'Overall GST Inclusive',
        4 => 'Overall Pre GST',
    ];
    
    public $maf = [
        1 => 'Yes (project specific)',
        2 => 'Yes (general)',
        3 => 'No',
    ];
    
    public $tenderFees = [
        1 => 'Pay on Portal',
        2 => 'NEFT/RTGS',
        3 => 'DD',
        4 => 'Not Applicable',
    ];
    
    public $emdReq = [
        1 => 'Yes',
        2 => 'No',
        3 => 'Exempt',
    ];
    
    public $emdOpt = [
        1 => 'Pay on Portal',
        2 => 'NEFT/RTGS',
        3 => 'DD',
        4 => 'BG',
        5 => 'FDR',
        6 => 'Not Applicable',
    ];
    
    public $revAuction = [
        1 => 'Yes',
        2 => 'No',
    ];

    public $teams = [
        'AC' => 'AC',
        'DC' => 'DC',
    ];
    
    public function index()
    {
        $user = Auth::user();
        $permissions = explode(',', $user->permissions);
        return view('tender.index', compact('permissions'));
    }

    public function getTenderData(Request $request, $type)
    {
        try {
            $user = Auth::user();
            $team = $request->input('team');
            if (!in_array($type, ['prep', 'dnb', 'bid', 'won', 'lost'])) {
                throw new \InvalidArgumentException('Invalid tender type');
            }

            Log::info("Fetching $type tenders");

            $query = TenderInfo::with([
                'organizations:id,name',
                'users:id,name',
                'itemName:id,name',
                'statuses:id,name'
            ])
                ->select('tender_infos.*')
                ->leftJoin('users', 'users.id', '=', 'tender_infos.team_member')
                ->leftJoin('organizations', 'organizations.id', '=', 'tender_infos.organisation')
                ->leftJoin('statuses', 'statuses.id', '=', 'tender_infos.status')
                ->leftJoin('items as item_name', 'item_name.id', '=', 'tender_infos.item')
                ->where('tender_infos.deleteStatus', '0');

            // Get Team Wise
            if ($team) {
                $query->where('tender_infos.team', $team);
            }
            
            if (!in_array($user->role, ['admin'])) {
                if (in_array($user->role, ['team-leader', 'coordinator'])) {
                    $query->where('tender_infos.team', $user->team);
                } else {
                    $query->where('tender_infos.team_member', $user->id);
                }
            }

            // Handle search
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];

                $query->where(function ($q) use ($searchValue) {
                    $q->where('tender_infos.tender_no', 'LIKE', "%{$searchValue}%")
                        ->orWhere('tender_infos.tender_name', 'LIKE', "%{$searchValue}%")
                        ->orWhere('tender_infos.gst_values', 'LIKE', "%{$searchValue}%")
                        ->orWhere('tender_infos.emd', 'LIKE', "%{$searchValue}%")
                        ->orWhere('tender_infos.due_date', 'LIKE', "%{$searchValue}%")
                        ->orWhere('users.name', 'LIKE', "%{$searchValue}%")
                        ->orWhere('organizations.name', 'LIKE', "%{$searchValue}%")
                        ->orWhere('statuses.name', 'LIKE', "%{$searchValue}%");
                });
            }

            // Type-based filtering
            switch ($type) {
                case 'prep':
                    $query->whereIn('tender_infos.status', ['1', '2', '3', '4', '5', '6', '7', '29', '30']);
                    break;
                case 'dnb':
                    $query->whereIn('tender_infos.status', ['8', '9', '10', '11', '12', '13', '14', '15', '16', '31', '32', '38', '39']);
                    break;
                case 'bid':
                    $query->whereIn('tender_infos.status', ['17', '19', '20', '23']);
                    break;
                case 'won':
                    $query->whereIn('tender_infos.status', ['25', '26', '27', '28']);
                    break;
                case 'lost':
                    $query->whereIn('tender_infos.status', ['18', '21', '22', '24']);
                    break;
            }

            // Log::info('Fetching tenders SQL: ' . $query->toSql());

            return DataTables::of($query)
                ->addColumn('action', function ($tender) {
                    return view('partials.tender-actions', compact('tender'))->render();
                })
                ->addColumn('timer', function ($tender) {
                    return view('partials.timer', compact('tender'))->render();
                })
                ->editColumn('due_date', function ($tender) {
                    return '<span class="d-none">'. strtotime($tender->due_date) .'</span>' . date('d-m-Y', strtotime($tender->due_date)) . '<br>' . date('h:i A', strtotime($tender->due_time));
                })
                ->editColumn('gst_values', function ($tender) {
                    return format_inr($tender->gst_values);
                })
                ->rawColumns(['action', 'timer', 'due_date'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error loading data',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    public function create()
    {
        $users = User::all()->where('role', '!=', 'admin')->where('status', '1');
        $statuses = Status::all();
        $items = Item::where('status', '1')->get();
        $organisations = Organization::where('status', '1')->get();
        $locations = Location::where('status', '1')->get();
        $websites = Websites::where('status', '1')->get();
        $teams = $this->teams;
        return view('tender.create', compact('users', 'statuses', 'organisations', 'items', 'locations', 'websites', 'teams'));
    }
    
    public function infoCreate($id)
    {
        $tenderInfo = TenderInfo::find($id);
        $items = Item::all();
        $tender = TenderInformation::where('tender_id', $id)->first();
        $reason = $this->reason;
        $commercial = $this->commercial;
        $maf = $this->maf;
        $tenderFees = $this->tenderFees;
        $emdReq = $this->emdReq;
        $emdOpt = $this->emdOpt;
        $revAuction = $this->revAuction;

        return view('tender.info', compact(
            'tenderInfo',
            'items',
            'tender',
            'reason',
            'commercial',
            'maf',
            'tenderFees',
            'emdReq',
            'emdOpt',
            'revAuction'
        ));
    }
    
    public function store(Request $request)
    {
        $va = $request->validate([
            'tender_no' => 'required|string',
            'organisation' => 'required|string|max:255',
            'tender_name' => 'required|string|max:255',
            'gst' => 'required|numeric',
            'tender_fees' => 'required|numeric',
            'emd' => 'required|numeric',
            'team' => 'required',
            'team_member' => 'required|exists:users,id',
            'due_date' => 'required|date',
            'due_time' => 'required',
            'location' => 'required|exists:locations,id',
            'website' => 'required|exists:websites,id',
            'remarks' => 'nullable|string',
            'item' => 'required|string',
        ]);

        try {
            Log::info('Tender create started');

            $tender = TenderInfo::create([
                'tender_no' => $request->tender_no,
                'organisation' => $request->organisation,
                'tender_name' => $request->tender_name,
                'item' => $request->item,
                'gst_values' => $request->gst,
                'tender_fees' => $request->tender_fees,
                'emd' => $request->emd,
                'team' => $request->team,
                'team_member' => $request->team_member,
                'due_date' => $request->due_date,
                'due_time' => $request->due_time,
                'status' => '1',
                'location' => $request->location,
                'website' => $request->website,
                'remarks' => $request->remarks,
            ]);

            Log::info('Tender created');

            // Start the 'tender_info_sheet' timer right after tender creation
            $this->timerService->startTimer($tender, 'tender_info_sheet', 72);

            Log::info('Tender info sheet timer started');

            $attachments = [];
            if ($request->hasFile('docs')) {
                foreach ($request->file('docs') as $file) {
                    $name = str_replace(' ', '_', $file->getClientOriginalName());
                    $filename = $name . '_' . time() . '_' . rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/docs'), $filename);
                    $doc = new TenderDoc();
                    $doc->tender_id = $tender->id;
                    $doc->doc_path = $filename;
                    $attachments[] = $filename;
                    $doc->save();
                }
            }

            Log::info('Tender attachments saved');

            $tenderId = $tender->id;

            if ($this->sendMail($tenderId, $attachments)) {
                Log::info('Tender mail sent successfully');
                return redirect()->back()->with('success', 'Tender created and Mail sent successfully');
            } else {
                Log::info('Tender mail not sent');
                return redirect()->route('tender.create')->with('success', 'Tender created but Mail not sent.');
            }
        } catch (\Exception $e) {
            Log::error("Tender Create Error: " . $e->getMessage());
            return redirect()->route('tender.create')->with('error', $e->getMessage());
        }
    }
    
    public function show(TenderInfo $tenderInfo, $id)
    {
        $tender = TenderInfo::where('deleteStatus', '0')->find($id);
        $reason = $this->reason;
        $commercial = $this->commercial;
        $maf = $this->maf;
        $tenderfees = $this->tenderFees;
        $emdReq = $this->emdReq;
        $emdopt = $this->emdOpt;
        $revAuction = $this->revAuction;
        return view('tender.show', compact('tender', 'reason', 'commercial', 'maf', 'tenderfees', 'emdReq', 'emdopt', 'revAuction'));
    }
    
    public function edit(TenderInfo $tenderInfo, $id)
    {
        $tenderInfo = TenderInfo::find($id);
        $items = Item::where('status', '1')->get();
        $statuses = Status::where('status', '1')->get();
        $organisations = Organization::where('status', '1')->get();
        $users = User::all()->where('role', '!=', 'admin')->where('status', '1');
        $locations = Location::where('status', '1')->get();
        $websites = Websites::where('status', '1')->get();
        $teams = $this->teams;
        return view('tender.edit', compact('tenderInfo', 'users', 'statuses', 'organisations', 'items', 'locations', 'websites', 'teams'));
    }
    
    public function update(Request $request, TenderInfo $tenderInfo, $id)
    {
        $request->validate([
            'tender_no' => 'nullable|string',
            'team' => 'string|max:50',
            'organisation' => 'nullable|string|max:255',
            'tender_name' => 'nullable|string|max:255',
            'gst' => 'nullable|numeric',
            'tender_fees' => 'nullable|numeric',
            'emd' => 'nullable|numeric',
            'team_member' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable',
            'status' => 'nullable',
            'location' => 'nullable|exists:locations,id',
            'website' => 'nullable|exists:websites,id',
            'remarks' => 'nullable|string',
            'item' => 'nullable|string'
        ]);

        try {
            $tenderInfo = TenderInfo::find($id);
            $tenderInfo->update([
                'tender_no' => $request->tender_no,
                'organisation' => $request->organisation,
                'tender_name' => $request->tender_name,
                'team' => $request->team,
                'item' => $request->item,
                'gst_values' => $request->gst,
                'tender_fees' => $request->tender_fees,
                'emd' => $request->emd,
                'team_member' => $request->team_member,
                'due_date' => $request->due_date,
                'due_time' => $request->due_time,
                'status' => $request->status,
                'location' => $request->location,
                'website' => $request->website,
                'remarks' => $request->remarks
            ]);

            Log::info('Tender updated');

            $attachments = [];
            if ($request->hasFile('docs')) {
                foreach ($request->file('docs') as $file) {
                    $name = str_replace(' ', '_', $file->getClientOriginalName());
                    $filename = $name . '_' . time() . '_' . rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/docs'), $filename);

                    $attachments[] = $filename;
                    TenderDoc::create([
                        'tender_id' => $tenderInfo->id,
                        'doc_path' => $filename,
                    ]);

                    Log::info("Attachment uploaded: $filename");
                }
            }
            $tenderId = $tenderInfo->id;

            $changedFields = [];
            if ($tenderInfo->wasChanged('team_member')) {
                $changedFields[] = 'Team Member';
            }
            if ($tenderInfo->wasChanged('tender_no')) {
                $changedFields[] = 'Tender Number';
            }
            if ($tenderInfo->wasChanged('team')) {
                $changedFields[] = 'Team';
            }

            if (!empty($changedFields)) {
                $changedField = implode(', ', $changedFields);
                $mailSent = $this->tenderUpdateMail($tenderId, $changedField);
                $message = $mailSent ? 'Tender updated and Mail sent successfully' : 'Tender updated but Mail not sent.';
                Log::info($message);
            } else {
                $message = 'Tender updated successfully';
                Log::info($message);
            }
            return redirect()->back()->with('success', $message);
        } catch (\Throwable $th) {
            Log::error("Tender Update Error: " . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    
    public function infoUpdate(Request $request, TenderInfo $tenderInfo, $id)
    {
        Log::info('Starting infoUpdate' . json_encode($request->all()));

        $this->validateRequest($request);

        try {
            $pt = TenderInformation::firstOrNew(['tender_id' => $id]);
            Log::info('Fetched or created TenderInformation', ['tender_id' => $id, 'info_id' => $pt->id]);

            $tender = TenderInfo::findOrFail($id);
            Log::info('Fetched TenderInfo', ['tender_id' => $id]);

            $this->updateTenderBasicInfo($tender, $request);
            Log::info('Updated tender basic info', ['tender_id' => $id]);

            $this->updateTenderInformation($pt, $request, $id);
            Log::info('Updated tender information', ['info_id' => $pt->id]);

            $this->syncRelatedModels($request, $pt, $id);
            Log::info('Synced related models', ['info_id' => $pt->id]);

            $this->handleClientDirectory($request);
            Log::info('Handled client directory', ['tender_id' => $id]);

            $this->timerService->stopTimer($tender, 'tender_info_sheet');
            $this->timerService->startTimer($tender, 'tender_approval', 24);
            Log::info('Handled timers', ['info_id' => $pt->id]);

            $tenderInfo = $pt->tender;
            $last_id = $id;

            // $this->sendMailAcc($tenderInfo, $last_id);
            Log::info('Sent acceptance mail', ['tender_id' => $tenderInfo->id]);

            Log::info('infoUpdate completed successfully', ['tender_id' => $id]);
            return redirect()->route('tender.index')->with('success', 'Tender Info updated successfully');
        } catch (\Throwable $th) {
            Log::error("Tender Info Update Error: " . $th->getMessage(), ['tender_id' => $id]);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    
    private function validateRequest($request)
    {
        $request->validate([
            'is_rejectable' => 'nullable|string',
            'reject_reason' => 'nullable|string',
            'reject_remarks' => 'nullable|string',
            'tender_fee' => 'nullable|array',
            'emd_req' => 'nullable|string',
            'emd_opt' => 'nullable|array',
            'rev_auction' => 'nullable|string',
            'pt_supply' => 'nullable|string',
            'pt_ic' => 'nullable|string',
            'pbg' => 'nullable|string',
            'pbg_duration' => 'nullable|string',
            'bid_valid' => 'nullable|string',
            'comm_eval' => 'nullable|string',
            'maf_req' => 'nullable|string',
            'supply' => 'nullable|string',
            'installation' => 'nullable|string',
            'ldperweek' => 'nullable|string',
            'maxld' => 'nullable|string',
            'phyDocs' => 'nullable|string',
            'dead_date' => 'nullable|string',
            'dead_time' => 'nullable|string',
            'tech_eligible' => 'nullable|string',
            'tecv[order1]' => 'nullable|string',
            'tecv[order2]' => 'nullable|string',
            'tecv[order3]' => 'nullable|string',
            'aat' => 'nullable|string',
            'aat_amt' => 'nullable|string',
            'wc' => 'nullable|string',
            'wc_amt' => 'nullable|string',
            'sc' => 'nullable|string',
            'sc_amt' => 'nullable|string',
            'nw' => 'nullable|string',
            'nw_amt' => 'nullable|string',
            'wo[*][wo_name]' => 'nullable|string',
            'doc[*][doc_name]' => 'nullable|string',
            'client_name' => 'nullable|string',
            'client_designation' => 'nullable|string',
            'client_email' => 'nullable|string',
            'client_mobile' => 'nullable|string',
            'client_organisation' => 'nullable|string',
            'courier_address' => 'nullable|string',
            'te_remark' => 'nullable|string',
        ]);
    }

    private function updateTenderBasicInfo(TenderInfo $tender, Request $request)
    {
        Log::info('Updating tender basic info', ['tender_id' => $tender->id, 'request' => $request->all()]);

        $tender->fill($request->only([
            'client_organisation',
            'courier_address'
        ]));

        Log::info('Updating tender status', ['tender_id' => $tender->id, 'status' => $request->reject_reason ?? 2]);

        $tender->status = $request->reject_reason ?? 2;
        $tender->save();

        Log::info('Tender basic info updated', ['tender_id' => $tender->id]);
    }

    private function updateTenderInformation(TenderInformation $pt, Request $request, $id)
    {
        Log::info('Updating tender information', ['info_id' => $pt->id, 'request' => $request->all()]);

        $data = $request->only([
            'is_rejectable',
            'reject_reason',
            'reject_remarks',
            'emd_req',
            'rev_auction',
            'pt_supply',
            'pt_ic',
            'pbg',
            'pbg_duration',
            'bid_valid',
            'comm_eval',
            'maf_req',
            'supply',
            'installation',
            'ldperweek',
            'maxld',
            'phyDocs',
            'dead_date',
            'dead_time',
            'tech_eligible',
            'aat',
            'aat_amt',
            'wc',
            'wc_amt',
            'sc',
            'sc_amt',
            'nw',
            'nw_amt',
            'te_remark'
        ]);

        Log::info('Data to be updated', ['data' => $data]);

        $data['tender_fees'] = $request->filled('tender_fee')
            ? implode(',', $request->tender_fee)
            : $pt->tender_fees;

        $data['emd_opt'] = $request->filled('emd_opt')
            ? implode(',', $request->emd_opt)
            : $pt->emd_opt;

        $tecv = $request->get('tecv', []);
        $data['order1'] = $tecv['order1'] ?? 0;
        $data['order2'] = $tecv['order2'] ?? 0;
        $data['order3'] = $tecv['order3'] ?? 0;

        Log::info('Updated data', ['data' => $data]);

        $data['tender_id'] = $id;

        $pt->fill($data)->save();

        Log::info('Tender information updated', ['info_id' => $pt->id]);
    }

    private function syncRelatedModels($request, $pt, $id)
    {
        Log::info('Deleting existing work orders', ['info_id' => $pt->id]);
        WorkOrder::where('info_id', $pt->id)->delete();

        $wo = $request->get('wo', []);
        Log::info('Creating new work orders', ['info_id' => $pt->id, 'work_orders' => $wo]);
        foreach ($wo as $value) {
            WorkOrder::create([
                'tender_id' => $id,
                'info_id' => $pt->id,
                'wo_name' => $value['wo_name']
            ]);
        }

        Log::info('Deleting existing eligible docs', ['info_id' => $pt->id]);
        EligibleDoc::where('info_id', $pt->id)->delete();

        $doc = $request->get('docs', []);
        Log::info('Creating new eligible docs', ['info_id' => $pt->id, 'docs' => $doc]);
        foreach ($doc as $value) {
            EligibleDoc::create([
                'tender_id' => $id,
                'info_id' => $pt->id,
                'doc_name' => $value['doc_name']
            ]);
        }
    }

    private function handleClientDirectory($request)
    {
        $clients = $request->get('client', []);

        // Only process if client data is sent
        if (!empty($clients)) {
            Log::info('Client list found. Updating client list for tender_id: ' . $request->id);

            // Delete old TenderClient records
            TenderClient::where('tender_id', $request->id)->delete();

            $org = $request->client_organisation;
            $addr = $request->courier_address;

            foreach ($clients as $client) {
                TenderClient::create([
                    'tender_id' => $request->id,
                    'client_name' => $client['client_name'],
                    'client_designation' => $client['client_designation'],
                    'client_email' => $client['client_email'],
                    'client_mobile' => $client['client_mobile'],
                    'client_organisation' => $org,
                    'courier_address' => $addr,
                ]);

                // Check and update Clintdirectory
                $existingClient = Clintdirectory::where('email', $client['client_email'])
                    ->orWhere('phone_no', $client['client_mobile'])
                    ->first();

                $clientData = [
                    'name' => $client['client_name'],
                    'designation' => $client['client_designation'],
                    'organization' => $org,
                    'courier_addr' => $addr,
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'strtotime' => Carbon::now('Asia/Kolkata')->timestamp,
                ];

                if ($existingClient) {
                    $existingClient->update($clientData);
                } else {
                    Clintdirectory::create(array_merge($clientData, [
                        'email' => $client['client_email'],
                        'phone_no' => $client['client_mobile'],
                    ]));
                }
            }
        } else {
            Log::info('No client data found in request. Skipping client directory update.');
        }
    }

    private function handleTimers($pt, $request)
    {
        if (!$pt->exists) {
            $tender = $pt->tender;
            Log::info('tender_id: ' . $tender->id);
            $this->timerService->stopTimer($tender, 'tender_info_sheet');
            $this->timerService->startTimer($tender, 'tender_approval', 24);
        }
    }
    
    public function destroy(TenderInfo $tenderInfo, $id)
    {
        try {
            $tenderInfo = TenderInfo::find($id);
            if ($tenderInfo) {
                $tenderInfo->deleteStatus = 1;
                $tenderInfo->save();
                return redirect()->back()->with('success', 'Tender Deleted successfully');
            }
            return redirect()->back()->with('error', 'Something went wrong.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function deleteItem($id)
    {
        try {
            $item = TenderItem::findOrFail($id);
            $item->delete();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    
    public function deleteDoc($id)
    {
        try {
            $doc = TenderDoc::findOrFail($id);
            // Delete the file from storage
            if (file_exists(public_path('uploads/docs') . '/' . $doc->doc_path)) {
                unlink(public_path('uploads/docs') . '/' . $doc->doc_path);
            }
            $doc->delete();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    
    public function tlApprovalForm(Request $request)
    {
        $tenderInfo = TenderInformation::find($request->id);
        $tenderFees = $this->tenderFees;
        $emdOpt = $this->emdOpt;
        $pqr = Pqr::all();
        $finance = Finance::all();
        return view('tender.tender-approval-form', compact('tenderInfo', 'tenderFees', 'emdOpt', 'pqr', 'finance'));
    }
    
    public function tlapproval(Request $request)
    {
        return view('tender.tlapprove');
    }

    public function tlapprovalData(Request $request, $type)
    {
        $user = Auth::user();
        $team = $request->input('team');

        $query = TenderInformation::query()
            ->select('tender_information.*')
            ->join('tender_infos', 'tender_information.tender_id', '=', 'tender_infos.id')
            ->with(['tender', 'tender.organizations', 'tender.users', 'tender.itemName', 'tender.statuses']);

        // Team filter for non-admins
        if ($user->role != 'admin') {
            $query->where('tender_infos.team', $user->team);
        } elseif ($team) {
            $query->where('tender_infos.team', $team);
        }

        // Status filter
        if ($type === 'pending') {
            $query->whereIn('tender_infos.tlStatus', ['0', '3']);
        } elseif ($type === 'approved') {
            $query->where('tender_infos.tlStatus', '1');
        } elseif ($type === 'rejected') {
            $query->where('tender_infos.tlStatus', '2');
        }

        // Order by due_date from tender_infos
        // $query->orderByDesc('tender_infos.due_date');

        // Add search functionality for all fields
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('tender', function ($t) use ($search) {
                    $t->where('tender_name', 'like', "%{$search}%")
                        ->orWhere('tender_no', 'like', "%{$search}%")
                        ->orWhere('gst_values', 'like', "%{$search}%")
                        ->orWhere('due_date', 'like', "%{$search}%")
                        ->orWhere('due_time', 'like', "%{$search}%");
                })
                    ->orWhereHas('tender.users', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('tender.itemName', function ($i) use ($search) {
                        $i->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('tender_information.reject_reason', 'like', "%{$search}%")
                    ->orWhere('tender_information.reject_remarks', 'like', "%{$search}%");
            });
        }
        
        // latest bg requests first
        if (!$request->has('order')) {
            $query->orderByDesc('tender_infos.due_date');
        }

        return DataTables::of($query)
            ->addColumn('tender_name', function ($info) {
                return "<strong>{$info->tender->tender_name}</strong> <br>
                                <span class='text-muted'>{$info->tender->tender_no}</span>";
            })
            ->addColumn('users.name', function ($info) {
                return optional(optional($info->tender)->users)->name ?? 'N/A';
            })
            ->addColumn('due_date', function ($info) {
                $tender = $info->tender;
                if (!$tender) return '';
                return '<span class="d-none">' . strtotime($tender->due_date) . '</span>' .
                    date('d-m-Y', strtotime($tender->due_date)) . '<br>' .
                    date('h:i A', strtotime($tender->due_time));
            })
            ->addColumn('gst_values', function ($info) {
                return format_inr($info->tender->gst_values) ?? '0';
            })
            ->addColumn('item_name.name', function ($info) {
                return optional(optional($info->tender)->itemName)->name ?? 'N/A';
            })
            ->addColumn('timer', function ($info) {
                return view('partials.tlapprove-timer', ['tender' => $info->tender])->render();
            })
            ->addColumn('action', function ($info) {
                return view('partials.tlapprove-actions', compact('info'))->render();
            })
            ->rawColumns(['due_date', 'timer', 'action', 'tender_name'])
            ->make(true);
    }
    
    public function tlapproved(Request $request)
    {
        Log::info('Tender Approved/Rejected', $request->all());
        try {
            $rules = [
                'id' => 'required',
                'te' => 'required',
            ];

            if ($request->te == 'No') {
                $rules['status'] = 'required';
            }

            if ($request->status == 1) {
                $rules['rfq_to'] = 'required|array';
                $rules['tender_fees'] = 'required|array';
                $rules['emd_mode'] = 'required|array';
                $rules['pqr_eligible'] = 'required';
                $rules['fin_eligible'] = 'required';
                $rules['wo.*.wo_name'] = 'nullable|string';
                $rules['doc.*.doc_name'] = 'nullable|string';
            }

            if ($request->status == 2) {
                $rules['tender_status'] = 'required';
                $rules['rej_remark'] = 'required';
                $rules['oem_who_denied'] = 'nullable|array';
            }

            if ($request->status == 3) {
                $rules['remarks'] = 'required';
            }

            $request->validate($rules);

            $info = TenderInformation::find($request->id);
            $info->tender_fees = $request->status == 1 ? implode(',', $request->tender_fees) : $info->tender_fees;
            $info->emd_opt = $request->status == 1 ? implode(',', $request->emd_mode) : $info->emd_opt;
            $info->pqr_eligible = $request->status == 1 ? $request->pqr_eligible : $info->pqr_eligible;
            $info->fin_eligible = $request->status == 1 ? $request->fin_eligible : $info->fin_eligible;
            $info->rej_remark = $request->rej_remark;
            $info->save();
            Log::info('TenderInformation updated', $info->toArray());
            if ($request->has('wo')) {
                if (
                    array_filter($request->wo, function ($wo) {
                        return $wo['wo_name'] != null;
                    })
                ) {
                    WorkOrder::where('info_id', $info->id)->delete();
                    foreach ($request->wo as $key => $value) {
                        WorkOrder::create([
                            'tender_id' => $info->tender_id,
                            'info_id' => $info->id,
                            'wo_name' => $value['wo_name'],
                        ]);
                    }
                }
            }

            if ($request->has('docs')) {
                if (
                    array_filter($request->docs, function ($doc) {
                        return $doc['doc_name'] != null;
                    })
                ) {
                    EligibleDoc::where('info_id', $info->id)->delete();
                    foreach ($request->docs as $key => $value) {
                        EligibleDoc::create([
                            'tender_id' => $info->tender_id,
                            'info_id' => $info->id,
                            'doc_name' => $value['doc_name'],
                        ]);
                    }
                }
            }

            $tender = TenderInfo::find($info->tender_id);
            $tender->tlStatus = $request->status ?? '0';
            $tender->tlRemarks = $request->status == 3 ? $request->remarks : $tender->tlRemarks;
            $tender->status = [1 => 3, 2 => $request->tender_status, 3 => 29][$request->status];
            $tender->rfq_to = $request->status == 1
                ? (is_array($request->rfq_to) ? implode(',', $request->rfq_to) : $request->rfq_to)
                : '0';
            $tender->oem_who_denied = $request->status == 2
                ? (is_array($request->oem_who_denied) ? implode(',', $request->oem_who_denied) : $request->oem_who_denied)
                : '0';
            $tender->save();
            Log::info('TenderInfo updated', $tender->toArray());

            // Stop 'tender_approval' timer if tlStatus is 1 and start 'rfq', 'physical_docs', 'emd_request' timer
            $this->timerService->stopTimer($tender, 'tender_approval');
            if ($tender->tlStatus == 1 || $tender->tlStatus == 2) {
                if ($tender->rfq_to != '0') {
                    $this->timerService->startTimer($tender, 'rfq');
                }
                if ($info->emd_req == '1' || $info->emd_req == 1) {
                    $this->timerService->startTimer($tender, 'emd_request');
                }
                if ($info->phyDocs == 'Yes') {
                    $this->timerService->startTimer($tender, 'physical_docs');
                }
                // countdown to 72 hours before the tender due date and time
                $dueDate = new Carbon("{$tender->due_date} {$tender->due_time}");
                $cutoffDate = (clone $dueDate)->subHours(72); // Timer hits zero here
                $now = Carbon::now();

                // This gives positive or negative hours naturally
                $hrs = $now->diffInHours($cutoffDate, false);

                Log::info('Due Date: ' . $dueDate->toDateTimeString() .
                    ' | Cutoff Date: ' . $cutoffDate->toDateTimeString() .
                    ' | Current Time: ' . $now->toDateTimeString() .
                    ' | Hours until/since cutoff: ' . $hrs);

                $this->timerService->startTimer($tender, 'costing_sheet', $hrs);
                $this->timerService->startTimer($tender, 'document_checklist', $hrs);
            }

            if ($this->TlApprove($info)) {
                Log::info('TL Approval Mail Sent successfully');
                if ($request->status == 3) {
                    // delete tender info and restart timer from last stopped time
                    Log::info('TenderInfo going to be deleted', $tender->info->toArray());
                    $tender->info->delete();
                    Log::info('TenderInfo deleted');
                    $tender->tlStatus = '0';
                    $tender->tlRemarks = null;
                    $tender->save();
                    $this->timerService->restartTimer($tender, 'tender_info_sheet');
                    Log::info('Timer Restarting...');
                    $this->timerService->deleteTimer($tender, 'tender_approval');
                }
                return redirect()->route('tlapproval')->with('success', 'Tender Info updated and Mail Sent successfully');
            } else {
                Log::info('Mail not Sent');
                return redirect()->route('tlapproval')->with('success', 'Tender Info updated successfully');
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return redirect()->route('tlapproval')->with('error', $th->getMessage());
        }
    }
    
    public function updateStatus(Request $request)
    {
        try {
            $tenderInfo = TenderInfo::find($request->id);
            $tenderInfo->status = $request->status;
            $tenderInfo->save();
            $tenderId = $tenderInfo->id;
            $rejected = [16, 18, 21, 22, 24, 25, 28];
            
            if (in_array($tenderInfo->status, $rejected)) {
                $this->TenderStatusUpdated($tenderId);
                return redirect()->back()->with('success', 'Tender Updated and Mail Sent successfully.');
            } else {
                if ($this->tenderUpdateMail($tenderId, '')) {
                    return redirect()->back()->with('success', 'Tender Updated and Mail Sent successfully.');
                } else {
                    return redirect()->back()->with('success', 'Tender Updated successfully.');
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    
    public function sendMail($tenderId, $attachments = [])
    {
        try {
            $tender = TenderInfo::find($tenderId);
            // Send email to team member and admin, for example
            $memberId = User::find($tender->team_member);
            $memberMail = $memberId->email ?? 'gyanprakashk55@gmail.com';
            $member = $memberId->name ?? 'gyanprakash';
            $adminMail = User::where('team', $memberId->team)->where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('team', $memberId->team)->where('role', 'team-leader')->first()->email ?? 'gyanprakashk55@gmail.com';
            $coo = User::where('team', $memberId->team)->where('role', 'coordinator')->first();
            $cooMail = $coo->email ?? 'gyanprakashk55@gmail.com';
            $cooName = $coo->name ?? 'gyanprakash';
            $password = $coo->app_password ?? 'password';

            $date = date('d-m-Y', strtotime($tender->due_date));
            $data = [
                'assignee' => $member,
                'coordinator' => $cooName,
                'tenderName' => $tender->tender_name,
                'tenderId' => $tender->tender_id,
                'tenderNo' => $tender->tender_no,
                'website' => $tender->websites->url ?? 'N/A',
                'due_date' => $date,
                'due_time' => $tender->due_time,
                'tenderValue' => $tender->gst_values,
                'tenderFees' => $tender->tender_fees,
                'emd' => $tender->emd,
                'remarks' => $tender->remarks,
                'from' => $memberMail,
                'tenderInfoSheet' => route('tender.info.create', $tender->id),
                'files' => $attachments
            ];
            Log::info("Tender Created DATA: " . json_encode($data));
            MailHelper::configureMailer($cooMail, $password, $cooName);
            // MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Denji');
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($memberMail)
                ->cc([$tlMail, $adminMail])
                ->send(new TenderCreated($data));
            if ($mail) {
                Log::info("Tender Created Email successfully sent ", ['to' => $memberMail, 'cc' => [$tlMail, $adminMail]]);
            } else {
                Log::error("Tender Created Email failed to send");
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("Tender Created Mail Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    
    public function sendMailRej($tenderInfo, $last_id)
    {
        try {
            $member = User::find($tenderInfo->team_member);
            $username = $member->email ?? 'gyanprakashk55@gmail.com';
            $name = $member->name ?? 'gyanprakash';
            $password = $member->app_password ?? 'password';
            $adminMail = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->first()->email ?? 'gyanprakashk55@gmail.com';
            $cooMail = User::where('role', 'coordinator')->first()->email ?? 'gyanprakashk55@gmail.com';
            $pt = TenderInformation::where('tender_id', $last_id)->first();
            // Log::info("PT:  " . json_encode($pt));
            $status = Status::where('id', $pt->reject_reason)->first();
            $data = [
                'assignee' => $name,
                'tenderNo' => $tenderInfo->tender_no,
                'tenderName' => $tenderInfo->tender_name,
                'remarks' => $pt->reject_remarks,
                'reason' => $status->name,
            ];
            Log::info("TenderInfo:  " . json_encode($data));
            MailHelper::configureMailer($username, $password, $name);
            // MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Denji');
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($tlMail)
                ->cc([$cooMail, $adminMail])
                ->send(new TenderRejected($data));
            if ($mail) {
                Log::info("Tender Rejectable Email sent successfully");
            } else {
                Log::error("Tender Rejectable Email failed to send");
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("TenderInfoRejected: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    
    public function sendMailAcc($tenderInfo, $last_id)
    {
        try {
            $request = $tenderInfo->where('id', $last_id)->first();
            $member = User::find($request->team_member);
            $username = $member->email ?? 'gyanprakashk55@gmail.com';
            $membername = $member->name ?? 'gyanprakash';
            $password = $member->app_password ?? 'password';
            $adminMail = User::where('team', $member->team)->where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('team', $member->team)->where('role', 'team-leader')->first()->email ?? 'gyanprakashk55@gmail.com';
            $cooMail = User::where('team', $member->team)->where('role', 'coordinator')->first()->email ?? 'gyanprakashk55@gmail.com';
            $due_date = date('d-m-Y', strtotime($request->due_date));
            $commercial = $this->commercial;
            $maf = $this->maf;
            $tenderFees = $this->tenderFees;
            $emdReq = $this->emdReq;
            $emdOpt = $this->emdOpt;
            $revAuction = $this->revAuction;
            $tf = '';
            $eo = '';
            $info = $request->info;
            if ($info->tender_fees) {
                $f = explode(",", $info->tender_fees);
                foreach ($f as $key => $value) {
                    $tf .= $tenderFees[$value] . ', ';
                }
            }
            if ($info->emd_opt) {
                $e = explode(",", $info->emd_opt);
                foreach ($e as $key => $value) {
                    $eo .= $emdOpt[$value] . ', ';
                }
            }
            $deadline = date('d-m-Y', strtotime($info->dead_date));
            $deadline .= date('h:i A', strtotime($info->dead_time));
            $wo = $info->workOrder;
            $eDocs = $info->eligibleDocs;
            $eds = '';
            if ($eDocs) {
                foreach ($eDocs as $key => $value) {
                    $eds .= 'Docs-' . $value->doc_name . ', ';
                }
            }
            $tenderDocs = $request->docs;
            $tds = '';
            if ($tenderDocs) {
                foreach ($tenderDocs as $key => $value) {
                    $name = explode("_", $value['doc_path'])[0];
                    $tds .= $name . ', ';
                }
            }
            $client = $request->client;
            
            $data = [
                'assignee' => $membername,
                'organization' => $request->organizations->name,
                'tender_name' => $request->tender_name,
                'tender_no' => $request->tender_no,
                'website' => $request->websites->url ?? 'N/A',
                'due_date' => $due_date,
                'recommendation_by_te' => $info->is_rejectable ? 'No' : 'Yes',
                'reason' => $info->tender->statuses->name,
                'tender_fees' => format_inr($request->tender_fees),
                'tender_fees_in_form_of' => $tf,
                'emd' => format_inr($request->emd),
                'emd_required' => $emdReq[$info->emd_req],
                'tender_value' => format_inr($request->gst_values),
                'emd_in_form_of' => $eo,
                'bid_validity' => $info->bid_valid,
                'commercial_evaluation' => $commercial[$info->comm_eval],
                'ra_applicable' => $revAuction[$info->rev_auction],
                'maf_required' => $maf[$info->maf_req],
                'delivery_time' => $info->supply,
                'delivery_time_ic' => $info->installation,
                'pbg_percentage' => $info->pbg,
                'payment_terms' => $info->pt_supply,
                'payment_terms_ic' => $info->pt_ic,
                'pbg_duration' => $info->pbg_duration,
                'ld_percentage' => $info->ldperweek,
                'max_ld' => $info->maxld,
                'phydocs_submission_required' => $info->phyDocs,
                'phydocs_submission_deadline' => $deadline,
                'eligibility_criterion' => $info->tech_eligible,
                'work_value1' => format_inr($info->order1),
                'name1' => '',
                'aat' => $info->aat,
                'aat_amt' => format_inr($info->aat_amt),
                'work_value2' => format_inr($info->order2),
                'name2' => '',
                'wc' => $info->wc,
                'wc_amt' => format_inr($info->wc_amt),
                'work_value3' => format_inr($info->order3),
                'name3' => '',
                'nw' => $info->nw,
                'nw_amt' => format_inr($info->nw_amt),
                'te_docs' => $eds,
                'sc' => $info->sc,
                'sc_amt' => format_inr($info->sc_amt),
                'tender_docs' => $tds,
                'ce_docs' => $eds,
                'clients' => json_encode($client),
                'link' => route('tender.info.create', $request->id),
            ];
            Log::info("MAIL DATA:  " . json_encode($data));
            MailHelper::configureMailer($username, $password, $membername);
            // MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Denji');
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($tlMail)
                ->cc([$cooMail, $adminMail])
                ->send(new TenderinfoFilled($data));

            if ($mail) {
                Log::info("Tender Info Filled Email sent successfully");
            } else {
                Log::error("Tender Info Filled Email failed to send");
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("TenderApproved: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    public function TlApprove($request)
    {
        try {
            Log::info("Tender: " . json_encode($request->tender));
            $member = User::where('id', $request->tender->team_member)->first();
            $memberMail = $member->email ?? 'gyanprakashk55@gmail.com';
            $membername = $member->name ?? 'gyanprakash';
            $adminMail = User::where('team', $member->team)->where('role', 'admin')->value('email') ?? 'gyanprakashk55@gmail.com';
            $cooMail = User::where('team', $member->team)->where('role', 'coordinator')->value('email') ?? 'gyanprakashk55@gmail.com';
            $tl = User::where('team', $member->team)->where('role', 'team-leader')->first();
            $tlMail = $tl->email ?? 'gyanprakashk55@gmail.com';
            $tlName = $tl->name ?? 'gyanprakash';
            $tlPassword = $tl->app_password ?? 'password';
            $vendors = VendorOrg::whereIn('id', explode(',', $request->tender->rfq_to))->get();
            $vendor = $vendors->pluck('name')->implode(', ');
            $tenderName = $request->tender->tender_name ?? '';
            $subjectMap = [
                1 => 'Tender Approved',
                2 => 'Tender Rejected',
                3 => 'Tender Needs Review',
            ];
            $subject = $subjectMap[$request->tender->tlStatus] ?? '';
            $subject .= ' ' . $tenderName;

            $data = [
                'subject' => $subject,
                'tlStatus' => $request->tender->tlStatus,
                'remarks' => $request->tender->tlRemarks,
                'emd' => $request->emd,
                'emdLink' => route('emds.index'),
                'rfqLink' => route('rfq.index'),
                'phyDocs' => $request->phyDocs,
                'vendor' => $vendor,
                'tlName' => $tlName,
                'assignee' => $membername,
                'tenderFeesLink' => route('tender-fees.index'),
                'tenderFeesMode' => implode(', ', array_intersect_key($this->tenderFees, array_flip(explode(',', $request->tender_fees)))) ?? '',
                'emdMode' => implode(', ', array_intersect_key($this->emdOpt, array_flip(explode(',', $request->emd_opt)))) ?? '',
                'pqr' => $request->pqr_eligible,
                'fin' => $request->fin_eligible,
                'rej_remark' => $request->rej_remark,
            ];

            Log::info("TL Approval Mail Data: " . json_encode($data));

            MailHelper::configureMailer($tlMail, $tlPassword, $tlName);
            // MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Denji');
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($memberMail)
                ->cc([$cooMail, $adminMail])
                ->send(new TlApproval($data));

            if ($mail) {
                Log::info("TL approval Email sent successfully");
            } else {
                Log::error("TL approval Email failed to send");
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("TenderApproved: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    
    public function tenderUpdateMail($tenderId, $field)
    {
        set_time_limit(0);
        try {
            $tender = TenderInfo::find($tenderId);
            $recipientEmail = User::find($tender->team_member)->email ?? 'gyanprakashk55@gmail.com';
            $member = User::find($tender->team_member)->name ?? 'gyanprakash';
            $adminMail = User::where('role', 'admin')->where('team', $tender->team)->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->where('team', $tender->team)->first()->email ?? 'gyanprakashk55@gmail.com';
            $coo = User::where('role', 'coordinator')->where('team', $tender->team)->first();
            $cooMail = $coo->email ?? 'gyanprakashk55@gmail.com';
            $cooName = $coo->name ?? 'gyanprakash';
            $password = $coo->app_password ?? 'password';
            $attachments = [];
            foreach ($tender->docs as $doc) {
                $attachments[] = $doc->doc_path;
            }
            $data = [
                'changed' => $field,
                'assignee' => $member,
                'tenderName' => $tender->tender_name,
                'tenderId' => $tender->tender_id,
                'tenderNo' => $tender->tender_no,
                'website' => $tender->websites->url ?? 'N/A',
                'due_date' => date('d-m-Y', strtotime($tender->due_date)),
                'due_time' => date('H:i A', strtotime($tender->due_time)),
                'tenderValue' => format_inr($tender->gst_values),
                'tenderFees' => format_inr($tender->tender_fees),
                'emd' => format_inr($tender->emd),
                'coordinator' => $cooName,
                'remarks' => $tender->remarks,
                'tenderInfoSheet' => route('tender.info.create', $tender->id),
                'files' => $attachments,
            ];
            Log::info("Tender Update Data: " . json_encode($data));

            MailHelper::configureMailer($cooMail, $password, $cooName);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($recipientEmail)
                ->cc([$adminMail, $tlMail])
                ->send(new TenderUpdate($data));
            if ($mail) {
                Log::info("Tender Updated Email sent successfully");
            } else {
                Log::error("Tender Updated Email failed to send");
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("Tender Update Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    
    public function checkAndGenerateTenderName(Request $request)
    {
        $organisation = $request->input('organisation');
        $item = $request->input('item');
        $location = $request->input('location');
        $baseName = "$organisation $item $location";
        $existingCount = TenderInfo::where('tender_name', 'LIKE', "$baseName%")->count();
        if ($existingCount > 0) {
            $uniqueName = "$baseName ($existingCount)";
        } else {
            $uniqueName = $baseName;
        }

        return response()->json(['tender_name' => $uniqueName]);
    }

    public function TenderStatusUpdated($tid)
    {
        $tender = TenderInfo::find($tid);
        $assignee = User::find($tender->team_member);
        $AssigneeName = $assignee->name ?? 'gyanprakash';
        $admin = User::where('role', 'admin')->first();

        $adminMail = $admin->email ?? 'gyanprakashk55@gmail.com';
        $coo = User::where('role', 'coordinator')->where('team', $assignee->team)->first();
        $cooName = $coo->name ?? 'gyanprakash';
        $cooMail = $coo->email ?? 'gyanprakashk55@gmail.com';
        $cooPass = $coo->app_password ?? 'password';
        $emd = Emds::where('tender_id', $tid)->first();
        if (!$emd) {
            Log::error("EMD not found for tender id: $tid");
            return;
        }
        $instrumentType = [
            '1' => 'Demand Draft',
            '2' => 'FDR',
            '3' => 'Cheque',
            '4' => 'BG',
            '5' => 'Bank Transfer',
            '6' => 'Pay on Portal',
        ];

        $purpose = '';
        switch ($emd->instrument_type) {
            case '1':
                $purpose = $emd->emdDemandDrafts->first()?->dd_purpose ?? 'Unknown or Not Updated';
                break;
            case '2':
                $purpose = 'FDR';
                break;
            case '3':
                $purpose = $emd->emdCheques->first()?->cheque_reason ?? 'Unknown or Not Updated';
                break;
            case '4':
                $purpose = $emd->emdBgs ? 'BG' : 'Unknown or Not Updated';;
                break;
            case '5':
                $purpose = $emd->emdBankTransfers->first()?->purpose ?? 'Unknown or Not Updated';
                break;
            case '6':
                $purpose = $emd->emdPayOnPortals->first()?->purpose ?? 'Unknown or Not Updated';
                break;
            default:
                # code...
                break;
        }

        $data = [
            'emdType' => $instrumentType[$emd->instrument_type],
            'purpose' => $purpose,
            'assignee' => $AssigneeName,
            'tenderNo' => $tender->tender_no,
            'projectName' => $tender->tender_name,
            'status' => $tender->statuses->name ?? '',
            'link' => route('emds-dashboard.index'),
            'cooName' => $cooName,
        ];

        Log::error("Lost Tender EMD Update mail Data: $data");
        
        MailHelper::configureMailer($cooMail, $cooPass, $cooName);
        $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
        try {
            $mail = Mail::mailer($mailer)->to($assignee->email)
                ->cc([$cooMail, $adminMail])
                ->send(new TenderStatusUpdateMail($data));
            if ($mail) {
                Log::info("Tender Status Updated Email sent successfully");
            } else {
                Log::error("Tender Status Updated Email failed to send");
            }
        } catch (\Throwable $th) {
            Log::error("Tender Status Updated Email failed to send: " . $th->getMessage());
        }
    }
}
