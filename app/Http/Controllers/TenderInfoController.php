<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Emds;
use App\Models\Item;
use App\Models\User;
use App\Models\Status;
use App\Models\VendorOrg;
use App\Mail\TlApproval;
use App\Models\Location;
use App\Models\Websites;
use App\Models\TenderDoc;
use App\Models\WorkOrder;
use App\Mail\TenderUpdate;
use App\Models\TenderInfo;
use App\Models\TenderItem;
use App\Helpers\MailHelper;
use App\Mail\TenderCreated;
use App\Models\EligibleDoc;
use App\Mail\TenderRejected;
use App\Models\Organization;
use App\Models\WorkEligible;
use Illuminate\Http\Request;
use App\Mail\TenderinfoFilled;
use App\Models\Clintdirectory;
use App\Services\TimerService;
use App\Models\TenderInformation;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;
use App\Mail\TenderStatusUpdateMail;
use App\Models\TenderClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

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
        5 => 'Not Applicable',
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
        $commonEagerLoad = ['organizations', 'users', 'itemName', 'statuses'];
        $prepTenders = TenderInfo::with($commonEagerLoad)
            ->where('deleteStatus', '0')
            ->whereIn('status', ['1', '2', '3', '4', '5', '6', '7', '29', '30'])
            ->orderBy('due_date', 'DESC')->get();

        $dnbTenders = TenderInfo::with($commonEagerLoad)
            ->where('deleteStatus', '0')
            ->whereIn('status', ['8', '9', '10', '11', '12', '13', '14', '15', '16', '31', '32'])
            ->orderBy('due_date', 'DESC')->get();

        $tbTenders = TenderInfo::with($commonEagerLoad)
            ->where('deleteStatus', '0')
            ->whereIn('status', ['17', '19', '20', '23'])
            ->orderBy('due_date', 'DESC')->get();

        $tlTenders = TenderInfo::with($commonEagerLoad)
            ->where('deleteStatus', '0')
            ->whereIn('status', ['18', '21', '22', '24'])
            ->orderBy('due_date', 'DESC')->get();

        $twTenders = TenderInfo::with($commonEagerLoad)
            ->where('deleteStatus', '0')
            ->whereIn('status', ['25', '26', '27', '28'])
            ->orderBy('due_date', 'DESC')->get();


        return view('tender.index', compact('prepTenders', 'dnbTenders', 'tbTenders', 'twTenders', 'tlTenders', 'permissions'));
    }

    public function create()
    {
        $users = User::all()->where('role', '!=', 'admin')->where('status', 1);
        $statuses = Status::all();
        $items = Item::all();
        $organisations = Organization::all();
        $locations = Location::all();
        $websites = Websites::all();
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
        $items = Item::all();
        $statuses = Status::all();
        $organisations = Organization::all();
        $users = User::all()->where('role', '!=', 'admin')->where('status', 1);
        $locations = Location::all();
        $websites = Websites::all();
        $teams = $this->teams;
        return view('tender.edit', compact('tenderInfo', 'users', 'statuses', 'organisations', 'items', 'locations', 'websites', 'teams'));
    }

    public function update(Request $request, TenderInfo $tenderInfo, $id)
    {
        $request->validate([
            'tender_no' => 'nullable|string|max:50',
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

    public function infoUpdate11(Request $request, TenderInfo $tenderInfo, $id)
    {
        // dd($request->all());
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

        try {
            $pt = TenderInformation::where('tender_id', $id)->first();
            if (isset($pt)) {
                Log::info("Tender Info Update: found existing tender info record");
                if ($request->has('tecv')) {
                    $order1 = $request->tecv['order1'];
                    $order2 = $request->tecv['order2'];
                    $order3 = $request->tecv['order3'];
                }
                Log::info("TECV: " . json_encode($request->tecv));
                if ($request->tender_fee) {
                    $tender_fees = implode(',', $request->tender_fee);
                } else {
                    $tender_fees = $pt->tender_fees;
                }
                if ($request->emd_opt) {
                    $emd_opt = implode(',', $request->emd_opt);
                } else {
                    $emd_opt = $pt->emd_opt;
                }

                $pt->update([
                    'is_rejectable' => $request->is_rejectable,
                    'reject_reason' => $request->reject_reason,
                    'reject_remarks' => $request->reject_remarks,
                    'tender_fees' => $tender_fees,
                    'emd_req' => $request->emd_req,
                    'emd_opt' => $emd_opt,
                    'rev_auction' => $request->rev_auction,
                    'pt_supply' => $request->pt_supply,
                    'pt_ic' => $request->pt_ic,
                    'pbg' => $request->pbg,
                    'pbg_duration' => $request->pbg_duration,
                    'bid_valid' => $request->bid_valid,
                    'comm_eval' => $request->comm_eval,
                    'maf_req' => $request->maf_req,
                    'supply' => $request->supply,
                    'installation' => $request->installation,
                    'ldperweek' => $request->ldperweek,
                    'maxld' => $request->maxld,
                    'phyDocs' => $request->phyDocs,
                    'dead_date' => $request->dead_date,
                    'dead_time' => $request->dead_time,
                    'tech_eligible' => $request->tech_eligible,
                    'order1' => $order1,
                    'order2' => $order2,
                    'order3' => $order3,
                    'aat' => $request->aat,
                    'aat_amt' => $request->aat_amt,
                    'wc' => $request->wc,
                    'wc_amt' => $request->wc_amt,
                    'sc' => $request->sc,
                    'sc_amt' => $request->sc_amt,
                    'nw' => $request->nw,
                    'nw_amt' => $request->nw_amt,
                    'te_remark' => $request->te_remark,
                ]);
                // dd($tender_fees);
                $tender = TenderInfo::where('id', $id)->first();
                $tender->client_name = $request->client_name;
                $tender->client_designation = $request->client_designation;
                $tender->client_email = $request->client_email;
                $tender->client_mobile = $request->client_mobile;
                $tender->client_organisation = $request->client_organisation;
                $tender->courier_address = $request->courier_address;
                if ($request->reject_reason) {
                    $tender->status = $request->reject_reason;
                } else {
                    $tender->status = 2;
                }
                $tender->save();

                if ($request->has('wo')) {
                    WorkOrder::where('info_id', $pt->id)->delete();
                    foreach ($request->wo as $key => $value) {
                        WorkOrder::create([
                            'tender_id' => $id,
                            'info_id' => $pt->id,
                            'wo_name' => $value['wo_name'],
                        ]);
                    }
                }
                if ($request->has('docs')) {
                    EligibleDoc::where('info_id', $pt->id)->delete();
                    foreach ($request->docs as $key => $value) {
                        EligibleDoc::create([
                            'tender_id' => $id,
                            'info_id' => $pt->id,
                            'doc_name' => $value['doc_name'],
                        ]);
                    }
                }
                // dd(EligibleDoc::all());
            } else {
                Log::info("Tender Info Update: no existing tender info record found, creating new one");
                $pt = new TenderInformation();

                $tender = TenderInfo::where('id', $id)->first();
                $tender->client_name = $request->client_name;
                $tender->client_designation = $request->client_designation;
                $tender->client_email = $request->client_email;
                $tender->client_mobile = $request->client_mobile;
                $tender->client_organisation = $request->client_organisation;
                $tender->courier_address = $request->courier_address;
                if ($request->reject_reason) {
                    $tender->status = $request->reject_reason;
                } else {
                    $tender->status = 2;
                }
                $tender->save();

                $pt->tender_id = $id;
                $pt->is_rejectable = $request->is_rejectable;
                $pt->reject_reason = $request->reject_reason;
                $pt->tender->status = $request->reject_reason ? $request->reject_reason : $pt->tender->status;
                $pt->reject_remarks = $request->reject_remarks;

                $tender_fees = $request->tender_fee ? implode(',', $request->tender_fee) : null;

                if ($request->emd_opt) {
                    $emd_opt = implode(',', $request->emd_opt);
                }

                $pt->tender_id = $id;
                $pt->tender_fees = $tender_fees;
                $pt->emd_req = $request->emd_req;
                $pt->emd_opt = $emd_opt;
                $pt->rev_auction = $request->rev_auction;
                $pt->pt_supply = $request->pt_supply;
                $pt->pt_ic = $request->pt_ic;
                $pt->pbg = $request->pbg;
                $pt->pbg_duration = $request->pbg_duration;
                $pt->bid_valid = $request->bid_valid;
                $pt->comm_eval = $request->comm_eval;
                $pt->maf_req = $request->maf_req;
                $pt->supply = $request->supply;
                $pt->installation = $request->installation;
                $pt->ldperweek = $request->ldperweek;
                $pt->maxld = $request->maxld;
                $pt->phyDocs = $request->phyDocs;
                $pt->dead_date = $request->dead_date;
                $pt->dead_time = $request->dead_time;
                $pt->tech_eligible = $request->tech_eligible;
                $pt->order1 = $request->order1;
                $pt->order2 = $request->order2;
                $pt->order3 = $request->order3;
                $pt->aat = $request->aat;
                $pt->aat_amt = $request->aat_amt;
                $pt->wc = $request->wc;
                $pt->wc_amt = $request->wc_amt;
                $pt->sc = $request->sc;
                $pt->sc_amt = $request->sc_amt;
                $pt->nw = $request->nw;
                $pt->nw_amt = $request->nw_amt;
                $pt->te_remark = $request->te_remark;

                $pt->save();

                // Stop 'tender_info_sheet' timer and start 'tl_approval' timer
                $this->timerService->stopTimer($tender, 'tender_info_sheet');
                $this->timerService->startTimer($tender, 'tender_approval', 24);

                if ($request->has('wo')) {
                    foreach ($request->wo as $key => $value) {
                        WorkOrder::create([
                            'tender_id' => $id,
                            'info_id' => $pt->id,
                            'wo_name' => $value['wo_name'] ?? 'NA',
                        ]);
                    }
                }

                if ($request->has('doc')) {
                    foreach ($request->doc as $key => $value) {
                        EligibleDoc::create([
                            'tender_id' => $id,
                            'info_id' => $pt->id,
                            'doc_name' => $value['doc_name'],
                        ]);
                    }
                }

                if ($request->has('we')) {
                    foreach ($request->we as $key => $value) {
                        WorkEligible::create([
                            'tender_id' => $id,
                            'info_id' => $pt->id,
                            'worktype' => $value['worktype'],
                            'value' => $value['value'],
                            'availablity' => $value['availablity'],
                        ]);
                    }
                }
            }

            // Check if client with same email or phone exists
            $existingClient = Clintdirectory::where('email', $request->client_email)
                ->orWhere('phone_no', $request->client_mobile)
                ->first();

            if ($existingClient) {
                // Update existing client record
                $existingClient->name = $request->client_name;
                $existingClient->designation = $request->client_designation;
                $existingClient->organization = $request->client_organisation;
                $existingClient->courier_addr = $request->courier_address;
                $existingClient->ip = $_SERVER['REMOTE_ADDR'];
                $existingClient->strtotime = Carbon::now('Asia/Kolkata')->timestamp;
                $existingClient->save();
            } else {
                // Create new client record
                $clintdata = new Clintdirectory();
                $clintdata->name = $request->client_name;
                $clintdata->designation = $request->client_designation;
                $clintdata->email = $request->client_email;
                $clintdata->phone_no = $request->client_mobile;
                $clintdata->organization = $request->client_organisation;
                $clintdata->courier_addr = $request->courier_address;
                $clintdata->ip = $_SERVER['REMOTE_ADDR'];
                $clintdata->strtotime = Carbon::now('Asia/Kolkata')->timestamp;
                $clintdata->save();
            }

            // last inserted id
            $last_id = $id;
            $tenderInfo = $pt->tender;

            // if ($request->is_rejectable == 1) {
            //     if ($this->sendMailRej($tenderInfo, $last_id)) {
            //         return redirect()->route('tender.index')->with('success', 'Tender Info updated and Mail Sent successfully');
            //     } else {
            //         return redirect()->route('tender.index')->with('success', 'Tender Info updated successfully');
            //     }
            // } else {
            // }

            if ($this->sendMailAcc($tenderInfo, $last_id)) {
                return redirect()->route('tender.index')->with('success', 'Tender Info updated and Mail Sent successfully');
            } else {
                return redirect()->route('tender.index')->with('success', 'Tender Info updated successfully');
            }

            // return redirect()->route('tender.index')->with('success', 'Tender Info updated successfully');
        } catch (\Throwable $th) {
            Log::error("Tender Info Update Error: " . $th->getMessage());
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
        return view('tender.tender-approval-form', compact('tenderInfo', 'tenderFees', 'emdOpt'));
    }

    public function tlapproval(Request $request)
    {
        $tenderInfo = TenderInformation::with('tender')->get()->sortByDesc(fn($info) => optional($info->tender)->due_date);

        $approved = $tenderInfo->filter(fn($info) => optional($info->tender)->tlStatus == 1);
        $rejected = $tenderInfo->filter(fn($info) => optional($info->tender)->tlStatus == 2);
        $pending  = $tenderInfo->filter(fn($info) => in_array(optional($info->tender)->tlStatus, [0, 3]));

        return view('tender.tlapprove', compact('pending', 'approved', 'rejected'));
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
                if (array_filter($request->wo, function ($wo) {
                    return $wo['wo_name'] != null;
                })) {
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
                if (array_filter($request->docs, function ($doc) {
                    return $doc['doc_name'] != null;
                })) {
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
            $tender->rfq_to = $request->status == 1 ? implode(',', $request->rfq_to) : '0';
            $tender->save();
            Log::info('TenderInfo updated', $tender->toArray());

            // Stop 'tender_approval' timer if tlStatus is 1 and start 'rfq', 'physical_docs', 'emd_request' timer
            $this->timerService->stopTimer($tender, 'tender_approval');
            if ($tender->tlStatus == 1 || $tender->tlStatus == 2) {
                if ($tender->rfq_to != '0') {
                    $this->timerService->startTimer($tender, 'rfq');
                }
                if ($tender->emd > 0) {
                    $this->timerService->startTimer($tender, 'emd_request');
                }
                if ($info->phyDocs == 'Yes') {
                    $this->timerService->startTimer($tender, 'physical_docs');
                }

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
            $adminMail = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->first()->email ?? 'gyanprakashk55@gmail.com';
            $coo = User::where('role', 'coordinator')->first();
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
                Log::info("Tender Created Email sent successfully using " . json_encode($mailer));
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
            $adminMail = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->first()->email ?? 'gyanprakashk55@gmail.com';
            $cooMail = User::where('role', 'coordinator')->first()->email ?? 'gyanprakashk55@gmail.com';
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
            $adminMail = User::where('role', 'admin')->value('email') ?? 'gyanprakashk55@gmail.com';
            $cooMail = User::where('role', 'coordinator')->value('email') ?? 'gyanprakashk55@gmail.com';
            $tl = User::where('role', 'team-leader')->first();
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
            $adminMail = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->first()->email ?? 'gyanprakashk55@gmail.com';
            $coo = User::where('role', 'coordinator')->first();
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
                $purpose = $emd->emdDemandDrafts->dd_purpose ?? '';
                break;
            case '2':
                $purpose = 'FDR';
                break;
            case '3':
                $purpose = $emd->emdDemandDrafts->dd_purpose ?? '';
                break;
            case '4':
                $purpose = 'BG';
                break;
            case '5':
                $purpose = $emd->emdBankTransfers->purpose ?? '';
                break;
            case '6':
                $purpose = $emd->emdPayOnPortals->purpose ?? '';
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
