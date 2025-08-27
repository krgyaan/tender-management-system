<?php

namespace App\Http\Controllers;

use App\Models\Rfq;
use App\Models\Item;
use App\Models\User;
use App\Mail\RfqSent;
use App\Models\RfqBoq;
use App\Models\RfqMaf;
use App\Models\RfqMii;
use App\Models\Vendor;
use App\Models\RfqItem;
use App\Models\RfqScope;
use App\Models\RfqVendor;
use App\Models\VendorOrg;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use App\Models\RfqResponse;
use App\Models\RfqTechnical;
use Illuminate\Http\Request;
use App\Services\TimerService;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;


class RFQController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function index()
    {
        return view('rfq.index');
    }

    public function rfqData(Request $request,  $type)
    {
        $user = Auth::user();
        $team = $request->input('team');

        $query = TenderInfo::with(['rfqs', 'itemName', 'users'])
            ->where('deleteStatus', '0')
            ->whereNotIn('status', ['8', '9', '10', '11', '12', '13', '14', '15', '38', '39'])
            ->whereNot('rfq_to', '0')
            ->where('tlStatus', '1');

        if (!in_array($user->role, ['admin'])) {
            if (in_array($user->role, ['team-leader', 'coordinator'])) {
                $query->where('team', $user->team);
            } else {
                $query->where('team_member', $user->id);
            }
        } else if ($team) {
            $query->where('team', $team);
        }

        // Filter by RFQ status
        if ($type === 'pending') {
            $query->whereDoesntHave('rfqs');
        } elseif ($type === 'sent') {
            $query->whereHas('rfqs');
        }

        // Order by due_date
        if (!$request->filled('order')) {
            $query->orderByDesc('due_date');
        }

        // Global search
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('tender_name', 'like', "%{$search}%")
                    ->orWhere('tender_no', 'like', "%{$search}%")
                    ->orWhere('due_date', 'like', "%{$search}%")
                    ->orWhereHas('users', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return DataTables::of($query)
            ->addColumn('tender_name', function ($tender) {
                return "<strong>{$tender->tender_name}</strong> <br>
            <span class='text-muted'>{$tender->tender_no}</span>";
            })
            ->addColumn('users.name', function ($tender) {
                return optional($tender->users)->name ?? 'N/A';
            })
            ->addColumn('item_name', function ($tender) {
                return optional($tender->itemName)->name ?? '-';
            })
            ->addColumn('rfq_to', function ($tender) {
                if (!empty($tender->rfq_to)) {
                    $vendorNames = [];
                    foreach (explode(',', $tender->rfq_to) as $vendorId) {
                        $vendor = VendorOrg::find($vendorId);
                        if ($vendor) {
                            $vendorNames[] = $vendor->name;
                        }
                    }
                    return implode('<br>', $vendorNames) ?: '-';
                }
                return '-';
            })
            ->addColumn('due_date', function ($tender) {
                return '<span class="d-none">' . strtotime($tender->due_date) . '</span>' .
                    date('d-m-Y', strtotime($tender->due_date)) . '<br>' .
                    (isset($tender->due_time) ? date('h:i A', strtotime($tender->due_time)) : '');
            })
            ->addColumn('timer', function ($tender) {
                // You can use a partial view or just a placeholder
                return view('partials.rfq-timer', ['tender' => $tender])->render();
            })
            ->addColumn('rfq_received_timer', function ($tender) {
                // You can use a partial view or just a placeholder
                return view('partials.rfq-received-timer', ['tender' => $tender])->render();
            })
            ->addColumn('action', function ($tender) {
                return view('partials.rfq-action', ['tender' => $tender])->render();
            })
            ->rawColumns(['tender_name', 'due_date', 'rfq_to', 'timer', 'rfq_received_timer', 'action'])
            ->make(true);
    }

    public function create($id = null)
    {
        $orgs = VendorOrg::all();
        $tender = TenderInfo::find($id);
        $allTenders = TenderInfo::where('deleteStatus', '0')->where('tlStatus', '1')->get();
        return view('rfq.create', compact('orgs', 'tender', 'allTenders'));
    }

    public function store(Request $request)
    {
        Log::info('Storing RFQ', ['rfq' => $request->all()]);
        try {
            $request->validate([
                'tender_id' => 'required',
                'organisation' => 'nullable|string',
                'location' => 'nullable|string',
                'item_name' => 'nullable|string',
                'techical' => 'nullable',
                'boq' => 'nullable',
                'scope' => 'nullable',
                'maf' => 'nullable',
                'mii' => 'nullable',
                'docs_list' => 'nullable|string',
                'due_date' => 'nullable|string',
                'req[*][item]' => 'nullable|string',
                'req[*][unit]' => 'nullable|string',
                'req[*][qty]' => 'nullable|string',
            ]);
            $rfq = new RFQ();
            $rfq->tender_id = $request->tender_id;
            $rfq->organisation = $request->organisation;
            $rfq->location = $request->location;
            $rfq->item_name = $request->item_name;
            $rfq->docs_list = $request->docs_list;
            $rfq->due_date = $request->due_date;
            $rfq->save();

            Log::info('RFQ created', ['rfq_id' => $rfq->id]);

            $attachments = [];
            // Save Technical Specifications documents
            if ($request->hasFile('techical') && count($request->techical) > 0) {
                Log::info('Saving technical documents');
                foreach ($request->file('techical') as $value) {
                    $file = explode('.', $value->getClientOriginalName())[0];
                    $fileName = rand() . str_replace(' ', '_', $file) . '.' . $value->getClientOriginalExtension();
                    $value->move(public_path('uploads/rfqdocs'), $fileName);
                    $rfq->technicals()->create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'name' => $file,
                        'file_path' => $fileName,
                    ]);
                    $attachments['technical'] = $fileName;
                }
                Log::info('Technical documents saved' . json_encode($attachments['technical']));
            }
            // Save BOQ documents
            if ($request->hasFile('boq') && count($request->boq) > 0) {
                Log::info('Saving BOQ documents');
                foreach ($request->file('boq') as $value) {
                    $file = explode('.', $value->getClientOriginalName())[0];
                    $fileName = rand() . str_replace(' ', '_', $file) . '.' . $value->getClientOriginalExtension();
                    $value->move(public_path('uploads/rfqdocs'), $fileName);
                    $rfq->boqs()->create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'name' => $file,
                        'file_path' => $fileName,
                    ]);
                    $attachments['boq'] = $fileName;
                }
                Log::info('BOQ documents saved' . json_encode($attachments['boq']));
            }
            // Save Scope documents
            if ($request->hasFile('scope') && count($request->scope) > 0) {
                Log::info('Saving Scope documents');
                foreach ($request->file('scope') as $value) {
                    $file = explode('.', $value->getClientOriginalName())[0];
                    $fileName = rand() . str_replace(' ', '_', $file) . '.' . $value->getClientOriginalExtension();
                    $value->move(public_path('uploads/rfqdocs'), $fileName);
                    $rfq->scopes()->create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'name' => $file,
                        'file_path' => $fileName,
                    ]);
                    $attachments['scope'] = $fileName;
                }
                Log::info('Scope documents saved' . json_encode($attachments['scope']));
            }
            // Save MAF documents
            if ($request->hasFile('maf') && count($request->maf) > 0) {
                Log::info('Saving MAF documents');
                foreach ($request->file('maf') as $value) {
                    $file = explode('.', $value->getClientOriginalName())[0];
                    $fileName = rand() . str_replace(' ', '_', $file) . '.' . $value->getClientOriginalExtension();
                    $value->move(public_path('uploads/rfqdocs'), $fileName);
                    $rfq->mafs()->create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'name' => $file,
                        'file_path' => $fileName,
                    ]);
                    $attachments['maf'] = $fileName;
                }
                Log::info('MAF documents saved' . json_encode($attachments['maf']));
            }
            // Save MII documents
            if ($request->hasFile('mii') && count($request->mii) > 0) {
                Log::info('Saving MII documents');
                foreach ($request->file('mii') as $value) {
                    $file = explode('.', $value->getClientOriginalName())[0];
                    $fileName = rand() . str_replace(' ', '_', $file) . '.' . $value->getClientOriginalExtension();
                    $value->move(public_path('uploads/rfqdocs'), $fileName);
                    $rfq->miis()->create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'name' => $file,
                        'file_path' => $fileName,
                    ]);
                    $attachments['mii'] = $fileName;
                }
                Log::info('MII documents saved' . json_encode($attachments['mii']));
            }
            // Save Requirements
            if ($request->has('req') && count($request->req) > 0) {
                Log::info('Saving Requirements' . json_encode($request->req));
                foreach ($request->req as $value) {
                    RfqItem::create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'requirement' => $value['item'],
                        'unit' => $value['unit'],
                        'qty' => $value['qty'],
                    ]);
                    Log::info('Requirement saved', [
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'requirement' => $value['item'],
                        'unit' => $value['unit'],
                        'qty' => $value['qty'],
                    ]);
                }
            }

            // Save Vendors
            if ($request->has('vendor') && count($request->vendor) > 0) {
                Log::info('Saving Vendors' . json_encode($request->vendor));
                foreach ($request->vendor as $vendorGroup) {
                    $org = $vendorGroup['org'];
                    foreach ($vendorGroup['vendors'] as $vendorId) {
                        $vendor = Vendor::find($vendorId);
                        if ($vendor) {
                            RfqVendor::create([
                                'rfq_id' => $rfq->id,
                                'tender_id' => $request->tender_id,
                                'org' => $org,
                                'vendor' => $vendorId,
                                'email' => $vendor->email,
                                'mobile' => $vendor->mobile,
                            ]);
                        }
                    }
                }
            }

            // Stop the 'rfq' timer
            $tender = TenderInfo::find($request->tender_id);
            $tender->status = 4;
            $tender->save();
            $this->timerService->stopTimer($tender, 'rfq');
            // Start a new timer for 'rfq_received'
            $this->timerService->startTimer($tender, 'rfq_received', 24);

            // Send mail
            if ($this->sendMail($rfq, $attachments, $request->vendor)) {
                return redirect()->route('rfq.index')->with('success', 'RFQ added successfully and email sent successfully');
            } else {
                return redirect()->route('rfq.index')->with('success', 'RFQ added successfully but email sending failed');
            }
        } catch (\Illuminate\Http\Exceptions\PostTooLargeException $exception) {
            return redirect()->back()->with('error', 'File size too large');
        } catch (\Exception $exception) {
            Log::error('error' . json_encode($exception->getMessage()));
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $rfq = RFQ::with('requirementss')->findOrFail($id);
            return view('rfq.show', compact('rfq'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            $rfq = RFQ::findOrFail($id);
            $vendors = Vendor::all();
            $tenders = TenderInfo::where('deleteStatus', '0')->get();
            return view('rfq.edit', compact('rfq', 'vendors', 'tenders'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function update(Request $request, string $id)
    {
        try {
            Log::info('Updating RFQ #' . $id);
            $request->validate([
                'tender_id' => 'required',
                'team_name' => 'required',
                'organisation' => 'nullable|string',
                'location' => 'nullable|string',
                'item_name' => 'nullable|string',
                'techical' => 'nullable',
                'boq' => 'nullable',
                'scope' => 'nullable',
                'maf' => 'nullable',
                'mii' => 'nullable',
                'docs_list' => 'nullable|string',
                'due_date' => 'nullable|string',
                'req[*][item]' => 'nullable|string',
                'req[*][unit]' => 'nullable|string',
                'req[*][qty]' => 'nullable|string',
                'vendor[*][name]' => 'nullable|string',
                'vendor[*][email]' => 'nullable|string',
                'vendor[*][mobile]' => 'nullable|string',
            ]);

            $attachments = [];

            $rfq = RFQ::find($id);
            $rfq->tender_id = $request->tender_id;
            $rfq->team_name = $request->team_name;
            $rfq->organisation = $request->organisation;
            $rfq->location = $request->location;
            $rfq->item_name = $request->item_name;
            $rfq->docs_list = $request->docs_list;
            $rfq->due_date = $request->due_date;
            $rfq->save();
            Log::info('RFQ updated');
            // last inserted id
            $rfqid = $rfq->id;
            // Save Technical Specifications documents
            if ($request->hasFile('techical') && count($request->techical) > 0) {
                foreach ($request->file('techical') as $value) {
                    Log::info('Saving technical document: ' . $value->getClientOriginalName());
                    $file = $request->file('techical');
                    $fileName = 'rfq-' . $request->tender_id . '-technical-' . rand() . '.' . $value->getClientOriginalName();
                    $value->move(public_path('uploads/rfqdocs'), $fileName);
                    $encryptedPath = time() . $fileName;
                    $rfq->technicals()->create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'name' => $fileName,
                        'file_path' => $encryptedPath,
                    ]);
                    $attachments[] = $fileName;
                }
            }

            // Save BOQ documents
            if ($request->hasFile('boq') && count($request->boq) > 0) {
                foreach ($request->file('boq') as $value) {
                    Log::info('Saving boq document: ' . $value->getClientOriginalName());
                    $file = $request->file('boq');
                    $fileName = 'rfq-' . $request->tender_id . '-boq-' . rand() . '.' . $value->getClientOriginalName();
                    $value->move(public_path('uploads/rfqdocs'), $fileName);
                    $encryptedPath = time() . '_' . $fileName;
                    $rfq->boqs()->create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'name' => $fileName,
                        'file_path' => $encryptedPath,
                    ]);
                    $attachments[] = $fileName;
                }
            }

            // Save Scope documents
            if ($request->hasFile('scope') && count($request->scope) > 0) {
                foreach ($request->file('scope') as $value) {
                    Log::info('Saving scope document: ' . $value->getClientOriginalName());
                    $file = $request->file('scope');
                    $fileName = 'rfq-' . $request->tender_id . '-scope-' . rand() . '.' . $value->getClientOriginalName();
                    $value->move(public_path('uploads/rfqdocs'), $fileName);
                    $encryptedPath = time() . $fileName;
                    $rfq->scopes()->create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'name' => $fileName,
                        'file_path' => $encryptedPath,
                    ]);
                    $attachments[] = $fileName;
                }
            }

            // Save MAF documents
            if ($request->hasFile('maf') && count($request->maf) > 0) {
                foreach ($request->file('maf') as $value) {
                    Log::info('Saving maf document: ' . $value->getClientOriginalName());
                    $file = $request->file('maf');
                    $fileName = 'rfq-' . $request->tender_id . '-maf-' . rand() . '.' . $value->getClientOriginalName();
                    $value->move(public_path('uploads/rfqdocs'), $fileName);
                    $encryptedPath = time() . $fileName;
                    $rfq->mafs()->create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'name' => $fileName,
                        'file_path' => $encryptedPath,
                    ]);
                    $attachments[] = $fileName;
                }
            }

            // Save MII documents
            if ($request->hasFile('mii') && count($request->mii) > 0) {
                foreach ($request->file('mii') as $value) {
                    Log::info('Saving mii document: ' . $value->getClientOriginalName());
                    $file = $request->file('mii');
                    $fileName = 'rfq-' . $request->tender_id . '-mii-' . rand() . '.' . $value->getClientOriginalName();
                    $value->move(public_path('uploads/rfqdocs'), $fileName);
                    $encryptedPath = time() . $fileName;
                    $rfq->miis()->create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'name' => $fileName,
                        'file_path' => $encryptedPath,
                    ]);
                    $attachments[] = $fileName;
                }
            }

            if ($request->has('vendor') && count($request->vendor) > 0) {
                $rfq->rfqVendors()->delete();
                foreach ($request->vendor as $value) {
                    Log::info('Saving vendor: ' . $value['name']);
                    RfqVendor::create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'vendor' => $value['name'],
                        'email' => $value['email'],
                        'mobile' => $value['mobile'],
                    ]);
                }
            }

            // Save Requirements
            if ($request->has('req') && count($request->req) > 0) {
                $rfq->rfqItems()->delete();
                foreach ($request->req as $value) {
                    Log::info('Saving requirement: ' . $value['item']);
                    RfqItem::create([
                        'rfq_id' => $rfq->id,
                        'tender_id' => $request->tender_id,
                        'requirement' => $value['item'],
                        'unit' => $value['unit'],
                        'qty' => $value['qty'],
                    ]);
                }
            }

            // $this->sendMail($rfqid, $attachments);
            return redirect()->route('rfq.index')->with('success', 'RFQ updated successfully');
        } catch (\Throwable $th) {
            Log::error('Error updating RFQ: ' . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function destroy(string $id)
    {
        try {
            $rfq = RFQ::find($id);
            $rfq->delete();
            return redirect()->route('rfq.index')->with('success', 'RFQ deleted successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function getTenderDetails(Request $request)
    {
        $tenDetl = TenderInfo::find($request->id);
        $data = [
            'name' => $tenDetl->tender_name,
            'organisation' => $tenDetl->organizations->name,
            'location' => $tenDetl->locations->address,
            'item' => $tenDetl->itemName->name,
        ];
        return response()->json($data);
    }
    public function deleteVendor(string $id)
    {
        try {
            $rfq = RfqVendor::findOrFail($id);
            $rfq->delete();
            return response()->json(['success' => 'Vendor deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function delTechical($id)
    {
        try {
            $t = RfqTechnical::findOrFail($id);
            // unlink the file from uploads/rfqdocs
            if (file_exists(public_path('uploads/rfqdocs') . '/' . $t->name)) {
                unlink(public_path('uploads/rfqdocs') . '/' . $t->name);
            }
            $t->delete();
            return response()->json(['success' => 'Document deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function delBoq($id)
    {
        try {
            $b = RfqBoq::findOrFail($id);
            // unlink the file from uploads/rfqdocs
            if (file_exists(public_path('uploads/rfqdocs') . '/' . $b->name)) {
                unlink(public_path('uploads/rfqdocs') . '/' . $b->name);
            }
            $b->delete();
            return response()->json(['success' => 'Document deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function delScope($id)
    {
        try {
            $s = RfqScope::findOrFail($id);
            // unlink the file from uploads/rfqdocs
            if (file_exists(public_path('uploads/rfqdocs') . '/' . $s->name)) {
                unlink(public_path('uploads/rfqdocs') . '/' . $s->name);
            }
            $s->delete();
            return response()->json(['success' => 'Document deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function delMaf($id)
    {
        try {
            $m = RfqMaf::findOrFail($id);
            // unlink the file from uploads/rfqdocs
            if (file_exists(public_path('uploads/rfqdocs') . '/' . $m->name)) {
                unlink(public_path('uploads/rfqdocs') . '/' . $m->name);
            }
            $m->delete();
            return response()->json(['success' => 'Document deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function delMii($id)
    {
        try {
            $m = RfqMii::findOrFail($id);
            // unlink the file from uploads/rfqdocs
            if (file_exists(public_path('uploads/rfqdocs') . '/' . $m->name)) {
                unlink(public_path('uploads/rfqdocs') . '/' . $m->name);
            }
            $m->delete();
            return response()->json(['success' => 'Document deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function rfqRecipients(Request $request, $id)
    {
        if (request()->isMethod('get')) {
            try {
                $rfq = RFQ::with('rfqItems')->findOrFail($id);
                $tenderItems = Item::where('status', '1')->get();
                $requirements = $rfq->rfqItems;
                Log::info('Showing RFQ Recipient Form', ['rfq_id' => $id]);

                return view('rfq.recipient', compact('rfq', 'tenderItems', 'requirements'));
            } catch (\Throwable $th) {
                Log::error('Error showing RFQ Recipient Form', ['rfq_id' => $id, 'error' => $th->getMessage()]);
                return redirect()->back()->with('error', $th->getMessage());
            }
        }

        if (request()->isMethod('post')) {
            try {
                $request->validate([
                    'receipt_datetime' => 'required|date',
                    'items' => 'required|array',
                    'items.*.item_id' => 'required|exists:items,id',
                    'items.*.description' => 'nullable|string',
                    'items.*.quantity' => 'required|numeric|min:0',
                    'items.*.unit' => 'required|string',
                    'items.*.unit_price' => 'required|numeric|min:0',
                    'items.*.amount' => 'required|numeric|min:0',
                    'gst_percentage' => 'required|numeric|between:0,100',
                    'gst_type' => 'required|in:inclusive,extra',
                    'delivery_time' => 'required|integer|min:1',
                    'freight_type' => 'required|in:inclusive,extra',
                    'quotation_document' => 'required|file',
                    'technical_documents' => 'nullable|file',
                    'maf_document' => 'nullable|file',
                    'mii_document' => 'nullable|file',
                ]);
                Log::info('Validating RFQ Recipient Form', ['rfq_id' => $id]);

                $rfqResponse = RfqResponse::create([
                    'rfq_id' => $id,
                    'receipt_datetime' => $request->receipt_datetime,
                    'gst_percentage' => $request->gst_percentage,
                    'gst_type' => $request->gst_type,
                    'delivery_time' => $request->delivery_time,
                    'freight_type' => $request->freight_type,
                ]);
                Log::info('Created RFQ Response', ['rfq_response_id' => $rfqResponse->id]);

                // Handle file uploads
                if ($request->hasFile('quotation_document')) {
                    $file = $request->file('quotation_document');
                    $originalName = explode('.', $file->getClientOriginalName())[0];
                    $fileName = rand() . str_replace(' ', '_', $originalName) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/rfqdocs'), $fileName);
                    $rfqResponse->update([
                        'quotation_document' => $fileName
                    ]);
                    Log::info('Uploaded Quotation Document', ['file_name' => $fileName]);
                }

                if ($request->hasFile('technical_documents')) {
                    $file = $request->file('technical_documents');
                    $originalName = explode('.', $file->getClientOriginalName())[0];
                    $fileName = rand() . str_replace(' ', '_', $originalName) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/rfqdocs'), $fileName);
                    $rfqResponse->update([
                        'technical_documents' => $fileName
                    ]);
                    Log::info('Uploaded Technical Documents', ['file_name' => $fileName]);
                }

                if ($request->hasFile('maf_document')) {
                    $file = $request->file('maf_document');
                    $originalName = explode('.', $file->getClientOriginalName())[0];
                    $fileName = rand() . str_replace(' ', '_', $originalName) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/rfqdocs'), $fileName);
                    $rfqResponse->update([
                        'maf_document' => $fileName
                    ]);
                    Log::info('Uploaded MAF Document', ['file_name' => $fileName]);
                }

                if ($request->hasFile('mii_document')) {
                    $file = $request->file('mii_document');
                    $originalName = explode('.', $file->getClientOriginalName())[0];
                    $fileName = rand() . str_replace(' ', '_', $originalName) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/rfqdocs'), $fileName);
                    $rfqResponse->update([
                        'mii_document' => $fileName
                    ]);
                    Log::info('Uploaded MII Document', ['file_name' => $fileName]);
                }

                // Store items
                foreach ($request->items as $item) {
                    $rfqResponse->items()->create([
                        'item_id' => $item['item_id'],
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'unit_price' => $item['unit_price'],
                        'amount' => $item['amount']
                    ]);
                    Log::info('Created RFQ Response Item', ['item_id' => $item['item_id']]);
                }

                // Stop the 'rfq_received' timer
                $tender = TenderInfo::find($rfqResponse->rfq->tender_id);
                $this->timerService->stopTimer($tender, 'rfq_received');
                Log::info('Stopped RFQ Received Timer', ['tender_id' => $tender->id]);

                return redirect()->route('rfq.index')->with('success', 'Quotation received successfully');
            } catch (\Throwable $th) {
                Log::error('Error storing RFQ Response', ['rfq_id' => $id, 'error' => $th->getMessage()]);
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }

    public function RFQReceipts()
    {
        try {
            $receipts = RfqResponse::all();
            return view('rfq.receipts', compact('receipts'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /* ====== SEND MAIL TO ALL VENDORS ====== */
    public function sendMail(Rfq $rfq, $files, $vendors)
    {
        try {
            $user = User::find($rfq->tender->team_member);
            $adminMail = User::where('role', 'admin')->where('team', $user->team)->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->where('team', $user->team)->first()->email ?? 'gyanprakashk55@gmail.com';
            MailHelper::configureMailer($user->email, $user->app_password, $user->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';

            // get keys of $files array and send to $data
            $keys = array_keys($files);

            $data = [
                'tender_name' => $rfq->tender->tender_name,
                'items' => $rfq->requirementss()->get()->map(function ($requirement) {
                    return [
                        'requirement' => $requirement->requirement,
                        'unit' => $requirement->unit,
                        'qty' => $requirement->qty,
                    ];
                }),
                'due_date' => date('d-m-Y h:i A', strtotime($rfq->due_date)),
                'list_of_docs' => $rfq->docs_list,
                'te_name' => $user->name,
                'te_mob' => $user->mobile,
                'te_mail' => $user->email,
                'keys' => $keys,
                'files' => $files,
            ];

            Log::info("RFQ Mail Data: " . json_encode($data));
            Log::info("RFQ Vendors: " . json_encode($vendors));

            foreach ($vendors as $vendorGroup) {
                $org = $vendorGroup['org'];
                $orgName = VendorOrg::find($org)->name;
                $vendorEmails = [];
                foreach ($vendorGroup['vendors'] as $vendorId) {
                    $ven = Vendor::find($vendorId);
                    if ($ven) {
                        $vendorEmails[] = $ven->email;
                    }
                }
                $data['org'] = $orgName;
                $mail = Mail::mailer($mailer)
                    ->to($vendorEmails)
                    ->cc([$adminMail, $tlMail])
                    ->send(new RfqSent($data));

                if ($mail) {
                    Log::info("RFQ Mail sent to: " . json_encode($vendorEmails));
                } else {
                    Log::error("RFQ Mail sending failed: " . json_encode($vendorEmails));
                }
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('RFQ Mail sending failed: ' . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
}
