<?php

namespace App\Http\Controllers;

use App\Models\Emds;
use App\Models\User;
use App\Models\EmdBg;
use App\Models\EmdFdr;
use App\Models\EmdCheque;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use App\Mail\BgCreatedMail;
use App\Mail\DdCreatedMail;
use App\Models\PayOnPortal;
use App\Mail\PopCreatedMail;
use App\Models\BankTransfer;
use Illuminate\Http\Request;
use App\Mail\BankTransferMail;
use App\Models\EmdDemandDraft;
use App\Services\TimerService;
use App\Mail\ChequeCreatedMail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Services\PdfGeneratorService;
use Illuminate\Support\Facades\Config;

class EmdsController extends Controller
{
    protected $timerService;
    protected $pdfGenerator;

    public function __construct(TimerService $timerService, PdfGeneratorService $pdfGenerator)
    {
        $this->timerService = $timerService;
        $this->pdfGenerator = $pdfGenerator;
    }

    public $instrumentType = [
        '1' => 'Demand Draft',
        '2' => 'FDR',
        '3' => 'Cheque',
        '4' => 'BG',
        '5' => 'Bank Transfer',
        '6' => 'Pay on Portal',
    ];

    public function index()
    {
        return view('emds.index');
    }

    public function emdData(Request $request, $type)
    {
        $user = Auth::user();
        $team = $request->input('team');

        $query = TenderInfo::with(['emds', 'users'])
            ->whereNotIn( 'status', ['8', '9', '10', '11', '12', '13', '14', '15', '38', '39'])
            ->where(function ($query) {
                $query->where('emd', '>', '0')
                    ->orWhere('tender_fees', '>', '0');
            })
            ->where('tlStatus', '1')
            ->where('deleteStatus', '0');

        // Team filtering
        if (!in_array($user->role, ['admin'])) {
            if (in_array($user->role, ['team-leader', 'coordinator'])) {
                $query->where('team', $user->team);
            } else {
                $query->where('team_member', $user->id);
            }
        } else if ($team) {
            $query->where('team', $team);
        }

        // Filter by EMD status
        if ($type === 'pending') {
            $query->whereDoesntHave('emds');
        } elseif ($type === 'sent') {
            $query->whereHas('emds');
        }

        // Order by due_date
        $query->orderByDesc('due_date');

        // Global search
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('tender_name', 'like', "%{$search}%")
                    ->orWhere('tender_no', 'like', "%{$search}%")
                    ->orWhere('gst_values', 'like', "%{$search}%")
                    ->orWhere('tender_fees', 'like', "%{$search}%")
                    ->orWhere('emd', 'like', "%{$search}%")
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
            ->addColumn('gst_values', function ($tender) {
                return format_inr($tender->gst_values) ?? '-';
            })
            ->addColumn('tender_emd', function ($tender) {
                return format_inr($tender->emd) ?? '-';
            })
            ->addColumn('tender_fees', function ($tender) {
                return format_inr($tender->tender_fees) ?? '-';
            })
            ->addColumn('users.name', function ($tender) {
                return optional($tender->users)->name ?? 'N/A';
            })
            ->addColumn('due_date', function ($tender) {
                return '<span class="d-none">' . strtotime($tender->due_date) . '</span>' .
                    date('d-m-Y', strtotime($tender->due_date)) . '<br>' .
                    (isset($tender->due_time) ? date('h:i A', strtotime($tender->due_time)) : '');
            })
            ->addColumn('timer', function ($tender) {
                return view('partials.emd-timer', ['tender' => $tender])->render();
            })
            ->addColumn('action', function ($tender) {
                return view('partials.emd-action', ['tender' => $tender])->render();
            })
            ->rawColumns(['tender_name', 'due_date', 'timer', 'action'])
            ->make(true);
    }

    public function create($id = null)
    {
        $instrumentType = $this->instrumentType;
        $tender = TenderInfo::where('tender_no', base64_decode($id))->first();
        $tenders = TenderInfo::where('emd', '>', '0')
            ->where('deleteStatus', '0')
            ->where('tlStatus', '1')
            ->get();
        return view('emds.create', compact('tender', 'instrumentType', 'tenders'));
    }

    public function postStep1(Request $request)
    {
        try {
            Log::info('EMD Start creating by ' . Auth::user()->name);
            $request->validate([
                'tender_id' => 'required',
                'tender_no' => 'required',
                'due_date' => 'nullable',
                'project_name' => 'required',
                'instrument_type' => 'required',
                'requested_by' => 'required',
            ]);
            $request->session()->put('emds', $request->only('tender_id', 'tender_no', 'due_date', 'project_name', 'instrument_type', 'requested_by'));
            $users = User::all()->where('status', '1');
            // Redirect to step 2 based on instrument type
            Log::info('Session data:', ['emds' => session('emds')]);
            return view('emds.create-two', [
                'emds' => session('emds'),
                'users' => $users,
                'instrument_type' => $request->instrument_type,
            ]);
        } catch (\Throwable $th) {
            Log::error('Error in postStep1: ' . $th->getMessage());
            return redirect()->route('emds.index')->with('error', $th->getMessage());
        }
    }

    public function store(Request $request)
    {
        Log::info('EMD Second Form opened');
        try {
            $step1Data = session('emds');
            if (!$step1Data) {
                return redirect()->route('emds.create')->with('error', 'Step 1 means Session data is missing or invalid.');
            }
            $finalData = array_merge($step1Data, $request->all());
            Log::info('EMD Start creating by ' . Auth::user()->name . ' for BI type ' . $finalData['instrument_type']);

            try {
                $emd = new Emds();
                $emd->tender_id = $finalData['tender_id'] ?? '00';
                $emd->tender_no = $finalData['tender_no'] ?? 'NA';
                $emd->due_date = $finalData['due_date'];
                $emd->type = $finalData['tender_id'] == '0' ? 'Other Than TMS' : 'TMS';
                $emd->project_name = $finalData['project_name'];
                $emd->instrument_type = $finalData['instrument_type'];
                $emd->requested_by = $finalData['requested_by'];
                $emd->save();
            } catch (\Throwable $e) {
                Log::error('Failed to save EMD: ' . $e->getMessage());
                return redirect()->route('emds.create')->with('error', 'Failed to save EMD');
            }

            if (!in_array($finalData['instrument_type'], [1, 2, 3, 4, 5, 6])) {
                return redirect()->route('emds.create')->with('error', 'Invalid Instrument Type');
            }

            if ($emd->tender_id != '00') {
                $tender = TenderInfo::where('tender_no', $emd->tender_no)->first();
                $statusUpdate = $tender->update(['status' => '5']);

                if (!$statusUpdate) {
                    Log::error("Failed to update tender status to EMD Requested for tender ID: {$tender->id}");
                } else {
                    Log::info("Tender status updated successfully to EMD Requested for tender ID: {$tender->id}");
                }
            }

            if ($emd->due_date) {
                $remainingHrs = (strtotime($emd->due_date) - time()) / 3600;
                $hrs = (int) floor($remainingHrs);
            } else {
                $hrs = 0;
            }

            if ($finalData['instrument_type'] == 1) {
                try {
                    // dd($request->all());
                    Log::info('DD start creating by ' . Auth::user()->name);
                    $request->validate([
                        'dd_favour' => 'required',
                        'dd_amt' => 'required|numeric',
                        'dd_payable' => 'required',
                        'dd_purpose' => 'required',
                        'dd_needs' => ($emd->tender_id == '00') ? 'nullable' : 'required',
                        'courier_add' => ($emd->tender_id == '00') ? 'nullable' : 'required',
                        'courier_deadline' => ($emd->tender_id == '00') ? 'nullable' : 'required',
                        'dd_date' => ($emd->tender_id == '00') ? 'required|date' : 'nullable|date',
                        'remarks' => ($emd->tender_id == '00') ? 'required' : 'nullable',
                    ]);

                    $draft = new EmdDemandDraft();
                    $draft->emd_id = $emd->id;
                    $draft->dd_favour = $request->dd_favour;
                    $draft->dd_amt = $request->dd_amt;
                    $draft->dd_payable = $request->dd_payable;
                    $draft->dd_needs = $request->dd_needs;
                    $draft->dd_purpose = $request->dd_purpose;
                    $draft->courier_add = $request->courier_add;
                    $draft->courier_deadline = $request->courier_deadline;
                    $draft->status = 'DD requested';
                    $draft->save();

                    $chq = new EmdCheque();
                    $chq->emd_id = $emd->id;
                    $chq->dd_id = $draft->id;
                    $chq->cheque_favour = $request->dd_favour;
                    $chq->cheque_amt = $request->dd_amt;
                    $chq->cheque_date = date('Y-m-d');
                    $chq->cheque_reason = 'DD';
                    $chq->cheque_needs = $request->dd_needs;
                    $chq->status = 'DD requested';
                    $chq->save();

                    Log::info("Demand Draft ($draft->id and Cheque ($chq->id) created.");

                    $request->session()->forget('emds');

                    // =Tender due date time - Expected Courier Delivery time
                    if ($finalData['tender_id'] != '0') {
                        $tender = TenderInfo::find($finalData['tender_id'])->first();
                        $dueDT = "$tender->due_date $tender->due_time";
                        $eCD = time() - $request->courier_deadline * 3600;
                        Log::info("$dueDT - $eCD = " . (strtotime($dueDT) - $eCD));
                        $remainingHrs = (strtotime($dueDT) - $eCD) / 3600;
                        $hrs = (int) floor($remainingHrs);
                    } else {
                        $hrs = 0;
                    }

                    $dd = [];
                    $dd['cheque_date'] = date('d-m-Y');
                    $dd['cheque_amt'] = $request->dd_amt;
                    $dd['cheque_favour'] = $request->dd_favour;
                    $pdfFiles = $this->pdfGenerator->generatePdfs('chqCret', $dd);

                    $this->timerService->startTimer($chq, 'cheque_ac_form', 3);
                    $this->timerService->startTimer($draft, 'dd_acc_form', 3);

                    if ($emd->tender_id == '0') {
                        // return redirect()->route('emds.index')->with('success', 'Demand Draft EMD other than TMS created successfully');
                        if ($this->ddMail($emd->id, $pdfFiles)) {
                            return redirect()->route('emds.index')->with('success', 'Demand Draft EMD other than TMS created and Mail sent successfully');
                        } else {
                            return redirect()->route('emds.index')->with('success', 'Demand Draft EMD other than TMS created but Mail not sent.');
                        }
                    } else {
                        // Stop 'emd_request' timer
                        $tender = TenderInfo::find($finalData['tender_id']);
                        if ($tender) {
                            $this->timerService->stopTimer($tender, 'emd_request');
                        }

                        if ($this->ddMail($emd->id, $pdfFiles)) {
                            return redirect()->route('emds.index')->with('success', 'Demand Draft EMD created and Mail sent successfully');
                        } else {
                            return redirect()->route('emds.index')->with('success', 'Demand Draft EMD created but Mail not sent.');
                        }
                    }
                } catch (\Throwable $th) {
                    Log::error('Exception in processing instrument type 1:', ['error' => $th->getMessage()]);
                    return redirect()->route('emds.create')->with('error', $th->getMessage());
                }
            } else if ($finalData['instrument_type'] == 2) {
                try {
                    Log::info('FDR Started by user ' . Auth::user()->name);
                    $request->validate([
                        'fdr_purpose' => 'required',
                        'fdr_favour' => 'required',
                        'fdr_amt' => 'required',
                        'fdr_expiry' => 'required',
                        'fdr_needs' => 'required',
                        'courier_add' => 'nullable',
                        'courier_deadline' => 'nullable',
                        'fdr_date' => ($emd->tender_id == '00') ? 'required|date' : 'nullable|date',
                    ]);

                    $fdr = new EmdFdr();
                    $fdr->emd_id = $emd->id;
                    $fdr->fdr_purpose = $request->fdr_purpose;
                    $fdr->fdr_favour = $request->fdr_favour;
                    $fdr->fdr_amt = $request->fdr_amt;
                    $fdr->fdr_expiry = $request->fdr_expiry;
                    $fdr->fdr_needs = $request->fdr_needs;
                    $fdr->courier_add = $request->courier_add;
                    $fdr->courier_deadline = $request->courier_deadline;
                    $fdr->fdr_date = $request->fdr_date;
                    $fdr->fdr_source = 'Direct';
                    $fdr->save();
                    $request->session()->forget('emds');

                    // $this->timerService->startTimer($fdr, 'fdr_ac_form', $hrs);

                    if ($emd->tender_id == '0') {
                        return redirect()->route('emds.index')->with('success', 'FDR EMD other than TMS created successfully');
                    } else {
                        // Stop 'emd_request' timer
                        $tender = TenderInfo::find($finalData['tender_id']);
                        $this->timerService->stopTimer($tender, 'emd_request');
                        return redirect()->route('emds.index')->with('success', 'FDR EMD created successfully');
                    }
                } catch (\Throwable $th) {
                    Log::error('Exception in processing instrument type 2:', ['error' => $th->getMessage()]);
                    return redirect()->route('emds.index')->with('error', $th->getMessage());
                }
            } else if ($finalData['instrument_type'] == 3) {
                try {
                    Log::info('Cheque Started by user ' . Auth::user()->name);
                    $request->validate([
                        'cheque_favour' => 'nullable|required_without_all:cheque_amt,cheque_date',
                        'cheque_amt' => 'nullable|required_without_all:cheque_favour,cheque_date',
                        'cheque_date' => 'nullable|required_without_all:cheque_favour,cheque_amt',
                        'cheque_needs' => 'nullable',
                        'cheque_reason' => 'nullable',
                        'cheque_bank' => 'nullable',
                    ]);
                    $cheque = new EmdCheque();
                    $cheque->emd_id = $emd->id;
                    $cheque->cheque_favour = $request->cheque_favour;
                    $cheque->cheque_amt = $request->cheque_amt;
                    $cheque->cheque_date = $request->cheque_date;
                    $cheque->cheque_needs = $request->cheque_needs;
                    $cheque->cheque_reason = $request->cheque_reason;
                    $cheque->cheque_bank = $request->cheque_bank;
                    $cheque->save();
                    $request->session()->forget('emds');

                    $pdfFiles = $this->pdfGenerator->generatePdfs('chqCret', $cheque->toArray());
                    $cheque->generated_pdfs = json_encode($pdfFiles);
                    $cheque->save();

                    $this->timerService->startTimer($cheque, 'cheque_ac_form', $request->cheque_needs);

                    if ($finalData['tender_id'] != '0') {
                        // Stop 'emd_request' timer
                        $tender = TenderInfo::find($finalData['tender_id']);
                        $this->timerService->stopTimer($tender, 'emd_request');

                        if ($this->chequeMail($emd->id, $pdfFiles)) {
                            return redirect()->route('emds.index')->with('success', 'Cheque EMD created and Mail sent successfully');
                        } else {
                            return redirect()->route('emds.index')->with('error', 'Cheque EMD created but Mail not sent.');
                        }
                    } else {
                        if ($this->chequeMail($emd->id, $pdfFiles)) {
                            return redirect()->route('emds.index')->with('success', 'Cheque EMD created and Mail sent successfully');
                        } else {
                            return redirect()->route('emds.index')->with('error', 'Cheque EMD created but Mail not sent.');
                        }
                        // return redirect()->route('emds.index')->with('success', 'Cheque EMD created successfully');
                    }
                } catch (\Throwable $th) {
                    Log::error('Exception in processing instrument type 3:', ['error' => $th->getMessage()]);
                    return redirect()->route('emds.index')->with('error', $th->getMessage());
                }
            } else if ($finalData['instrument_type'] == 4) {
                // dd($finalData);
                Log::info('BG Started by user ' . Auth::user()->name);
                try {
                    $va = $request->validate([
                        'bg_needs' => 'required',
                        'bg_purpose' => 'required',
                        'bg_favour' => 'required',
                        'bg_address' => 'required',
                        'bg_bank' => 'required',
                        'bg_expiry' => 'required|date',
                        'bg_claim' => 'required|date',
                        'bg_amt' => 'required|numeric',
                        'bg_stamp' => 'required|numeric',
                        'bg_client_user' => 'nullable|email',
                        'bg_client_cp' => 'nullable|email',
                        'bg_client_fin' => 'nullable|email',
                        'bg_bank_name' => 'required',
                        'bg_bank_acc' => 'required',
                        'bg_bank_ifsc' => 'required',
                        'bg_courier_addr' => 'required',
                        'courier_deadline' => 'required',
                        'bg_format_te' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
                        'bg_po' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
                    ]);

                    $bg = new EmdBg();
                    $bg->emd_id = $emd->id;
                    $bg->bg_needs = $request->bg_needs;
                    $bg->bg_purpose = $request->bg_purpose;
                    $bg->bg_bank = $request->bg_bank;
                    $bg->bg_favour = $request->bg_favour;
                    $bg->bg_address = $request->bg_address;
                    $bg->bg_expiry = $request->bg_expiry;
                    $bg->bg_claim = $request->bg_claim;
                    $bg->bg_amt = $request->bg_amt;
                    $bg->bg_stamp = $request->bg_stamp;
                    $bg->bg_client_user = $request->bg_client_user;
                    $bg->bg_client_cp = $request->bg_client_cp;
                    $bg->bg_client_fin = $request->bg_client_fin;
                    $bg->bg_bank_name = $request->bg_bank_name;
                    $bg->bg_bank_acc = $request->bg_bank_acc;
                    $bg->bg_bank_ifsc = $request->bg_bank_ifsc;
                    $bg->bg_courier_addr = $request->bg_courier_addr;
                    $bg->bg_courier_deadline = $request->courier_deadline;

                    // // Handle bg_format_te file upload
                    // if ($request->hasFile('bg_format_te')) {
                    //     $file = $request->file('bg_format_te');
                    //     $ext = $file->getClientOriginalExtension();
                    //     $filename = time() . '_te_' . '.' . $ext;
                    //     $file->move('uploads/emds/', $filename);
                    //     $bg->bg_format_te = $filename;
                    // }
                    Log::info('Request bg_format_te:', [$request->file('bg_format_te')]);
                    Log::info('Request bg_po:', [$request->file('bg_po')]);

                    try {
                        Log::info('bg_format_te file info', [$request->file('bg_format_te')]);
                        if ($request->hasFile('bg_format_te')) {
                            $file = $request->file('bg_format_te');
                            $filename = time() . '_bg_by_te_.' . $file->getClientOriginalExtension();
                            $file->move('uploads/emds/', $filename);
                            $bg->bg_format_te = $filename;
                            Log::info('bg_format_te updated', ['bg_id' => $bg->id]);
                        }
                    } catch (\Exception $e) {
                        Log::error("bg_format_te file upload failed: " . $e->getMessage());
                    }
                    try {
                        if ($request->hasFile('bg_po')) {
                            $file = $request->file('bg_po');
                            $filename = time() . '_po_.' . $file->getClientOriginalExtension();
                            $file->move('uploads/emds/', $filename);
                            $bg->bg_po = $filename;
                            Log::info('BG PO updated', ['bg_id' => $bg->id]);
                        }
                    } catch (\Exception $e) {
                        Log::error("BG PO file upload failed: " . $e->getMessage());
                    }

                    $bg->save();
                    Log::info('BG EMD created successfully ' . $bg);
                    // $request->session()->forget('emds');

                    $pdfData = [
                        'id' => $bg->id,
                        'bg_favour' => $bg->bg_favour,
                        'due_date' => $bg->emds->due_date ?? 'NA',
                        'tender_name' => $bg->emds->project_name,
                        'bg_amt' => $bg->bg_amt,
                        'bg_expiry' => $bg->bg_expiry,
                        'bg_claim' => $bg->bg_claim,
                        'bg_purpose' => $bg->bg_purpose,
                        'bg_bank' => $bg->bg_bank,
                        'bg_no' => $bg->bg_no,
                        'tender_no' => $bg->emds->tender_no ?? 'NA',
                        'bg_client_user' => $bg->bg_client_user,
                        'bg_client_cp' => $bg->bg_client_cp,
                        'bg_client_fin' => $bg->bg_client_fin,
                        'date' => $bg->date,
                        'bg_address' => $bg->bg_address,
                        'bg_favour_ifsc' => $bg->bg_bank_ifsc,
                    ];

                    $pdfFiles = $this->pdfGenerator->generatePdfs('bg', $pdfData);
                    $bg->generated_pdfs = json_encode($pdfFiles); // save as JSON array
                    $bg->save();
                    Log::info('Generated PDF files: ' . json_encode($pdfFiles));

                    // $this->timerService->startTimer($bg, 'bg_acc_form', $hrs);

                    // Stop 'emd_request' timer
                    if ($emd->tender_id == '0') {
                        Log::info('BG EMD other than TMS created successfully');
                        // return redirect()->route('emds.index')->with('success', 'BG EMD other than TMS created successfully');
                        if ($this->bgCreatedMail($bg->id, $pdfFiles)) {
                            return redirect()->route('emds.index')->with('success', 'BG EMD other than TMS created and Mail sent successfully');
                        } else {
                            return redirect()->route('emds.index')->with('success', 'BG EMD other than TMS created but Mail not sent.');
                        }
                    } else {
                        $tender = TenderInfo::find($finalData['tender_id']);
                        $this->timerService->stopTimer($tender, 'emd_request');

                        if ($this->bgCreatedMail($bg->id, $pdfFiles)) {
                            return redirect()->route('emds.index')->with('success', 'BG EMD created and Mail sent successfully');
                        } else {
                            return redirect()->route('emds.index')->with('success', 'BG EMD created but Mail not sent.');
                        }
                    }
                } catch (\Throwable $th) {
                    Log::error('Exception in processing instrument type 4:', ['error' => $th]);
                    return redirect()->route('emds.index')->with('error', $th->getMessage());
                }
            } else if ($finalData['instrument_type'] == 5) {
                try {
                    Log::info('Bank Transfer EMD creating by ' . Auth::user()->name);
                    $request->validate([
                        'purpose' => 'required',
                        'bt_acc' => 'required',
                        'bt_ifsc' => 'required',
                        'bt_acc_name' => 'required',
                        'bt_amount' => 'required',
                    ]);

                    $bt = new BankTransfer();
                    $bt->emd_id = $emd->id;
                    $bt->purpose = $request->purpose;
                    $bt->bt_acc_name = $request->bt_acc_name;
                    $bt->bt_acc = $request->bt_acc;
                    $bt->bt_ifsc = $request->bt_ifsc;
                    $bt->bt_amount = $request->bt_amount;
                    $bt->save();
                    $request->session()->forget('emds');

                    $this->timerService->startTimer($bt, 'bt_acc_form', $hrs);
                    if ($finalData['tender_id'] != '00') {
                        Log::error('Bank Transfer:' . json_encode($finalData));
                        // Stop 'emd_request' timer
                        $tender = TenderInfo::find($finalData['tender_id']);
                        if ($tender) {
                            $this->timerService->stopTimer($tender, 'emd_request');
                        }

                        if ($this->bankTransferMail($emd->id)) {
                            return redirect()->route('emds.index')->with('success', 'Bank Transfer EMD created and Mail sent successfully');
                        } else {
                            return redirect()->route('emds.index')->with('success', 'Bank Transfer EMD created but Mail not sent.');
                        }
                    } else {
                        Log::error('Bank Transfer Other Than Tender:' . json_encode($finalData));
                        if ($this->bankTransferMail($emd->id)) {
                            return redirect()->route('emds.index')->with('success', 'Bank Transfer EMD other than TMS created and Mail sent successfully');
                        } else {
                            return redirect()->route('emds.index')->with('success', 'Bank Transfer EMD other than TMS created but Mail not sent.');
                        }
                    }
                } catch (\Throwable $th) {
                    Log::error('Exception in processing instrument type 5:', ['error' => $th->getMessage()]);
                    return redirect()->route('emds.index')->with('error', $th->getMessage());
                }
            } else if ($finalData['instrument_type'] == 6) {
                try {
                    Log::info('Pay On Portal EMD creating by ' . Auth::user()->name);
                    $request->validate([
                        'purpose' => 'nullable',
                        'portal' => 'nullable',
                        'is_netbanking' => 'nullable',
                        'is_debit' => 'nullable',
                        'amount' => 'nullable',
                    ]);

                    $pop = new PayOnPortal();
                    $pop->emd_id = $emd->id;
                    $pop->purpose = $request->purpose;
                    $pop->portal = $request->portal;
                    $pop->is_netbanking = $request->is_netbanking;
                    $pop->is_debit = $request->is_debit;
                    $pop->amount = $request->amount;
                    $pop->save();
                    $request->session()->forget('emds');
                    $this->timerService->startTimer($pop, 'pop_acc_form', $hrs);

                    if ($finalData['tender_id'] != 00) {
                        // Stop 'emd_request' timer
                        $tender = TenderInfo::find($finalData['tender_id']);
                        if ($tender) {
                            $this->timerService->stopTimer($tender, 'emd_request');
                        }

                        if ($this->popMail($emd->id)) {
                            return redirect()->route('emds.index')->with('success', 'Pay on Portal EMD created and Mail sent successfully');
                        } else {
                            return redirect()->route('emds.index')->with('success', 'Pay on Portal EMD created but Mail not sent.');
                        }
                    } else {
                        if ($this->popMail($emd->id)) {
                            return redirect()->route('emds.index')->with('success', 'Pay on Portal EMD other than TMS created and Mail sent successfully');
                        } else {
                            return redirect()->route('emds.index')->with('success', 'Pay on Portal EMD other than TMS created but Mail not sent.');
                        }
                        // return redirect()->route('emds.index')->with('success', 'Pay on Portal EMD other than tms created successfully');
                    }
                } catch (\Throwable $th) {
                    Log::error('Exception in processing instrument type 6:', ['error' => $th->getMessage()]);
                    return redirect()->route('emds.index')->with('error', $th->getMessage());
                }
            } else {
                Log::error('Invalid Instrument Type:', ['instrument_type' => $finalData['instrument_type']]);
                return redirect()->route('emds.index')->with('error', 'Invalid Instrument Type');
            }
        } catch (\Throwable $th) {
            Log::error('Particular table error : BI Type- ' . $finalData['instrument_type'] . ' : ' . $th->getMessage());
            return redirect()->route('emds.index')->with('error', $th->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $emd = Emds::where('tender_id', $id)->first();
            return view('emds.show', compact('emd'));
        } catch (\Throwable $th) {
            return redirect()->route('emds.index')->with('error', $th->getMessage());
        }
    }

    public function edit(Emds $emds, $id)
    {
        $emd = Emds::where('id', $id)->first();
        if (!$emd) {
            return redirect()->back()->with("error", "EMD not found.");
        }
        $ins = $emd->instrument_type;
        Log::info(Auth::user()->name . " going to edit Emd type " . $this->instrumentType[$ins] . ' ==> ' . json_encode($emd));
        $allData = match ($emd->instrument_type) {
            '1' => EmdDemandDraft::where('emd_id', $emd->id)->first(),
            '2' => EmdFdr::where('emd_id', $emd->id)->first(),
            '3' => EmdCheque::where('emd_id', $emd->id)->first(),
            '4' => EmdBg::where('emd_id', $emd->id)->first(),
            '5' => BankTransfer::where('emd_id', $emd->id)->first(),
            '6' => PayOnPortal::where('emd_id', $emd->id)->first(),
            default => "The Instrument Type is not valid",
        };
        if ($emd->tender_id != 0) {
            $tender = $emd->tender;
        } else {
            $tender = null;
        }
        return view('emds.edit', compact('emd', 'allData', 'ins', 'tender'));
    }

    public function update(Request $request, $id)
    {
        Log::info('EMD update initiated by', ['user' => Auth::user()->name, 'emd_id' => $id, 'request_data' => $request->all()]);
        try {
            // Validate base EMD fields
            $request->validate([
                'tender_id' => 'required',
                'tender_no' => 'nullable',
                'due_date' => 'nullable',
                'project_name' => 'required',
                'instrument_type' => 'required',
                'requested_by' => 'required',
            ]);

            // Update EMD record
            $emd = Emds::findOrFail($id);
            $emd->tender_id = $request->tender_id;
            $emd->tender_no = $request->tender_no;
            $emd->due_date = $request->due_date;
            $emd->project_name = $request->project_name;
            $emd->instrument_type = $request->instrument_type;
            $emd->requested_by = $request->requested_by;
            $emd->save();
            Log::info('EMD record updated', ['emd_id' => $id]);

            // Handle specific instrument type updates
            switch ($request->instrument_type) {
                case '1': // Demand Draft
                    Log::info('Demand Draft update initiated', ['emd_id' => $id]);
                    $request->validate([
                        'dd_favour' => 'required',
                        'dd_amt' => 'required|numeric',
                        'dd_payable' => 'required',
                        'dd_purpose' => 'required',
                        'dd_needs' => ($emd->tender_id == '00') ? 'nullable' : 'required',
                        'courier_add' => ($emd->tender_id == '00') ? 'nullable' : 'required',
                        'courier_deadline' => ($emd->tender_id == '00') ? 'nullable' : 'required',
                        'dd_date' => ($emd->tender_id == '00') ? 'required|date' : 'nullable|date',
                        'remarks' => ($emd->tender_id == '00') ? 'required' : 'nullable',
                    ]);

                    $draft = EmdDemandDraft::where('emd_id', $id)->firstOrFail();
                    $draft->dd_favour = $request->dd_favour;
                    $draft->dd_amt = $request->dd_amt;
                    $draft->dd_payable = $request->dd_payable;
                    $draft->dd_needs = $request->dd_needs;
                    $draft->dd_purpose = $request->dd_purpose;
                    $draft->courier_add = $request->courier_add;
                    $draft->courier_deadline = $request->courier_deadline;
                    $draft->remarks = $request->remarks;
                    $draft->save();
                    Log::info('Demand Draft updated', ['emd_id' => $id]);

                    // Update associated cheque record
                    $cheque = EmdCheque::where('emd_id', $id)->first();
                    Log::info('Associated cheque found', ['emd_id' => $id]);
                    if ($cheque) {
                        $cheque->cheque_favour = $request->dd_favour;
                        $cheque->cheque_amt = $request->dd_amt;
                        $cheque->cheque_reason = $request->dd_purpose;
                        $cheque->cheque_needs = $request->dd_needs;
                        $cheque->save();
                        Log::info('Associated cheque updated', ['emd_id' => $id]);
                    }

                    break;

                case '2': // FDR
                    Log::info('FDR update initiated', ['emd_id' => $id]);
                    $request->validate([
                        'fdr_purpose' => 'required',
                        'fdr_favour' => 'required',
                        'fdr_amt' => 'required|numeric',
                        'fdr_expiry' => 'required|date',
                        'fdr_needs' => 'required',
                        'fdr_bank_name' => 'required',
                        'fdr_bank_acc' => 'required',
                        'fdr_bank_ifsc' => 'required',
                        'fdr_status' => 'nullable',
                        'fdr_rejection' => 'nullable',
                    ]);

                    $fdr = EmdFdr::where('emd_id', $id)->firstOrFail();
                    $fdr->fdr_purpose = $request->fdr_purpose;
                    $fdr->fdr_favour = $request->fdr_favour;
                    $fdr->fdr_amt = $request->fdr_amt;
                    $fdr->fdr_expiry = $request->fdr_expiry;
                    $fdr->fdr_needs = $request->fdr_needs;
                    $fdr->fdr_bank_name = $request->fdr_bank_name;
                    $fdr->fdr_bank_acc = $request->fdr_bank_acc;
                    $fdr->fdr_bank_ifsc = $request->fdr_bank_ifsc;
                    $fdr->fdr_status = $request->fdr_status;
                    $fdr->fdr_rejection = $request->fdr_rejection;
                    $fdr->save();
                    Log::info('FDR updated', ['emd_id' => $id]);

                    break;

                case '3': // Cheque
                    Log::info('Cheque update initiated', ['emd_id' => $id]);
                    $request->validate([
                        'cheque_favour' => 'required',
                        'cheque_amt' => 'required|numeric',
                        'cheque_date' => 'required|date',
                        'cheque_needs' => 'required',
                        'cheque_reason' => 'required',
                        'cheque_bank' => 'required',
                    ]);

                    $cheque = EmdCheque::where('emd_id', $id)->firstOrFail();
                    $cheque->cheque_favour = $request->cheque_favour;
                    $cheque->cheque_amt = $request->cheque_amt;
                    $cheque->cheque_date = $request->cheque_date;
                    $cheque->cheque_needs = $request->cheque_needs;
                    $cheque->cheque_reason = $request->cheque_reason;
                    $cheque->cheque_bank = $request->cheque_bank;
                    $cheque->save();
                    Log::info('Cheque updated', ['emd_id' => $id]);

                    break;

                case '4': // Bank Guarantee
                    Log::info('Bank Guarantee update initiated', ['emd_id' => $id]);
                    $request->validate([
                        'bg_needs' => 'nullable',
                        'bg_purpose' => 'nullable',
                        'bg_favour' => 'nullable',
                        'bg_address' => 'nullable',
                        'bg_expiry' => 'nullable|date',
                        'bg_claim' => 'nullable|date',
                        'bg_amt' => 'nullable|numeric',
                        'bg_bank' => 'nullable',
                        'bg_stamp' => 'nullable|numeric',
                        'bg_client_user' => 'nullable|email',
                        'bg_client_cp' => 'nullable|email',
                        'bg_client_fin' => 'nullable|email',
                        'bg_bank_name' => 'nullable',
                        'bg_bank_acc' => 'nullable',
                        'bg_bank_ifsc' => 'nullable',
                        'bg_courier_addr' => 'nullable',
                        'courier_deadline' => 'nullable',
                        'bg_no' => 'nullable|string',
                        'bg_date' => 'nullable|date',
                        'bg_soft_copy' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                        'sfms' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                        'fdr_per' => 'nullable|numeric|min:0',
                        'fdr_copy' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                        'fdr_amt' => 'nullable|numeric|min:0',
                        'fdr_no' => 'nullable|string',
                        'fdr_validity' => 'nullable|date',
                        'fdr_roi' => 'nullable|numeric|min:0',
                        'bg_charges' => 'nullable|numeric|min:0',
                        'sfms_charges' => 'nullable|numeric|min:0',
                        'stamp_charges' => 'nullable|numeric|min:0',
                        'other_charges' => 'nullable|numeric|min:0',
                        'remarks' => 'nullable|string',
                    ]);

                    $bg = EmdBg::where('emd_id', $id)->firstOrFail();
                    $bg->bg_needs = $request->bg_needs;
                    $bg->bg_purpose = $request->bg_purpose;
                    $bg->bg_bank = $request->bg_bank;
                    $bg->bg_favour = $request->bg_favour;
                    $bg->bg_address = $request->bg_address;
                    $bg->bg_expiry = $request->bg_expiry;
                    $bg->bg_claim = $request->bg_claim;
                    $bg->bg_amt = $request->bg_amt;
                    $bg->bg_stamp = $request->bg_stamp;
                    $bg->bg_client_user = $request->bg_client_user;
                    $bg->bg_client_cp = $request->bg_client_cp;
                    $bg->bg_client_fin = $request->bg_client_fin;
                    $bg->bg_bank_name = $request->bg_bank_name;
                    $bg->bg_bank_acc = $request->bg_bank_acc;
                    $bg->bg_bank_ifsc = $request->bg_bank_ifsc;
                    $bg->bg_courier_addr = $request->bg_courier_addr;
                    $bg->bg_courier_deadline = $request->courier_deadline;
                    $bg->bg_no = $request->bg_no;
                    $bg->bg_date = $request->bg_date;
                    $bg->fdr_per = $request->fdr_per;
                    $bg->fdr_amt = $request->fdr_amt;
                    $bg->fdr_no = $request->fdr_no;
                    $bg->fdr_validity = $request->fdr_validity;
                    $bg->fdr_roi = $request->fdr_roi;
                    $bg->bg_charges = $request->bg_charges;
                    $bg->sfms_charges = $request->sfms_charges;
                    $bg->stamp_charges = $request->stamp_charges;
                    $bg->other_charges = $request->other_charges;
                    $bg->remarks = $request->remarks;

                    // Handle file uploads if present
                    if ($request->hasFile('bg_format_te')) {
                        $file = $request->file('bg_format_te');
                        $filename = time() . '_te_.' . $file->getClientOriginalExtension();
                        $file->move('uploads/emds/', $filename);
                        $bg->bg_format_te = $filename;
                        Log::info('BG Format TE updated', ['emd_id' => $id]);
                    }

                    if ($request->hasFile('bg_po')) {
                        $file = $request->file('bg_po');
                        $filename = time() . '_po_.' . $file->getClientOriginalExtension();
                        $file->move('uploads/emds/', $filename);
                        $bg->bg_po = $filename;
                        Log::info('BG PO updated', ['emd_id' => $id]);
                    }
                    if ($request->hasFile('bg_soft_copy')) {
                        $file = $request->file('bg_soft_copy');
                        $filename = time() . '_soft_copy.' . $file->getClientOriginalExtension();
                        $file->move('uploads/emds/', $filename);
                        $bg->bg_soft_copy = $filename;
                        Log::info('BG Soft Copy updated', ['emd_id' => $id]);
                    }

                    if ($request->hasFile('sfms')) {
                        $file = $request->file('sfms');
                        $filename = time() . '_sfms.' . $file->getClientOriginalExtension();
                        $file->move('uploads/emds/', $filename);
                        $bg->sfms = $filename;
                        Log::info('SFMS file updated', ['emd_id' => $id]);
                    }

                    if ($request->hasFile('fdr_copy')) {
                        $file = $request->file('fdr_copy');
                        $filename = time() . '_fdr.' . $file->getClientOriginalExtension();
                        $file->move('uploads/emds/', $filename);
                        $bg->fdr_copy = $filename;
                        Log::info('FDR Copy updated', ['emd_id' => $id]);
                    }
                    $bg->save();
                    Log::info('Bank Guarantee updated', ['emd_id' => $id]);
                    break;

                case '5': // Bank Transfer
                    Log::info('Bank Transfer update initiated', ['emd_id' => $id]);
                    $request->validate([
                        'purpose' => 'required',
                        'bt_acc' => 'required',
                        'bt_ifsc' => 'required',
                        'bt_branch' => 'nullable',
                        'bt_acc_name' => 'required',
                        'bt_amount' => 'required|numeric',
                    ]);

                    $bt = BankTransfer::where('emd_id', $id)->firstOrFail();
                    $bt->purpose = $request->purpose;
                    $bt->bt_acc = $request->bt_acc;
                    $bt->bt_ifsc = $request->bt_ifsc;
                    $bt->bt_branch = $request->bt_branch;
                    $bt->bt_acc_name = $request->bt_acc_name;
                    $bt->bt_amount = $request->bt_amount;
                    $bt->save();
                    Log::info('Bank Transfer updated', ['emd_id' => $id]);

                    break;

                case '6': // Pay on Portal
                    Log::info('Pay on Portal update initiated', ['emd_id' => $id]);
                    $request->validate([
                        'purpose' => 'required',
                        'portal' => 'required',
                        'is_netbanking' => 'required',
                        'is_debit' => 'required',
                        'amount' => 'required|numeric',
                    ]);

                    $pop = PayOnPortal::where('emd_id', $id)->firstOrFail();
                    $pop->purpose = $request->purpose;
                    $pop->portal = $request->portal;
                    $pop->is_netbanking = $request->is_netbanking;
                    $pop->is_debit = $request->is_debit;
                    $pop->amount = $request->amount;
                    $pop->save();
                    Log::info('Pay on Portal updated', ['emd_id' => $id]);

                    break;

                default:
                    throw new \Exception('Invalid instrument type');
            }

            Log::info('EMD updated successfully', ['emd_id' => $id]);
            return redirect()->route('emds.index')->with('success', 'EMD updated successfully');
        } catch (\Throwable $th) {
            Log::error('EMD Update Error:', ['error' => $th->getMessage()]);
            return redirect()->route('emds.index')->with('error', $th->getMessage());
        }
    }

    public function BgOldEntry(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('emds.old-entries.bg');
        }

        if ($request->isMethod('post')) {
            Log::info('BG old entry initiated by ' . Auth::user()->name, ['data' => $request->all()]);
            try {
                $validated = $request->validate([
                    'tender_no' => 'required|string',
                    'project_name' => 'required|string',
                    'bg_purpose' => 'required|string',
                    'bg_favour' => 'required|string',
                    'bg_address' => 'required|string',
                    'bg_expiry' => 'required|date',
                    'bg_claim' => 'required|date',
                    'bg_amt' => 'required|numeric|min:0',
                    'bg_bank' => 'required|string',
                    'bg_stamp' => 'required|numeric|min:0',
                    'bg_po' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                    'bg_soft_copy' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                    'bg_client_user' => 'nullable|email',
                    'bg_client_cp' => 'nullable|email',
                    'bg_client_fin' => 'nullable|email',
                    'bg_bank_name' => 'nullable|string',
                    'bg_bank_acc' => 'nullable',
                    'bg_bank_ifsc' => 'nullable|string',
                    'bg_no' => 'required|string',
                    'bg_date' => 'required|date',
                    'sfms' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                    'fdr_per' => 'nullable|numeric',
                    'fdr_amt' => 'nullable|numeric',
                    'fdr_copy' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                    'fdr_no' => 'nullable|string',
                    'fdr_validity' => 'nullable|date',
                    'fdr_roi' => 'nullable|numeric',
                    'bg_charges' => 'nullable|numeric',
                    'sfms_charges' => 'nullable|numeric',
                    'stamp_charges' => 'nullable|numeric',
                    'other_charges' => 'nullable|numeric',
                    'remarks' => 'nullable|string'
                ]);

                // Create new EMD entry
                $emd = new Emds();
                $emd->tender_id = '00'; // For non-TMS entries
                $emd->due_date = null;
                $emd->type = 'Old Entries';
                $emd->tender_no = $validated['tender_no']; // For non-TMS entries
                $emd->project_name = $validated['project_name'];
                $emd->instrument_type = 4; // BG type
                $emd->requested_by = Auth::user()->name;
                $emd->save();

                // Handle file uploads
                $poFilename = null;
                if ($request->hasFile('bg_po')) {
                    $poFile = $request->file('bg_po');
                    $poFilename = time() . '_po.' . $poFile->getClientOriginalExtension();
                    $poFile->move('uploads/emds/', $poFilename);
                }

                $sfmsFilename = null;
                if ($request->hasFile('sfms')) {
                    $sfmsFile = $request->file('sfms');
                    $sfmsFilename = time() . '_sfms.' . $sfmsFile->getClientOriginalExtension();
                    $sfmsFile->move('uploads/emds/', $sfmsFilename);
                }

                $bgSoftCopyFilename = null;
                if ($request->hasFile('bg_soft_copy')) {
                    $bgSoftCopyFile = $request->file('bg_soft_copy');
                    $bgSoftCopyFilename = time() . '_soft_copy.' . $bgSoftCopyFile->getClientOriginalExtension();
                    $bgSoftCopyFile->move('uploads/emds/', $bgSoftCopyFilename);
                }

                $fdrFilename = null;
                if ($request->hasFile('fdr_copy')) {
                    $fdrFile = $request->file('fdr_copy');
                    $fdrFilename = time() . '_fdr.' . $fdrFile->getClientOriginalExtension();
                    $fdrFile->move('uploads/emds/', $fdrFilename);
                }

                // Create BG entry
                $bg = new EmdBg();
                $bg->emd_id = $emd->id;
                $bg->bg_purpose = $validated['bg_purpose'];
                $bg->bg_favour = $validated['bg_favour'];
                $bg->bg_address = $validated['bg_address'];
                $bg->bg_expiry = $validated['bg_expiry'];
                $bg->bg_claim = $validated['bg_claim'];
                $bg->bg_amt = $validated['bg_amt'];
                $bg->bg_bank = $validated['bg_bank'];
                $bg->bg_stamp = $validated['bg_stamp'];
                $bg->bg_po = $poFilename;
                $bg->bg_soft_copy = $bgSoftCopyFilename;
                $bg->bg_client_user = $validated['bg_client_user'];
                $bg->bg_client_cp = $validated['bg_client_cp'];
                $bg->bg_client_fin = $validated['bg_client_fin'];
                $bg->bg_bank_name = $validated['bg_bank_name'];
                $bg->bg_bank_acc = $validated['bg_bank_acc'];
                $bg->bg_bank_ifsc = $validated['bg_bank_ifsc'];
                $bg->bg_no = $validated['bg_no'];
                $bg->bg_date = $validated['bg_date'];
                $bg->sfms_conf = $sfmsFilename;
                $bg->fdr_copy = $fdrFilename;
                $bg->fdr_no = $validated['fdr_no'];
                $bg->fdr_per = $validated['fdr_per'];
                $bg->fdr_amt = $validated['fdr_amt'];
                $bg->fdr_validity = $validated['fdr_validity'];
                $bg->fdr_roi = $validated['fdr_roi'];
                $bg->bg_charge_deducted = $validated['bg_charges'];
                $bg->sfms_charge_deducted = $validated['sfms_charges'];
                $bg->stamp_charge_deducted = $validated['stamp_charges'];
                $bg->other_charge_deducted = $validated['other_charges'];
                $bg->bg2_remark = $validated['remarks'];
                $bg->save();

                $pdfData = [
                    'id' => $bg->id,
                    'bg_favour' => $bg->bg_favour,
                    'due_date' => $bg->emds->due_date ?? 'NA',
                    'tender_name' => $bg->emds->project_name,
                    'bg_amt' => $bg->bg_amt,
                    'bg_expiry' => $bg->bg_expiry,
                    'bg_claim' => $bg->bg_claim,
                    'bg_purpose' => $bg->bg_purpose,
                    'bg_bank' => $bg->bg_bank,
                    'bg_no' => $bg->bg_no,
                    'tender_no' => $bg->emds->tender_no ?? 'NA',
                    'bg_client_user' => $bg->bg_client_user,
                    'bg_client_cp' => $bg->bg_client_cp,
                    'bg_client_fin' => $bg->bg_client_fin,
                    'date' => $bg->date,
                    'bg_address' => $bg->bg_address,
                    'bg_favour_ifsc' => $bg->bg_bank_ifsc,
                ];

                $pdfFiles = $this->pdfGenerator->generatePdfs('bg', $pdfData);
                $bg->generated_pdfs = json_encode($pdfFiles); // save as JSON array
                $bg->save();
                Log::info('BG EMD Old Entries created successfully by ' . Auth::user()->name);
                if ($this->bgCreatedMail($bg->id, $pdfFiles)) {
                    return redirect()->route('emds.index')->with('success', 'BG EMD Old Entries created and Mail sent successfully');
                } else {
                    return redirect()->route('emds.index')->with('success', 'BG EMD Old Entries created but Mail not sent.');
                }

                Log::info('BG Old Entry created successfully', ['bg_id' => $bg->id]);
                return redirect()->intended(route('emds.index'))->with('success', 'Bank Guarantee old entry created successfully');
            } catch (\Throwable $th) {
                Log::error('BG Old Entry Error:', ['error' => $th->getMessage()]);
                return redirect()->route('emds.index')->with('error', $th->getMessage())->withInput();
            }
        }
    }

    public function DdOldEntry(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('emds.old-entries.dd');
        }

        if ($request->isMethod('post')) {
            try {
                // Validate request data
                $validated = $request->validate([
                    'project_name' => 'required|string',
                    'dd_favour' => 'required|string',
                    'dd_payable' => 'required|string',
                    'dd_date' => 'required|date',
                    'dd_amount' => 'required|numeric|min:0',
                    'dd_purpose' => 'required|string',
                    'remarks' => 'nullable|string'
                ]);

                // Create new EMD entry
                $emd = new Emds();
                $emd->tender_id = '00'; // For non-TMS entries
                $emd->project_name = $validated['project_name'];
                $emd->instrument_type = 1; // DD type
                $emd->requested_by = Auth::user()->name;
                $emd->save();

                // Create DD entry
                $dd = new EmdDemandDraft();
                $dd->emd_id = $emd->id;
                $dd->dd_favour = $validated['dd_favour'];
                $dd->dd_amt = $validated['dd_amount'];
                $dd->dd_payable = $validated['dd_payable'];
                $dd->dd_date = $validated['dd_date'];
                $dd->dd_purpose = $validated['dd_purpose'];
                $dd->remarks = $validated['remarks'];
                $dd->save();

                Log::info('DD Old Entry created successfully', [
                    'dd_id' => $dd->id,
                    'emd_id' => $emd->id
                ]);

                return redirect()->route('emds.index')
                    ->with('success', 'Demand Draft old entry created successfully');
            } catch (\Throwable $th) {
                Log::error('DD Old Entry Error:', ['error' => $th->getMessage()]);
                return redirect()->route('emds.index')
                    ->with('error', $th->getMessage())
                    ->withInput();
            }
        }
    }

    public function ChequeOTTEntry(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('emds.without-tender.cheque');
        }

        if ($request->isMethod('post')) {
            try {
                // Validate request data
                $validated = $request->validate([
                    'cheque_needed_in' => 'required',
                    'purpose' => 'required|string',
                    'cheque_favour' => 'required|string',
                    'cheque_date' => 'required|date',
                    'cheque_account' => 'required',
                    'amount' => 'required',
                ]);

                // Create new EMD entry
                $emd = new Emds();
                $emd->tender_id = '00';
                $emd->tender_no = 'Other Than Tender';
                $emd->due_date = null;
                $emd->type = 'Other Than Tender';
                $emd->project_name = 'Other Than Tender';
                $emd->instrument_type = 3;
                $emd->requested_by = Auth::user()->name;
                $emd->save();

                // Create DD entry
                $cheque = new EmdCheque();
                $cheque->emd_id = $emd->id;
                $cheque->cheque_favour = $request->cheque_favour;
                $cheque->cheque_amt = $request->amount;
                $cheque->cheque_date = $request->cheque_date;
                $cheque->cheque_needs = $request->cheque_needed_in;
                $cheque->cheque_reason = $request->purpose;
                $cheque->cheque_bank = $request->cheque_account;
                $cheque->save();

                $pdfFiles = $this->pdfGenerator->generatePdfs('chqCret', $cheque->toArray());
                $cheque->generated_pdfs = json_encode($pdfFiles);
                $cheque->save();
                Log::info('Cheque Old Entry created successfully', [
                    'cheque_id' => $cheque->id,
                    'emd_id' => $emd->id
                ]);

                if ($this->chequeMail($emd->id, $pdfFiles)) {
                    return redirect()->back()->with('success', 'Cheque other than tender created and mail sent successfully');
                } else {
                    return redirect()->back()->with('success', 'Cheque other than tender created but mail not sent.');
                }
            } catch (\Throwable $th) {
                Log::error('Cheque Old Entry Error:', ['error' => $th->getMessage()]);
                return redirect()->route('emds.index')->with('error', $th->getMessage())->withInput();
            }
        }
    }

    public function BgOTTEntry(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('emds.without-tender.bg');
        }
        if ($request->isMethod('post')) {
            Log::info('BG old entry initiated by ' . Auth::user()->name, ['data' => $request->all()]);
            try {
                $validated = $request->validate([
                    'tender_no' => 'required|string',
                    'project_name' => 'required|string',
                    'bg_needed_in' => 'required|string',
                    'bg_purpose' => 'required|string',
                    'bg_favour' => 'required|string',
                    'bg_address' => 'required|string',
                    'bg_expiry' => 'required|date',
                    'bg_claim' => 'required|date',
                    'bg_amt' => 'required|numeric|min:0',
                    'bg_bank' => 'required|string',
                    'bg_stamp' => 'required|numeric|min:0',
                    'bg_format_upload' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                    'bg_po' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                    'bg_client_user' => 'nullable|email',
                    'bg_client_cp' => 'nullable|email',
                    'bg_client_fin' => 'nullable|email',
                    'bg_bank_name' => 'nullable|string',
                    'bg_bank_acc' => 'nullable',
                    'bg_bank_ifsc' => 'nullable|string',
                    'bg_courier_addr' => 'nullable|string',
                    'bg_courier_deadline' => 'nullable|string',
                ]);

                // Create new EMD entry
                $emd = new Emds();
                $emd->tender_id = '00'; // For non-TMS entries
                $emd->due_date = null;
                $emd->type = 'Other Than Tender';
                $emd->tender_no = $validated['tender_no']; // For non-TMS entries
                $emd->project_name = $validated['project_name'];
                $emd->instrument_type = 4; // BG type
                $emd->requested_by = Auth::user()->name;
                $emd->save();

                // Handle file uploads
                $poFilename = null;
                if ($request->hasFile('bg_po')) {
                    $poFile = $request->file('bg_po');
                    $poFilename = time() . '_po.' . $poFile->getClientOriginalExtension();
                    $poFile->move('uploads/emds/', $poFilename);
                }

                $sfmsFilename = null;
                if ($request->hasFile('bg_format_upload')) {
                    $sfmsFile = $request->file('bg_format_upload');
                    $sfmsFilename = time() . '_bg_format_upload.' . $sfmsFile->getClientOriginalExtension();
                    $sfmsFile->move('uploads/emds/', $sfmsFilename);
                }

                // Create BG entry
                $bg = new EmdBg();
                $bg->emd_id = $emd->id;
                $bg->bg_needs = $validated['bg_needed_in'];
                $bg->bg_purpose = $validated['bg_purpose'];
                $bg->bg_favour = $validated['bg_favour'];
                $bg->bg_address = $validated['bg_address'];
                $bg->bg_expiry = $validated['bg_expiry'];
                $bg->bg_claim = $validated['bg_claim'];
                $bg->bg_amt = $validated['bg_amt'];
                $bg->bg_bank = $validated['bg_bank'];
                $bg->bg_stamp = $validated['bg_stamp'];
                $bg->bg_po = $poFilename;
                $bg->bg_format_te = $sfmsFilename;
                $bg->bg_client_user = $validated['bg_client_user'];
                $bg->bg_client_cp = $validated['bg_client_cp'];
                $bg->bg_client_fin = $validated['bg_client_fin'];
                $bg->bg_bank_name = $validated['bg_bank_name'];
                $bg->bg_bank_acc = $validated['bg_bank_acc'];
                $bg->bg_bank_ifsc = $validated['bg_bank_ifsc'];
                $bg->sfms_conf = $sfmsFilename;
                $bg->bg_courier_addr = $validated['bg_courier_addr'];
                $bg->bg_courier_deadline = $validated['bg_courier_deadline'];
                $bg->save();

                $pdfData = [
                    'id' => $bg->id,
                    'bg_favour' => $bg->bg_favour,
                    'due_date' => $bg->emds->due_date ?? 'NA',
                    'tender_name' => $bg->emds->project_name,
                    'bg_amt' => $bg->bg_amt,
                    'bg_expiry' => $bg->bg_expiry,
                    'bg_claim' => $bg->bg_claim,
                    'bg_purpose' => $bg->bg_purpose,
                    'bg_bank' => $bg->bg_bank,
                    'bg_no' => $bg->bg_no,
                    'tender_no' => $bg->emds->tender_no ?? 'NA',
                    'bg_client_user' => $bg->bg_client_user,
                    'bg_client_cp' => $bg->bg_client_cp,
                    'bg_client_fin' => $bg->bg_client_fin,
                    'date' => $bg->date,
                    'bg_address' => $bg->bg_address,
                    'bg_favour_ifsc' => $bg->bg_bank_ifsc,
                ];

                $pdfFiles = $this->pdfGenerator->generatePdfs('bg', $pdfData);
                $bg->generated_pdfs = json_encode($pdfFiles); // save as JSON array
                $bg->save();
                Log::info('BG EMD other than Tender created successfully by ' . Auth::user()->name);
                if ($this->bgCreatedMail($bg->id, $pdfFiles)) {
                    return redirect()->route('emds.index')->with('success', 'BG EMD other than Tender created and Mail sent successfully');
                } else {
                    return redirect()->route('emds.index')->with('success', 'BG EMD other than Tender created but Mail not sent.');
                }
            } catch (\Throwable $th) {
                Log::error('BG Old Entry Error:', ['error' => $th->getMessage()]);
                return redirect()->route('emds.index')->with('error', $th->getMessage())->withInput();
            }
        }
    }

    public function DdOTTEntry(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('emds.without-tender.dd');
        }
        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'project_name' => 'required',
                    'dd_needed_in' => 'required',
                    'purpose' => 'required',
                    'dd_favour' => 'required',
                    'dd_payable_at' => 'required',
                    'amount' => 'required',
                    'courier_address' => 'required',
                    'courier_delivery_time' => 'required',
                ]);

                $emd = new Emds();
                $emd->tender_id = '00';
                $emd->tender_no = 'Other Than Tender';
                $emd->due_date = null;
                $emd->type = 'Other Than Tender';
                $emd->project_name = $validated['project_name'];
                $emd->instrument_type = 1;
                $emd->requested_by = Auth::user()->name;
                $emd->save();

                $draft = new EmdDemandDraft();
                $draft->emd_id = $emd->id;
                $draft->dd_favour = $request->dd_favour;
                $draft->dd_amt = $request->amount;
                $draft->dd_payable = $request->dd_payable_at;
                $draft->dd_needs = $request->dd_needed_in;
                $draft->dd_purpose = $request->purpose;
                $draft->courier_add = $request->courier_address;
                $draft->courier_deadline = $request->courier_delivery_time;
                $draft->save();

                $chq = new EmdCheque();
                $chq->emd_id = $emd->id;
                $chq->dd_id = $draft->id;
                $chq->cheque_favour = $request->dd_favour;
                $chq->cheque_amt = $request->amount;
                $chq->cheque_date = date('Y-m-d');
                $chq->cheque_reason = $request->purpose;
                $chq->cheque_needs = $request->dd_needed_in;
                $chq->status = 'DD Created';
                $chq->save();

                Log::info("Demand Draft ($draft->id and Cheque ($chq->id) created.");
                Log::info('DD EMD other than Tender created successfully by ' . Auth::user()->name);
                $dd = [];
                $dd['cheque_date'] = date('d-m-Y');
                $dd['cheque_amt'] = $request->amount;
                $dd['cheque_favour'] = $request->dd_favour;
                $pdfFiles = $this->pdfGenerator->generatePdfs('chqCret', $dd);
                $chq->generated_pdfs = json_encode($pdfFiles); // save as JSON array
                $chq->save();
                if ($this->ddMail($emd->id, $pdfFiles)) {
                    return redirect()->route('emds.index')->with('success', 'DD EMD other than Tender created and Mail sent successfully');
                } else {
                    return redirect()->route('emds.index')->with('success', 'DD EMD other than Tender created but Mail not sent.');
                }
            } catch (\Throwable $th) {
                Log::error('DD Old Entry Error:', ['error' => $th->getMessage()]);
                return redirect()->route('emds.index')->with('error', $th->getMessage())->withInput();
            }
        }
    }

    /* ====== SEND MAIL ====== */
    public function bankTransferMail($emdId)
    {
        try {
            $tender = null;
            $emd = Emds::find($emdId);
            if ($emd->tender_id == '00') {
                $sender = Auth::user();
            } else {
                $tender = TenderInfo::find($emd->tender_id);
                if ($tender) {
                    $sender = User::find($tender->team_member) ?? Auth::user();
                }
            }
            $team = $sender->team ?? 'DC';
            $userRoles = User::where('team', $team)->whereIn('role', ['admin', 'coordinator', 'team-leader'])->pluck('email', 'role')->toArray();
            $adminMail = $userRoles['admin'] ?? 'gyanprakashk55@gmail.com';
            $cooMail = $userRoles['coordinator'] ?? 'gyanprakashk55@gmail.com';
            $tlMail = $userRoles['team-leader'] ?? 'gyanprakashk55@gmail.com';
            $tlName = User::where('team', $team)->where('role', 'team-leader')->value('name') ?? 'gyanprakashk';
            // $dueDate = date('d-m-Y', strtotime($tender->due_date));
            $bt = BankTransfer::where('emd_id', $emdId)->first();
            $data = [
                'id' => $bt->id,
                'purpose' => $bt->purpose,
                'tenderNo' => $emd->tender_no ?? 'NA',
                'tenderName' => $emd->project_name,
                'assignee' => $sender->name,
                'dueDate' => date('d-m-Y', strtotime($emd->due_date)) ?? 'NA',
                'dueTime' => date('h:i A', strtotime($emd->due_date)) ?? 'NA',
                'bt_acc' => $bt->bt_acc,
                'bt_ifsc' => $bt->bt_ifsc,
                'bt_acc_name' => $bt->bt_acc_name,
                'amount' => $tender ? $tender->emd : $bt->bt_amount,
                'tlName' => $tlName,
                'utr' => route('bt-action', $bt->id),
                'link' => route('bt-action', $bt->id),
            ];
            Log::info("BT Created Mail Data: " . json_encode($data));
            $to = User::where('role', 'account-executive')->first()->email ?? 'shivani@volksenergie.in';
            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to($to)
                ->cc([$adminMail, $tlMail, $cooMail, 'accounts@volksenergie.in'])
                ->send(new BankTransferMail($data));
            Log::info("Mail sent successfully for EMD ID: $emdId");
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("BT Mail Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    public function chequeMail($emdId, $pdfs)
    {
        try {
            $tender = null;
            $emd = Emds::find($emdId);
            if ($emd->tender_id != '00') {
                $tender = TenderInfo::find($emd->tender_id);
                $member = User::find($tender->team_member);
            } else {
                $member = Auth::user();
            }
            $team = $member->team ?? 'DC';
            $cooMail = User::where('role', 'coordinator')->where('team', $team)->value('email') ?? 'gyanprakashk55@gmail.com';
            $adminMail = User::where('role', 'admin')->where('team', $team)->value('email') ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->where('team', $team)->value('email') ?? 'gyanprakashk55@gmail.com';
            $tlName = User::where('role', 'team-leader')->where('team', $team)->value('name') ?? 'Gyan Prakash';
            $cheq = EmdCheque::where('emd_id', $emdId)->first();

            Log::info("Cheque EMD Tender Data: " . json_encode($tender));
            $data = [
                'assignee' => $member->name,
                'purpose' => $cheq->cheque_reason,
                'partyName' => $cheq->dd_id ? 'Yourself for DD' : $cheq->cheque_favour,
                'chequeDate' => date('d-m-Y', strtotime($cheq->cheque_date)),
                'amount' => $cheq->cheque_amt,
                'time_limit' => $cheq->cheque_needs,
                'link' => route('cheque-action', $cheq->id),
                'tlName' => $tlName,
                'pdfs' => $pdfs
            ];
            Log::info("Cheque Mail Data: " . json_encode($data));
            $to = User::where('role', 'account-executive')->first()->email ?? 'gyanprakashk55@gmail.com';
            MailHelper::configureMailer($member->email, $member->app_password, $member->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to(['kailash@volksenergie.in'])
                ->cc([$adminMail, $tlMail, $cooMail, 'accounts@volksenergie.in'])
                ->send(new ChequeCreatedMail($data));

            try {
                Log::info("Cheque Mail sent successfully");
                // Clean up temporary PDF files
                // foreach ($pdfs as $file) {
                //     Log::info("Deleting temp file: " . storage_path('app/temp/' . basename($file)));
                //     Storage::delete('temp/' . basename($file));
                // }
            } catch (\Exception $e) {
                Log::error("Cheque Mail Error: " . $e->getMessage());
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("Cheque Create Mail: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    public function ddMail($emdId, $pdfs)
    {
        try {
            $tender = null;
            $emd = Emds::findOrFail($emdId);
            Log::info("DD EMD Data: " . json_encode($emd));
            if ($emd->tender_id != '00') {
                $tender = TenderInfo::find($emd->tender_id);
                $member = User::find($tender->team_member);
            } else {
                $member = User::where('name', 'LIKE', "$emd->requested_by%")->first();
            }
            Log::info("DD EMD Member Data: " . json_encode($member));

            $team = $member->team ?? 'DC';
            $dd = EmdDemandDraft::where('emd_id', $emd->id)->firstOrFail();
            $cooMail = User::where('role', 'coordinator')->where('team', $team)->value('email') ?? 'gyanprakashk55@gmail.com';
            $adminMail = User::where('role', 'admin')->where('team', $team)->value('email') ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->where('team', $team)->value('email') ?? 'gyanprakashk55@gmail.com';
            $tlName = User::where('role', 'team-leader')->where('team', $team)->value('name') ?? 'Gyan Prakash';

            $data = [
                'purpose' => $dd->dd_purpose,
                'partyName' => 'YESBANK Ltd.',
                'chequeDate' => date('d-m-Y'),
                'amount' => $dd->dd_amt,
                'link' => route('dd-action', $dd->id),
                'assignee' => $member->name,
                'tlName' => $tlName,
                'pdfs' => $pdfs
            ];
            Log::error("DD Created Mail Data: " . json_encode($data));

            MailHelper::configureMailer($member->email, $member->app_password, $member->name);
            // MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Gyan');
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to(['kailash@volksenergie.in'])
                ->cc([$adminMail, $tlMail, $cooMail, 'accounts@volksenergie.in'])
                ->send(new DdCreatedMail($data));

            try {
                Log::error("DD Created Mail Success");
                // Clean up temporary PDF files
                // foreach ($pdfs as $file) {
                //     Log::info("Deleting temp file: " . storage_path('app/temp/' . basename($file)));
                //     Storage::delete('temp/' . basename($file));
                // }
            } catch (\Exception $e) {
                Log::error("DD Created Mail Error: " . $e->getMessage());
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("DD Created Mail Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    public function popMail($emdId)
    {
        try {
            $tender = null;
            $emd = Emds::find($emdId);

            if ($emd->tender_id != '0') {
                $tender = TenderInfo::find($emd->tender_id);
                $teamMember = User::find($tender->team_member);
                $assignee = $teamMember->name ?? 'gyanprakash';
                $appPass = $teamMember->app_password ?? '12345678';
                $assigenMail = $teamMember->email ?? 'gyanprakashk55@gmail.com';
            } else {
                $teamMember = Auth::user();
                $assignee = $teamMember->name;
                $appPass = $teamMember->app_password ?? '12345678';
                $assigenMail = $teamMember->email ?? 'gyanprakashk55@gmail.com';
            }
            $team = $teamMember->team ?? 'DC';
            $userRoles = User::where('team', $team)->whereIn('role', ['admin', 'team-leader', 'coordinator'])->pluck('email', 'role')->toArray();
            $adminMail = $userRoles['admin'] ?? 'gyanprakashk55@gmail.com';
            $tlMail = $userRoles['team-leader'] ?? 'gyanprakashk55@gmail.com';
            $cooMail = $userRoles['coordinator'] ?? 'gyanprakashk55@gmail.com';
            $tlName = User::where('team', $team)->where('role', 'team-leader')->value('name') ?? 'gyanprakashk';

            $pop = PayOnPortal::where('emd_id', $emdId)->firstOrFail();
            $dueDate = date('d-m-Y', strtotime($emd->due_date));
            $data = [
                'tender_no' => $emd->tender_no ?? 'NA',
                'tender_name' => $emd->project_name,
                'portal' => $pop->portal,
                'purpose' => $pop->purpose,
                'dueDate' => $dueDate,
                'netbanking' => $pop->is_netbanking,
                'debit' => $pop->is_debit,
                'amount' => $pop->amount,
                'assignee' => $assignee,
                'tlName' => $tlName,
                'link' => route('pop-action', $pop->id),
            ];
            Log::info("POP Mail Data: " . json_encode($data));
            $to = User::where('role', 'account-executive')->first()->email ?? 'shivani@volksenergie.in';

            MailHelper::configureMailer($assigenMail, $appPass, $assignee);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to($to)
                ->cc([$cooMail, $adminMail, 'accounts@volksenergie.in', $tlMail])
                ->send(new PopCreatedMail($data));

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("POP Mail Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    public function bgCreatedMail($bgId, $pdfs)
    {
        try {
            $tender = null;
            Log::info("BG Created Pdfs: " . json_encode($pdfs));
            $bg = EmdBg::where('id', $bgId)->first();

            if ($bg->emds->tender_id != '0') {
                $tender = TenderInfo::find($bg->emds->tender_id);
                $teamMember = User::find($tender->team_member);
                $assignee = $teamMember->name ?? 'gyanprakash';
                $appPass = $teamMember->app_password ?? '12345678';
                $assigneeMail = $teamMember->email ?? 'gyanprakashk55@gmail.com';
            } else {
                $teamMember = Auth::user();
                $assignee = $teamMember->name;
                $appPass = $teamMember->app_password ?? '12345678';
                $assigneeMail = $teamMember->email ?? 'gyanprakashk55@gmail.com';
            }
            $team = $teamMember->team ?? 'DC';
            $userRoles = User::where('team', $team)->whereIn('role', ['admin', 'coordinator', 'team-leader'])->pluck('email', 'role')->toArray();
            $adminMail = $userRoles['admin'] ?? 'goyal@volksenergie.in';
            $tlMail = $userRoles['team-leader'] ?? 'gyanprakashk55@gmail.com';
            $cooMail = $userRoles['coordinator'] ?? 'gyanprakashk55@gmail.com';
            $purpose = [
                'advance' => 'Advance Payment',
                'deposit' => 'Security Bond/ Deposit',
                'bid' => 'Bid Bond',
                'performance' => 'Performance',
                'financial' => 'Financial',
                'counter' => 'Counter Guarantee',
            ];

            $data = [
                'purpose' => $purpose[$bg->bg_purpose],
                'beneficiary_name' => $bg->bg_bank_name,
                'account_no' => $bg->bg_bank_acc,
                'ifsc_code' => $bg->bg_bank_ifsc,
                'amount' => format_inr($bg->bg_amt),
                'bg_stamp' => format_inr($bg->bg_stamp),
                'bg_in_favor_of' => $bg->bg_favour,
                'bg_address' => $bg->bg_address,
                'bg_expiry_date' => date('d-m-Y', strtotime($bg->bg_expiry)),
                'bg_claim_date' => date('d-m-Y', strtotime($bg->bg_claim)),
                'bg_needs' => $bg->bg_needs,
                'link_to_acc_form' => route('bg-action', $bg->id),
                'courier_address' => $bg->bg_courier_addr,
                'te' => $assignee,
                'files' => [$bg->bg_format_te, $bg->bg_po],
                'pdfs' => $pdfs
            ];

            Log::info("BG Created Mail Data: " . json_encode($data));

            MailHelper::configureMailer($assigneeMail, $appPass, $assignee);
            // MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Gyan');
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to(['imran@volksenergie.in'])
                ->cc([$adminMail, $tlMail, $cooMail, 'accounts@volksenergie.in'])
                ->send(new BgCreatedMail($data));

            Log::info("BG Created Mail sent successfully to: imran@volksenergie.in");

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("BG Mail Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    public function repeatEmail($bgId)
    {
        $bg = EmdBg::where('id', $bgId)->first();
        $pdfs = array_merge(
            [$bg->bg_format_te, $bg->bg_po],
            json_decode($bg->generated_pdfs, true)
        );

        return $this->bgCreatedMail($bgId, $pdfs);
    }

}
