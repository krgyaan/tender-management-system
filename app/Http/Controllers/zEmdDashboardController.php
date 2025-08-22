<?php

namespace App\Http\Controllers;

use App\Models\Emds;
use App\Models\User;
use App\Models\EmdBg;
use App\Models\EmdFdr;
use App\Models\EmdCheque;
use App\Models\FollowUps;
use App\Mail\ChequeAcForm;
use App\Mail\DdChqRejMail;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use App\Mail\PopStatusMail;
use App\Models\PayOnPortal;
use App\Mail\BgReminderMail;
use App\Mail\ChequeStopMail;
use App\Models\BankTransfer;
use Illuminate\Http\Request;
use App\Mail\DdChqAcceptMail;
use App\Models\EmdDemandDraft;
use App\Services\TimerService;
use App\Mail\DdAccountFormMail;
use App\Models\FollowUpPersons;
use App\Mail\BankTransferStatus;
use App\Mail\BgAccountForm1Mail;
use App\Mail\BgAccountForm2Mail;
use App\Mail\BgAccountForm3Mail;
use App\Mail\BgCancellationMail;
use App\Mail\DdCancellationMail;
use App\Mail\BgFDRCancellationMail;
use App\Mail\BgReturnedCourierMail;
use App\Mail\ChequeDueDateReminder;
use Illuminate\Support\Facades\Log;
use App\Mail\BgRequestExtensionMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Services\PdfGeneratorService;
use Illuminate\Support\Facades\Config;
use App\Mail\BgRequestCancellationMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Mail\BgClaimPeriodMail;
use Carbon\Carbon;

class EmdDashboardController extends Controller
{
    public $instrumentType = [
        '1' => 'Demand Draft',
        '2' => 'FDR',
        '3' => 'Cheque',
        '4' => 'BG',
        '5' => 'Bank Transfer',
        '6' => 'Pay on Portal',
    ];
    protected $timerService;
    protected $pdfGenerator;

    public function __construct(TimerService $timerService, PdfGeneratorService $pdfGenerator)
    {
        $this->timerService = $timerService;
        $this->pdfGenerator = $pdfGenerator;
    }
    public function dashboard()
    {
        try {
            $data['emdBt'] = BankTransfer::with('emd', 'emd.tender')->latest()->get();
            $data['emdPop'] = PayOnPortal::with('emd', 'emd.tender')->latest()->get();
            $data['emdDd'] = EmdDemandDraft::with('emd', 'emd.tender')->latest()->get();
            $data['emdCheque'] = EmdCheque::with('emds', 'emds.tender')->latest()->get();
            $data['emdBg'] = EmdBg::with('emds', 'emds.tender')->latest()->get();
            $data['emdFdr'] = EmdFdr::with('emds', 'emds.tender')->latest()->get();
            return view('emds.emd-dashboard.index', $data);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function BG(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
                $data['emdBg'] = EmdBg::with('emds', 'emds.tender')->latest()->get();
                $groupedBg = $data['emdBg']->groupBy('bg_bank');
                $totalBgCount = $data['emdBg']->count();
                $totalBgAmount = $data['emdBg']->sum('bg_amount');
                $bankStats = [];
                foreach ($groupedBg as $bankName => $bgs) {
                    $bankCount = $bgs->count();
                    $bankAmount = $bgs->sum('bg_amt');

                    $percentage = ($bankCount / $totalBgCount) * 100;

                    $bankStats[$bankName] = [
                        'count' => $bankCount,
                        'percentage' => $percentage,
                        'amount' => $bankAmount,
                    ];
                }
                $data['groupedBg'] = $groupedBg;
                $data['bankStats'] = $bankStats;
                $data['totalBgCount'] = $totalBgCount;
                $data['totalBgAmount'] = $totalBgAmount;
                // dd($data['groupedBg']);
                return view('emds.emd-dashboard.bank-gurantee', $data);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }
    public function DD(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
                $data['emdDd'] = EmdDemandDraft::with('emd', 'emd.tender', 'ddChq', 'emd.tender.statuses')->latest()->get();
                return view('emds.emd-dashboard.demand-draft', $data);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }
    public function BT(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
                $data['emdBt'] = BankTransfer::with('emd', 'emd.tender', 'emd.tender.statuses')->latest()->get();
                return view('emds.emd-dashboard.bank-transfer', $data);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }
    public function POP(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
                $data['emdPop'] = PayOnPortal::with('emd', 'emd.tender', 'emd.tender.statuses')->latest()->get();
                return view('emds.emd-dashboard.pay-on-portal', $data);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }
    public function CHQ(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
                $data['emdCheque'] = EmdCheque::with('emds', 'emds.tender')->latest()->get();
                return view('emds.emd-dashboard.cheque', $data);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }
    public function FDR(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
                $data['emdFdr'] = EmdFdr::with('emds', 'emds.tender')->latest()->get();
                return view('emds.emd-dashboard.fdr', $data);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }

    public function DemandDraftDashboard(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $dd = EmdDemandDraft::find($id);
                return view('emds.emd-dashboard.dd-action', compact('dd'));
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }

        if ($request->isMethod('put')) {
            try {
                Log::info('Emd update called by ' . Auth::user()->name . ' with id ' . $id);
                $request->validate([
                    'action' => 'required',
                    'dd_date' => 'nullable|date',
                    'dd_no' => 'nullable',
                    'req_no' => 'nullable',
                    'remarks' => 'nullable',
                    'docket_no' => 'nullable',
                    'docket_slip' => 'nullable',
                    'transfer_date' => 'nullable|date',
                    'utr' => 'nullable',
                    'date' => 'nullable|date',
                    'amount' => 'nullable',
                    'reference_no' => 'nullable',
                ]);

                $emd = EmdDemandDraft::find($id);
                switch ($request->action) {
                    case '1': // Accounts Form
                        Log::info('Accounts Form called by ' . Auth::user()->name . ' For DD id ' . $id);
                        $action = $request->action;
                        $dd_date = $request->dd_date;
                        $dd_no = $request->dd_no;
                        $req_no = $request->req_no;
                        $remarks = $request->remarks;

                        $emd->action = $action;
                        $emd->dd_date = $dd_date;
                        $emd->dd_no = $dd_no;
                        $emd->req_no = $req_no;
                        $emd->remarks = $remarks;
                        $emd->save();

                        if ($this->ddAccountForm($emd->id)) {
                            Log::info('Demand Draft account form mail sent successfully', ['id' => $emd->id]);
                        } else {
                            Log::info('Demand Draft account form mail sending failed', ['id' => $emd->id]);
                        }

                        break;

                    case '2': // Initiate Followup
                        Log::info('Initiate Followup called by ' . Auth::user()->name . ' For DD id ' . $id);
                        $validator = Validator::make($request->all(), [
                            'org_name' => 'nullable|string|max:255',
                            'fp' => 'nullable|array',
                            'fp.*.name' => 'nullable|string|max:255',
                            'fp.*.phone' => 'nullable|digits_between:10,15',
                            'fp.*.email' => 'nullable|email|max:255',
                            'frequency' => 'required|in:1,2,3,4,5,6',
                            'stop_reason' => 'required_if:frequency,6|nullable|in:1,2,3,4',
                            'proof_text' => 'required_if:stop_reason,2|nullable|string|max:500',
                            'proof_img' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
                            'stop_rem' => 'required_if:stop_reason,4|nullable|string|max:500',
                        ], [
                            'stop_reason.required_if' => 'Stop reason is required when frequency is "Stop".',
                            'proof_text.required_if' => 'Please provide proof when stop reason is "Followup Objective achieved".',
                            'proof_img.image' => 'Proof image must be an image file.',
                            'proof_img.mimes' => 'Proof image must be a file of type: jpg, png, jpeg.',
                            'stop_rem.required_if' => 'Remarks are required when stop reason is "Remarks".',
                        ]);

                        if ($validator->fails()) {
                            return redirect()->back()->withErrors($validator)->withInput();
                        }

                        $emd = EmdDemandDraft::find($id);
                        $emd->action = $request->input('action');
                        $emd->save();

                        $amount = $emd->dd_amount;
                        $org_name = $request->org_name;
                        $frequency = $request->frequency;
                        $stop_reason = $request->stop_reason;
                        $proof_text = $request->proof_text;
                        $stop_rem = $request->stop_rem;
                        $start = $request->start_date;
                        $assignee = $emd->emd->tender->team_member;
                        $area = $emd->emd->tender->team;

                        $proof_img = null;
                        if ($request->hasFile('proof_img')) {
                            $image = $request->file('proof_img');
                            $imageName = time() . '_' . str_replace(' ', '_', strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))) . '.' . $image->getClientOriginalExtension();
                            $image->move(public_path('uploads/accounts'), $imageName);
                            $proof_img = $imageName;
                        }

                        $followup = new FollowUps();
                        $followup->emd_id = $emd->emd_id;
                        $followup->area = $area == 'AC' ? 'AC Team' : 'DC Team';
                        $followup->followup_for = 'EMD Refund';
                        $followup->party_name = $org_name;
                        $followup->amount = $amount;
                        $followup->assigned_to = $assignee;
                        $followup->frequency = $frequency;
                        $followup->assign_initiate = 'Followup Assigned';
                        $followup->created_by = Auth::user()->id;
                        $followup->stop_reason = $stop_reason;
                        $followup->proof_text = $proof_text;
                        $followup->proof_img = $proof_img;
                        $followup->stop_rem = $stop_rem;
                        $followup->start_from = $start;
                        $followup->attachments = '["2025_cancelled_check_n_yes_bank_mandate.pdf"]';
                        $followup->save();

                        if ($request->has('fp') && is_array($request->fp)) {
                            foreach ($request->fp as $person) {
                                $fup = new FollowUpPersons();
                                $fup->follwup_id = $followup->id;
                                $fup->name = $person['name'] ?? null;
                                $fup->phone = $person['phone'] ?? null;
                                $fup->email = $person['email'] ?? null;
                                $fup->save();
                            }
                        }
                        Log::info('BT EMD Followup initiated successfully' . json_encode($followup));

                        if ((new FollowUpsController)->followupMail($followup->id)) {
                            return redirect()->back()->with('success', 'Followup initiated and mail sent to targeted persons successfully');
                        } else {
                            return redirect()->back()->with('error', 'Followup initiated successfully but mail not sent to targeted persons');
                        }


                    case '3': // Returned via courier
                        Log::info('Returned via courier called by ' . Auth::user()->name . ' For DD id ' . $id);
                        $action = $request->action;
                        $docket_no = $request->docket_no;

                        if ($request->hasFile('docket_slip')) {
                            $image = $request->file('docket_slip');
                            $file = strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
                            $oname = str_replace(' ', '_', $file);
                            $imageName = time() . '_' . $oname . '.' . $image->getClientOriginalExtension();
                            $image->move(public_path('uploads/accounts'), $imageName);
                            $docket_slip = $imageName;
                        } else {
                            $docket_slip = null;
                        }

                        $emd->action = $action;
                        $emd->docket_no = $docket_no;
                        $emd->docket_slip = $docket_slip;
                        $emd->save();
                        break;

                    case '4': // Returned via Bank Transfer
                        Log::info('Returned via Bank Transfer called by ' . Auth::user()->name . ' For DD id ' . $id);
                        $action = $request->action;
                        $transfer_date = $request->transfer_date;
                        $utr = $request->utr;

                        $emd->action = $action;
                        $emd->transfer_date = $transfer_date;
                        $emd->utr = $utr;
                        $emd->save();
                        break;

                    case '5': // Settled with Project Account
                        Log::info('Settled with Project Account called by ' . Auth::user()->name . ' For DD id ' . $id);
                        $action = $request->action;

                        $emd->action = $action;
                        $emd->save();
                        break;

                    case '6': // Send DD Cancellation Request
                        Log::info('Send DD Cancellation Request called by ' . Auth::user()->name . ' For DD id ' . $id);
                        $action = $request->action;

                        $emd->action = $action;
                        $emd->save();

                        $pdfData = [
                            'dd_no' => $emd->dd_no,
                            'dd_date' => $emd->dd_date,
                            'amount' => format_inr($emd->dd_amt),
                            'beneficiary_name' => 'YESBANK',
                        ];

                        $pdfFiles = $this->pdfGenerator->generatePdfs('DdCancellation', $pdfData);
                        try {
                            // Check if PDF files were generated successfully
                            if (empty($pdfFiles)) {
                                Log::warning('No PDF files generated for DD Cancellation. ID: ' . $emd->id);
                            }

                            $mailResult = $this->ddCancelling($emd->id, $pdfFiles);
                            if ($mailResult === null) {
                                Log::info('DD Cancellation Mail Sent Successfully for DD ID: ' . $emd->id);
                            } else {
                                Log::error('Failed to send DD Cancellation Request for DD ID: ' . $emd->id . '. Result: ' . json_encode($mailResult));
                            }
                        } catch (\Throwable $th) {
                            Log::error('DD Cancellation Error: ' . $th->getMessage());
                        }
                        break;

                    case '7': // DD cancelled at Branch
                        Log::info('DD cancelled at Branch called by ' . Auth::user()->name . ' For DD id ' . $id);
                        $action = $request->action;
                        $date = $request->date;
                        $amount = $request->amount;
                        $reference_no = $request->reference_no;

                        $emd->action = $action;
                        $emd->date = $date;
                        $emd->amount = $amount;
                        $emd->reference_no = $reference_no;
                        $emd->save();
                        break;

                    default:
                        # code...
                        break;
                }

                return redirect()->back()->with('success', 'Demand Draft Updated Successfully');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }
    public function FDRDashboard(Request $request, $id)
    {
        try {
            dd($request->all());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function ChequeDashboard(Request $request, $id)
    {
        if ($request->isMethod('GET')) {
            try {
                $cheque = EmdCheque::where('id', $id)->first();
                return view('emds.emd-dashboard.cheque-action', compact('cheque'));
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }

        if ($request->isMethod('PUT')) {
            try {
                $validator = $request->validate([
                    'action' => 'required',
                    'status' => 'nullable|string',
                    'reason' => 'nullable|string',
                    'cheq_no' => 'nullable|string',
                    'duedate' => 'nullable|date',
                    'handover' => 'nullable|string',
                    'cheq_img' => 'nullable|array',
                    'confirmation' => 'nullable|image|mimes:jpg,jpeg,png,gif',
                    'remarks' => 'nullable|string',
                    'transfer_date' => 'nullable|string',
                    'amount' => 'nullable|string',
                    'utr' => 'nullable|string',
                    'bt_transfer_date' => 'nullable|date',
                    'reference' => 'nullable|string',
                    'cancelled_img' => 'nullable|image|mimes:jpg,jpeg,png,gif',
                ]);

                switch ($request->input('action')) {
                    case '1': // Accounts Form
                        Log::info('Accounts Form called by ' . Auth::user()->name . ' For Cheque id ' . $id);
                        $action = $request->action;
                        $status = $request->status;
                        $reason = $request->reason;
                        $cheq_no = $request->cheq_no;
                        $duedate = $request->duedate;
                        $handover = $request->handover;
                        $remarks = $request->remarks;

                        if ($request->hasFile('cheq_img')) {
                            $cheqImages = [];
                            foreach ($request->file('cheq_img') as $image) {
                                $file = strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
                                $oname = str_replace(' ', '_', $file);
                                $imageName = time() . '_' . $oname . '.' . $image->getClientOriginalExtension();
                                $image->move(public_path('uploads/accounts'), $imageName);
                                $cheqImages[] = $imageName;
                            }
                            $cheq_img = implode(',', $cheqImages);
                        } else {
                            $cheq_img = null;
                        }
                        $cheqImg = $cheq_img;

                        if ($request->hasFile('confirmation')) {
                            $image = $request->file('confirmation');
                            $file = strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
                            $oname = str_replace(' ', '_', $file);
                            $imageName = time() . '_' . $oname . '.' . $image->getClientOriginalExtension();
                            $image->move(public_path('uploads/accounts'), $imageName);
                            $confirmation = $imageName;
                        } else {
                            $confirmation = null;
                        }

                        $emd = EmdCheque::find($id);
                        $emd->action = $action;
                        $emd->status = $status;
                        $emd->reason = $reason;
                        $emd->cheq_no = $cheq_no;
                        $emd->duedate = $duedate;
                        $emd->handover = $handover;
                        $emd->cheq_img = $cheqImg;
                        $emd->confirmation = $confirmation;
                        $emd->remarks = $remarks;
                        $emd->save();

                        if ($emd->dd_id) {
                            $pdfFiles = $this->pdfGenerator->generatePdfs('chqCret', $emd->toArray());
                            $result = $emd->status == 'Accepted'
                                ? $this->DdChqAccept($emd->id, $pdfFiles)
                                : ($emd->status == 'Rejected' ? $this->DdChqReject($emd->id) : false);

                            if ($result) {
                                Log::info('Cheque EMD for DD ' . $emd->dd_id . ' ' . strtolower($emd->status) . ' successfully');
                            }
                        } else {
                            // stop bt_acc_form timer
                            $tender = TenderInfo::where('id', $emd->emds->tender_id)->first();
                            $this->timerService->stopTimer($tender, 'cheque_ac_form');

                            $mail = $this->chequeAccountForm($emd->id);
                            if ($mail) {
                                Log::info('Account Form Cheque EMD Mail sent for successfully');
                            } else {
                                Log::error('Account Form Cheque EMD Mail not sent');
                            }
                        }

                        break;

                    case '2': // Initiate Followup
                        Log::info('Initiate Followup called by ' . Auth::user()->name . ' For Cheque id ' . $id);
                        $validator = Validator::make($request->all(), [
                            'fp.*' => 'required|array',
                            'fp.org_name.*' => 'nullable|string|max:255',
                            'fp.name.*' => 'nullable|string|max:255',
                            'fp.phone.*' => 'nullable|digits_between:10,15',
                            'fp.email.*' => 'nullable|email|max:255',
                            'frequency' => 'required|in:1,2,3,4',
                            'stop_reason' => 'required_if:frequency,4',
                            'proof_text' => 'required_if:stop_reason,2',
                            'proof_img' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
                            'stop_rem' => 'nullable|string|max:500',
                        ], [
                            'stop_reason.required_if' => 'Stop reason is required when frequency is "Stop".',
                            'proof_text.required_if' => 'Please provide proof when stop reason is "Followup Objective achieved".',
                            'proof_img.image' => 'Proof image must be an image file.',
                            'proof_img.mimes' => 'Proof image must be a file of type: jpg, png, jpeg.',
                        ]);

                        if ($validator->fails()) {
                            return redirect()->back()->withErrors($validator)->withInput();
                        }
                        $emd = EmdCheque::find($id);
                        $emd->action = $request->input('action');
                        $emd->save();

                        $amount = $emd->cheque_amt;
                        $org_name = $request->org_name;
                        $frequency = $request->frequency;
                        $stop_reason = $request->stop_reason;
                        $proof_text = $request->proof_text;
                        $stop_rem = $request->stop_rem;
                        $start = $request->start_date;
                        $assignee = $emd->emds->tender->team_member;
                        $area = $emd->emds->tender->team;

                        $proof_img = null;
                        if ($request->hasFile('proof_img')) {
                            $image = $request->file('proof_img');
                            $imageName = time() . '_' . str_replace(' ', '_', strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))) . '.' . $image->getClientOriginalExtension();
                            $image->move(public_path('uploads/accounts'), $imageName);
                            $proof_img = $imageName;
                        }

                        $followup = new FollowUps();
                        $followup->emd_id = $emd->emd_id;
                        $followup->area = $area == 'AC' ? 'AC Team' : 'DC Team';
                        $followup->followup_for = 'Cheque Return';
                        $followup->party_name = $org_name;
                        $followup->amount = $amount;
                        $followup->assigned_to = $assignee;
                        $followup->frequency = $frequency;
                        $followup->assign_initiate = 'Followup Assigned';
                        $followup->created_by = Auth::user()->id;
                        $followup->stop_reason = $stop_reason;
                        $followup->proof_text = $proof_text;
                        $followup->proof_img = $proof_img;
                        $followup->stop_rem = $stop_rem;
                        $followup->start_from = $start;
                        $followup->attachments = '["2025_cancelled_check_n_yes_bank_mandate.pdf"]';
                        $followup->save();

                        if ($request->has('fp') && is_array($request->fp)) {
                            foreach ($request->fp as $person) {
                                $fup = new FollowUpPersons();
                                $fup->follwup_id = $followup->id;
                                $fup->name = $person['name'] ?? null;
                                $fup->phone = $person['phone'] ?? null;
                                $fup->email = $person['email'] ?? null;
                                $fup->save();
                            }
                        }
                        Log::info('Chq EMD Followup initiated successfully' . json_encode($followup));

                        if ((new FollowUpsController)->followupMail($followup->id)) {
                            return redirect()->back()->with('success', 'Followup initiated and mail sent to targeted persons successfully');
                        } else {
                            return redirect()->back()->with('error', 'Followup initiated successfully but mail not sent to targeted persons');
                        }

                    case '3': // Stop the cheque from the bank
                        Log::info('Stop cheque called by ' . Auth::user()->name . ' For Cheque id ' . $id);
                        $action = $request->action;

                        $emd = EmdCheque::find($id);
                        $emd->action = $request->action;
                        $emd->stop_reason_text = $request->stop_reason_text;
                        $emd->save();

                        if ($this->chequeStopMail($id)) {
                            Log::info('Cheque Stop Mail sent successfully');
                        } else {
                            Log::error('Failed to send Cheque Stop Mail');
                        }

                        break;

                    case '4': // Paid via Bank Transfer
                        Log::info('Paid via Bank Transfer called by ' . Auth::user()->name . ' For Cheque id ' . $id);
                        $action = $request->action;
                        $transfer_date = $request->transfer_date;
                        $amount = $request->amount;
                        $utr = $request->utr;

                        $emd = EmdCheque::find($id);
                        $emd->action = $action;
                        $emd->transfer_date = $transfer_date;
                        $emd->amount = $amount;
                        $emd->utr = $utr;
                        $emd->save();
                        break;

                    case '5': // Deposited in Bank
                        Log::info('Deposited in Bank called by ' . Auth::user()->name . ' For Cheque id ' . $id);
                        $action = $request->action;
                        $bt_transfer_date = $request->bt_transfer_date;
                        $reference = $request->reference;

                        $emd = EmdCheque::find($id);
                        $emd->action = $action;
                        $emd->bt_transfer_date = $bt_transfer_date;
                        $emd->reference = $reference;
                        $emd->save();
                        break;

                    case '6': // Cancelled/Torn
                        Log::info('Cancelled/Torn called by ' . Auth::user()->name . ' For Cheque id ' . $id);
                        $action = $request->action;

                        if ($request->hasFile('cancelled_img')) {
                            $image = $request->file('cancelled_img');
                            $file = strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
                            $oname = str_replace(' ', '_', $file);
                            $imageName = time() . '_' . $oname . '.' . $image->getClientOriginalExtension();
                            $image->move(public_path('uploads/accounts'), $imageName);
                            $cancelled_img = $imageName;
                        } else {
                            $cancelled_img = null;
                        }

                        $emd = EmdCheque::find($id);
                        $emd->action = $action;
                        $emd->cancelled_img = $cancelled_img;
                        $emd->save();
                        break;

                    default:
                        return redirect()->back()->with('error', 'Invalid action selected.');
                }
                return redirect()->back()->with('success', 'Cheque EMD status updated successfully');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }
    public function BankGuaranteeDashboard(Request $request, $id)
    {
        if ($request->isMethod('GET')) {
            $bg = EmdBg::find($id);
            $followups = FollowUps::where('emd_id', $id)->where('followup_for', 'BG')->get();
            $followup_persons = FollowUpPersons::whereHas('followup', function ($query) use ($id) {
                $query->where('followup_for', 'BG')->where('emd_id', $id);
            })->get();
            return view('emds.emd-dashboard.bg-action', compact('bg', 'followups', 'followup_persons'));
        }

        if ($request->isMethod('PUT')) {
            // dd($request->all());
            try {
                $request->validate([
                    'action' => 'required',
                    'bg_req' => 'nullable|string',
                    'reason_req' => 'nullable|string',
                    'bg_fdr_percent' => 'nullable|string',
                    'approve_bg' => 'nullable|string',
                    'bg_no' => 'nullable|string',
                    'bg_date' => 'nullable|date',
                    'claim_expiry' => 'nullable|date',
                    'courier_no' => 'nullable|string',
                    'bg2_remark' => 'nullable|string',
                    'sfms_conf' => 'nullable|file',
                    'fdr_copy' => 'nullable|file',
                    'fdr_no' => 'nullable|string',
                    'fdr_per' => 'nullable|numeric',
                    'fdr_amt' => 'nullable|numeric',
                    'fdr_validity' => 'nullable|date',
                    'fdr_roi' => 'nullable|numeric',
                    'bg_charge_deducted' => 'nullable|numeric',
                    'sfms_charge_deducted' => 'nullable|numeric',
                    'stamp_charge_deducted' => 'nullable|numeric',
                    'other_charge_deducted' => 'nullable|numeric',
                    'frequency' => 'nullable|string',
                    'stop_reason' => 'nullable|string',
                    'proof_text' => 'nullable|string',
                    'proof_img' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
                    'stop_rem' => 'nullable|string',
                    'docket_no' => 'nullable|string',
                    'docket_slip' => 'nullable|file',
                    'bg_fdr_cancel_date' => 'nullable|date',
                    'bg_fdr_cancel_amount' => 'nullable|string',
                    'bg_fdr_cancel_ref_no' => 'nullable|string',
                    'new_stamp_charge_deducted' => 'nullable|numeric',
                    'new_bg_bank_name' => 'nullable|string',
                    'new_bg_amt' => 'nullable|numeric',
                    'new_bg_expiry' => 'nullable|date',
                    'new_bg_claim' => 'nullable|date',
                ]);
                $bg = EmdBg::find($id);
                switch ($request->input('action')) {
                    case '1': // Accounts Form 1
                        Log::info('BG Acoount Form 1 updated for BG ID: ' . $id . ' by ' . Auth::user()->name);
                        $bg->action = $request->input('action');
                        $bg->bg_req = $request->input('bg_req');
                        $bg->reason_req = $request->input('reason_req');
                        $bg->bg_fdr_percent = $request->input('bg_fdr_percent');
                        $bg->approve_bg = $request->input('approve_bg');

                        if ($request->hasFile('bg_format_imran')) {
                            $file = $request->file('bg_format_imran');
                            $filename = time() . '_bg_format_' . '.' . $file->getClientOriginalName();
                            $file->move(public_path('uploads/accounts'), $filename);
                            $bg->bg_format_imran = $filename;
                        }
                        $bg->save();

                        if ($this->bgAccountForm1Mail($bg->id)) {
                            return redirect()->back()->with('success', 'BG Status Changed & Mail sent successfully.');
                        } else {
                            return redirect()->back()->with('error', 'BG Status Changed & Mail sending failed.');
                        }

                    case '2': // Accounts Form 2
                        Log::info('BG Acoount Form 2 updated for BG ID: ' . $id . ' by ' . Auth::user()->name);
                        $bg->action = $request->input('action');
                        $bg->bg_no = $request->input('bg_no');
                        $bg->bg_date = $request->input('bg_date');
                        $bg->claim_expiry = $request->input('claim_expiry');
                        $bg->courier_no = $request->input('courier_no');
                        $bg->bg2_remark = $request->input('bg2_remark');
                        $bg->save();

                        if ($this->bgAccountForm2Mail($bg->id)) {
                            return redirect()->back()->with('success', 'BG Status Changed & Mail sent successfully.');
                        } else {
                            return redirect()->back()->with('error', 'BG Status Changed & Mail sending failed.');
                        }

                    case '3': // Accounts Form 3
                        Log::info('BG Acoount Form 3 updated for BG ID: ' . $id . ' by ' . Auth::user()->name);
                        $bg->action = $request->input('action');
                        $bg->fdr_no = $request->input('fdr_no');
                        $bg->fdr_per = $request->input('fdr_per');
                        $bg->fdr_amt = $request->input('fdr_amt');
                        $bg->fdr_validity = $request->input('fdr_validity');
                        $bg->fdr_roi = $request->input('fdr_roi');
                        $bg->bg_charge_deducted = $request->input('bg_charge_deducted');
                        $bg->sfms_charge_deducted = $request->input('sfms_charge_deducted');
                        $bg->stamp_charge_deducted = $request->input('stamp_charge_deducted');
                        $bg->other_charge_deducted = $request->input('other_charge_deducted');

                        if ($request->hasFile('sfms_conf')) {
                            $file = $request->file('sfms_conf');
                            $filename = time() . '_bg_' . $bg->id . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('uploads/accounts'), $filename);
                            $bg->sfms_conf = $filename;
                        }

                        if ($request->hasFile('fdr_copy')) {
                            $file = $request->file('fdr_copy');
                            $filename = time() . '_bg_' . $bg->id . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('uploads/accounts'), $filename);
                            $bg->fdr_copy = $filename;
                        }

                        $bg->save();

                        if ($this->bgAccountForm3Mail($bg->id)) {
                            return redirect()->back()->with('success', 'BG Status Changed & Mail sent successfully.');
                        } else {
                            return redirect()->back()->with('error', 'BG Status Changed & Mail sending failed.');
                        }

                    case '4': // Initiate Followup
                        Log::info('BG Initiate followup for BG ID: ' . $id . ' by ' . Auth::user()->name);
                        $validator = Validator::make($request->all(), [
                            'org_name' => 'nullable|string|max:255',
                            'fp' => 'nullable|array',
                            'fp.*.name' => 'nullable|string|max:255',
                            'fp.*.phone' => 'nullable|digits_between:10,15',
                            'fp.*.email' => 'nullable|email|max:255',
                            'frequency' => 'required|in:1,2,3,4,5,6',
                            'stop_reason' => 'required_if:frequency,6|nullable|in:1,2,3,4',
                            'proof_text' => 'required_if:stop_reason,2|nullable|string|max:500',
                            'proof_img' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
                            'stop_rem' => 'required_if:stop_reason,4|nullable|string|max:500',
                        ], [
                            'stop_reason.required_if' => 'Stop reason is required when frequency is "Stop".',
                            'proof_text.required_if' => 'Please provide proof when stop reason is "Followup Objective achieved".',
                            'proof_img.image' => 'Proof image must be an image file.',
                            'proof_img.mimes' => 'Proof image must be a file of type: jpg, png, jpeg.',
                            'stop_rem.required_if' => 'Remarks are required when stop reason is "Remarks".',
                        ]);

                        if ($validator->fails()) {
                            return redirect()->back()->withErrors($validator)->withInput();
                        }

                        $bg->action = $request->input('action');
                        $bg->save();

                        $amount = $bg->bg_amount;
                        $org_name = $request->org_name;
                        $frequency = $request->frequency;
                        $stop_reason = $request->stop_reason;
                        $proof_text = $request->proof_text;
                        $stop_rem = $request->stop_rem;
                        $start = $request->start_date;
                        $assignee = $bg->emds->tender->team_member;
                        $area = $bg->emds->tender->team;

                        $proof_img = null;
                        if ($request->hasFile('proof_img')) {
                            $image = $request->file('proof_img');
                            $imageName = time() . '_' . str_replace(' ', '_', strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))) . '.' . $image->getClientOriginalExtension();
                            $image->move(public_path('uploads/accounts'), $imageName);
                            $proof_img = $imageName;
                        }

                        $followup = new FollowUps();
                        $followup->emd_id = $bg->emd->emd_id;
                        $followup->area = $area == 'AC' ? 'AC Team' : 'DC Team';
                        $followup->followup_for = 'EMD Refund';
                        $followup->party_name = $org_name;
                        $followup->amount = $amount;
                        $followup->assigned_to = $assignee;
                        $followup->frequency = $frequency;
                        $followup->assign_initiate = 'Followup Assigned';
                        $followup->created_by = Auth::user()->id;
                        $followup->stop_reason = $stop_reason;
                        $followup->proof_text = $proof_text;
                        $followup->proof_img = $proof_img;
                        $followup->stop_rem = $stop_rem;
                        $followup->start_from = $start;
                        $followup->attachments = '["2025_cancelled_check_n_yes_bank_mandate.pdf"]';
                        $followup->save();

                        if ($request->has('fp') && is_array($request->fp)) {
                            foreach ($request->fp as $person) {
                                $fup = new FollowUpPersons();
                                $fup->follwup_id = $followup->id;
                                $fup->name = $person['name'] ?? null;
                                $fup->phone = $person['phone'] ?? null;
                                $fup->email = $person['email'] ?? null;
                                $fup->save();
                            }
                        }
                        Log::info('BG EMD Followup initiated successfully' . json_encode($followup));

                        if ((new FollowUpsController)->followupMail($followup->id)) {
                            return redirect()->back()->with('success', 'Followup initiated and mail sent to targeted persons successfully');
                        } else {
                            return redirect()->back()->with('error', 'Followup initiated successfully but mail not sent to targeted persons');
                        }

                    case '5': // Request Extension
                        Log::info('BG Request Extension by ' . Auth::user()->name . ' for BG ' . $bg->emd_id);
                        $bg->action = $request->input('action');
                        if ($request->input('isModReq') == '1') {
                            $new_stamp_charge_deducted = $request->new_stamp_charge_deducted;
                            $new_bg_bank_name = $request->new_bg_bank_name;
                            $new_bg_amt = $request->new_bg_amt;
                            $new_bg_expiry = $request->new_bg_expiry;
                            $new_bg_claim = $request->new_bg_claim;

                            $bg->new_stamp_charge_deducted = $new_stamp_charge_deducted;
                            $bg->new_bg_bank_name = $new_bg_bank_name;
                            $bg->new_bg_amt = $new_bg_amt;
                            $bg->new_bg_expiry = $new_bg_expiry;
                            $bg->new_bg_claim = $new_bg_claim;

                            Log::info('BG Modification request accepted by ' . Auth::user()->name . ' for BG ' . $bg->emd_id, [
                                'action' => $request->input('action'),
                                'new_stamp_charge_deducted' => $new_stamp_charge_deducted,
                                'new_bg_bank_name' => $new_bg_bank_name,
                                'new_bg_amt' => $new_bg_amt,
                                'new_bg_expiry' => $new_bg_expiry,
                                'new_bg_claim' => $new_bg_claim
                            ]);
                        }
                        if ($request->hasFile('ext_letter')) {
                            $file = $request->file('ext_letter');
                            $filename = time() . '_bg_ext_letter_' . $bg->id . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('uploads/accounts'), $filename);
                            $bg->ext_letter = $filename;
                        }
                        $bg->save();
                        $pdfFiles = $this->pdfGenerator->generatePdfs('reqExtLetter', $bg->toArray());
                        $bg->request_extension_pdf = $pdfFiles[0];
                        $bg->save();

                        if ($this->bgRequestExtensionMail($bg->id)) {
                            Log::info('BG Request Extension mail sent successfully');
                            return redirect()->back()->with('success', 'BG Updated and Request Extension mail sent successfully');
                        } else {
                            Log::info('BG Request Extension mail not sent');
                            return redirect()->back()->with('error', 'BG Updated But Request Extension mail not sent');
                        }

                        break;

                    case '6': // Returned via courier
                        Log::info('BG Returned via courier by ' . Auth::user()->name . ' for BG ' . $bg->emd_id);
                        $bg->action = $request->input('action');
                        $bg->docket_no = $request->input('docket_no');
                        if ($request->hasFile('docket_slip')) {
                            $file = $request->file('docket_slip');
                            $filename = time() . '_bg_' . $bg->id . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('uploads/accounts'), $filename);
                            $bg->docket_slip = $filename;
                        }
                        $bg->save();
                        break;

                    case '7': // Request Cancellation
                        Log::info('BG Request Cancellation by ' . Auth::user()->name . ' for BG ' . $bg->emd_id);
                        $bg->action = $request->input('action');
                        if ($request->hasFile('stamp_covering_letter')) {
                            $file = $request->file('stamp_covering_letter');
                            $filename = time() . '_bg_stamp_covering_letter_' . $bg->id . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('uploads/accounts'), $filename);
                            $bg->stamp_covering_letter = $filename;
                        }
                        $bg->save();
                        break;

                    case '8': // BG Cancellation Confirmation
                        Log::info('BG Cancellation Confirmation by ' . Auth::user()->name . ' for BG ' . $bg->emd_id);
                        $bg->action = $request->input('action');
                        $bg->save();
                        break;

                    case '9': // FDR Cancellation Confirmation
                        Log::info('FDR Cancellation Confirmation by ' . Auth::user()->name . ' for BG ' . $bg->emd_id);
                        $bg->action = $request->input('action');
                        $bg->bg_fdr_cancel_date = $request->input('bg_fdr_cancel_date');
                        $bg->bg_fdr_cancel_amount = $request->input('bg_fdr_cancel_amount');
                        $bg->bg_fdr_cancel_ref_no = $request->input('bg_fdr_cancel_ref_no');
                        $bg->save();
                        break;

                    default:
                        return redirect()->back()->with('error', 'Invalid action selected.');
                }
                return redirect()->back()->with('success', 'Bank Guarantee status updated successfully.');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }
    public function BankTransferDashboard(Request $request, $id)
    {
        if ($request->isMethod('GET')) {
            try {
                $emd = BankTransfer::find($id);
                if (!$emd) {
                    return redirect()->back()->with('error', 'Bank Transfer record not found.');
                }
                $followups = FollowUps::where('emd_id', $id)->where('followup_for', 'Bank Transfer')->get();
                $followup_persons = FollowUpPersons::whereHas('followup', function ($query) use ($id) {
                    $query->where('followup_for', 'Bank Transfer')->where('emd_id', $id);
                })->get();
                return view('emds.emd-dashboard.bt-action', compact('emd', 'followups', 'followup_persons'));
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }

        if ($request->isMethod('PUT')) {
            try {
                $validator = $request->validate([
                    'action' => 'required',
                    'status' => 'nullable|string',
                    'reason' => 'nullable|string',
                    'utr' => 'nullable|string',
                    'remarks' => 'nullable|string',
                    'utr_mgs' => 'nullable|string',
                    'transfer_date' => 'nullable|date',
                    'utr_num' => 'nullable|string',
                ]);

                switch ($request->input('action')) {
                    case '1': // Accounts Form
                        Log::info('BT Account form initiated successfully by ' . Auth::user()->name . ' for BT id ' . $id);
                        $action = $request->input('action');
                        $status = $request->input('status');
                        $reason = $request->input('reason');
                        $utr = $request->input('utr');
                        $remarks = $request->input('remarks');
                        $utr_mgs = $request->input('utr_mgs');

                        $emd = BankTransfer::find($id);
                        $emd->action = $action;
                        $emd->status = $status;
                        $emd->reason = $reason;
                        $emd->utr = $utr;
                        $emd->remarks = $remarks;
                        $emd->utr_mgs = $utr_mgs;
                        $emd->save();

                        if ($emd->emd->tender_id != '0') {
                            // stop bt_acc_form timer
                            $tender = TenderInfo::where('id', $emd->emd->tender_id)->first();
                            $this->timerService->stopTimer($tender, 'bt_acc_form');
                        }

                        if ($this->btStatusMail($emd->emd_id)) {
                            Log::info('BT EMD status updated and mail sent successfully');
                        } else {
                            Log::info('BT Emd status updated but Failed to send mail');
                        }
                        break;

                    case '2': // Initiate Followup
                        Log::info('BT Initiate Followup initiated by ' . Auth::user()->name . ' For BT ID ' . $id);
                        $validator = Validator::make($request->all(), [
                            'org_name' => 'nullable|string|max:255',
                            'fp' => 'nullable|array',
                            'fp.*.name' => 'nullable|string|max:255',
                            'fp.*.phone' => 'nullable|digits_between:10,15',
                            'fp.*.email' => 'nullable|email|max:255',
                            'frequency' => 'required|in:1,2,3,4,5,6',
                            'stop_reason' => 'required_if:frequency,6|nullable|in:1,2,3,4',
                            'proof_text' => 'required_if:stop_reason,2|nullable|string|max:500',
                            'proof_img' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
                            'stop_rem' => 'required_if:stop_reason,4|nullable|string|max:500',
                        ], [
                            'stop_reason.required_if' => 'Stop reason is required when frequency is "Stop".',
                            'proof_text.required_if' => 'Please provide proof when stop reason is "Followup Objective achieved".',
                            'proof_img.image' => 'Proof image must be an image file.',
                            'proof_img.mimes' => 'Proof image must be a file of type: jpg, png, jpeg.',
                            'stop_rem.required_if' => 'Remarks are required when stop reason is "Remarks".',
                        ]);

                        if ($validator->fails()) {
                            return redirect()->back()->withErrors($validator)->withInput();
                        }

                        $emd = BankTransfer::find($id);
                        $emd->action = $request->input('action');
                        $emd->save();

                        $amount = $emd->bt_amount;
                        $org_name = $request->org_name;
                        $frequency = $request->frequency;
                        $stop_reason = $request->stop_reason;
                        $proof_text = $request->proof_text;
                        $stop_rem = $request->stop_rem;
                        $start = $request->start_date;
                        $assignee = $emd->emd->tender->team_member;
                        $area = $emd->emd->tender->team;

                        $proof_img = null;
                        if ($request->hasFile('proof_img')) {
                            $image = $request->file('proof_img');
                            $imageName = time() . '_' . str_replace(' ', '_', strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))) . '.' . $image->getClientOriginalExtension();
                            $image->move(public_path('uploads/accounts'), $imageName);
                            $proof_img = $imageName;
                        }

                        $followup = new FollowUps();
                        $followup->emd_id = $emd->emd_id;
                        $followup->area = $area == 'AC' ? 'AC Team' : 'DC Team';
                        $followup->followup_for = 'EMD Refund';
                        $followup->party_name = $org_name;
                        $followup->amount = $amount;
                        $followup->assigned_to = $assignee;
                        $followup->frequency = $frequency;
                        $followup->assign_initiate = 'Followup Assigned';
                        $followup->created_by = Auth::user()->id;
                        $followup->stop_reason = $stop_reason;
                        $followup->proof_text = $proof_text;
                        $followup->proof_img = $proof_img;
                        $followup->stop_rem = $stop_rem;
                        $followup->start_from = $start;
                        $followup->attachments = '["2025_cancelled_check_n_yes_bank_mandate.pdf"]';
                        $followup->save();

                        if ($request->has('fp') && is_array($request->fp)) {
                            foreach ($request->fp as $person) {
                                $fup = new FollowUpPersons();
                                $fup->follwup_id = $followup->id;
                                $fup->name = $person['name'] ?? null;
                                $fup->phone = $person['phone'] ?? null;
                                $fup->email = $person['email'] ?? null;
                                $fup->save();
                            }
                        }
                        Log::info('BT EMD Followup initiated successfully' . json_encode($followup));

                        if ((new FollowUpsController)->followupMail($followup->id)) {
                            return redirect()->back()->with('success', 'Followup initiated and mail sent to targeted persons successfully');
                        } else {
                            return redirect()->back()->with('error', 'Followup initiated successfully but mail not sent to targeted persons');
                        }

                    case '3': // Returned via Bank Transfer
                        Log::info('BT EMD Returned via Bank Transfer by ' . Auth::user()->name . ' with BT id ' . $id);
                        $action = $request->input('action');
                        $transfer_date = $request->input('transfer_date');
                        $utr_num = $request->input('utr_num');

                        $emd = BankTransfer::find($id);
                        $emd->action = $action;
                        $emd->transfer_date = $transfer_date;
                        $emd->utr_num = $utr_num;
                        $emd->save();

                        break;

                    case '4': // Settled with Project Account
                        Log::info('BT EMD Settled with Project Account by ' . Auth::user()->name . ' with BT id ' . $id);
                        $action = $request->input('action');

                        $emd = BankTransfer::find($id);
                        $emd->action = $action;
                        $emd->save();
                        break;

                    default:
                        return redirect()->back()->with('error', 'Invalid action selected.');
                }
                return redirect()->back()->with('success', 'Bank Transfer status updated successfully.');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }
    public function PayOnPortalDashboard(Request $request, $id)
    {
        // dd($request->all());
        // for get method
        if ($request->isMethod('get')) {
            $pop = PayOnPortal::find($id);
            $followup = FollowUps::where('emd_id', $id)->first();
            return view('emds.emd-dashboard.pop-action', compact('pop', 'followup'));
        }

        if ($request->isMethod('PUT')) {
            try {
                $validator = $request->validate([
                    'action' => 'required',
                    'status' => 'nullable|string',
                    'reason' => 'nullable|string',
                    'utr' => 'nullable|string',
                    'remarks' => 'nullable|string',
                    'utr_mgs' => 'nullable|string',
                    'transfer_date' => 'nullable|date',
                    'utr_num' => 'nullable|string',
                ]);

                switch ($request->input('action')) {
                    case '1': // Accounts Form
                        Log::info('POP Account Form initiated by ' . Auth::user()->name . ' For POP ID ' . $id);
                        $action = $request->input('action');
                        $status = $request->input('status');
                        $reason = $request->input('reason');
                        $utr = $request->input('utr');
                        $remarks = $request->input('remarks');
                        $utr_mgs = $request->input('utr_mgs');

                        $emd = PayOnPortal::find($id);
                        $emd->action = $action;
                        $emd->status = $status;
                        $emd->reason = $reason;
                        $emd->utr = $utr;
                        $emd->remarks = $remarks;
                        $emd->utr_mgs = $utr_mgs;
                        $emd->save();

                        // stop pop_acc_form timer
                        $tender = TenderInfo::where('id', $emd->emd->tender_id)->first();
                        $this->timerService->stopTimer($tender, 'pop_acc_form');

                        if ($this->popStatusMail($emd->emd_id)) {
                            Log::info('POP EMD status updated and mail sent successfully');
                        } else {
                            Log::info('POP Emd status updated but Failed to send mail');
                        }

                        break;

                    case '2': // Initiate Followup
                        Log::info('POP Followup initiated by ' . Auth::user()->name . ' with POP id ' . $id);
                        $validator = Validator::make($request->all(), [
                            'org_name' => 'nullable|string|max:255',
                            'fp' => 'nullable|array',
                            'fp.*.name' => 'nullable|string|max:255',
                            'fp.*.phone' => 'nullable|digits_between:10,15',
                            'fp.*.email' => 'nullable|email|max:255',
                            'frequency' => 'required|in:1,2,3,4,5,6',
                            'stop_reason' => 'required_if:frequency,6|nullable|in:1,2,3,4',
                            'proof_text' => 'required_if:stop_reason,2|nullable|string|max:500',
                            'proof_img' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
                            'stop_rem' => 'required_if:stop_reason,4|nullable|string|max:500',
                        ], [
                            'stop_reason.required_if' => 'Stop reason is required when frequency is "Stop".',
                            'proof_text.required_if' => 'Please provide proof when stop reason is "Followup Objective achieved".',
                            'proof_img.image' => 'Proof image must be an image file.',
                            'proof_img.mimes' => 'Proof image must be a file of type: jpg, png, jpeg.',
                            'stop_rem.required_if' => 'Remarks are required when stop reason is "Remarks".',
                        ]);

                        if ($validator->fails()) {
                            return redirect()->back()->withErrors($validator)->withInput();
                        }

                        $emd = PayOnPortal::find($id);
                        $emd->action = $request->input('action');
                        $emd->save();

                        $amount = $emd->amount;
                        $org_name = $request->org_name;
                        $frequency = $request->frequency;
                        $stop_reason = $request->stop_reason;
                        $proof_text = $request->proof_text;
                        $stop_rem = $request->stop_rem;
                        $start = $request->start_date;
                        $assignee = $emd->emd->tender->team_member;
                        $area = $emd->emd->tender->team;

                        $proof_img = null;
                        if ($request->hasFile('proof_img')) {
                            $image = $request->file('proof_img');
                            $imageName = time() . '_' . str_replace(' ', '_', strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))) . '.' . $image->getClientOriginalExtension();
                            $image->move(public_path('uploads/accounts'), $imageName);
                            $proof_img = $imageName;
                        }

                        $followup = new FollowUps();
                        $followup->emd_id = $emd->emd_id;
                        $followup->area = $area == 'AC' ? 'AC Team' : 'DC Team';
                        $followup->followup_for = 'EMD Refund';
                        $followup->party_name = $org_name;
                        $followup->amount = $amount;
                        $followup->assigned_to = $assignee;
                        $followup->frequency = $frequency;
                        $followup->assign_initiate = 'Followup Assigned';
                        $followup->created_by = Auth::user()->id;
                        $followup->stop_reason = $stop_reason;
                        $followup->proof_text = $proof_text;
                        $followup->proof_img = $proof_img;
                        $followup->stop_rem = $stop_rem;
                        $followup->start_from = $start;
                        $followup->attachments = '["2025_cancelled_check_n_yes_bank_mandate.pdf"]';
                        $followup->save();

                        if ($request->has('fp') && is_array($request->fp)) {
                            foreach ($request->fp as $person) {
                                $fup = new FollowUpPersons();
                                $fup->follwup_id = $followup->id;
                                $fup->name = $person['name'] ?? null;
                                $fup->phone = $person['phone'] ?? null;
                                $fup->email = $person['email'] ?? null;
                                $fup->save();
                            }
                        }
                        Log::info('POP EMD Followup initiated successfully' . json_encode($followup));

                        if ((new FollowUpsController)->followupMail($followup->id)) {
                            return redirect()->back()->with('success', 'Followup initiated and mail sent to targeted persons successfully');
                        } else {
                            return redirect()->back()->with('error', 'Followup initiated successfully but mail not sent to targeted persons');
                        }

                    case '3': // Returned via Bank Transfer
                        Log::info('POP EMD Returned via Bank Transfer called by ' . Auth::user()->name . ' with POP id ' . $id);
                        $action = $request->input('action');
                        $transfer_date = $request->input('transfer_date');
                        $utr_num = $request->input('utr_num');

                        $emd = PayOnPortal::find($id);
                        $emd->action = $action;
                        $emd->transfer_date = $transfer_date;
                        $emd->utr_num = $utr_num;
                        $emd->save();

                        break;

                    case '4': // Settled with Project Account
                        Log::info('POP EMD Settled with Project Account called by ' . Auth::user()->name . ' with POP id ' . $id);
                        $action = $request->input('action');

                        $emd = PayOnPortal::find($id);
                        $emd->action = $action;
                        $emd->save();
                        break;

                    default:
                        return redirect()->back()->with('error', 'Invalid action selected.');
                }
                return redirect()->back()->with('success', 'Pay On Portal status updated successfully.');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }
    public function show($id)
    {
        try {
            $emd = Emds::find($id);
            return view('emds.emd-dashboard.show', compact('emd'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function edit(Emds $emds, $id)
    {
        $emd = Emds::where('id', $id)->first();
        if (!$emd) {
            return redirect()->back()->with('error', 'EMD not found');
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
        return view('emds.emd-dashboard.edit', compact('emd', 'allData', 'ins', 'tender'));
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
                        'bg_needs' => 'required',
                        'bg_purpose' => 'required',
                        'bg_favour' => 'required',
                        'bg_address' => 'required',
                        'bg_expiry' => 'required|date',
                        'bg_claim' => 'required|date',
                        'bg_amt' => 'required|numeric',
                        'bg_bank' => 'required',
                        'bg_stamp' => 'required|numeric',
                        'bg_client_user' => 'required|email',
                        'bg_client_cp' => 'required|email',
                        'bg_client_fin' => 'required|email',
                        'bg_bank_name' => 'required',
                        'bg_bank_acc' => 'required',
                        'bg_bank_ifsc' => 'required',
                        'bg_courier_addr' => 'required',
                        'courier_deadline' => 'required',
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
            return redirect()->back()->with('success', 'EMD updated successfully');
        } catch (\Throwable $th) {
            Log::error('EMD Update Error:', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Log::info('Emd delete called by ' . Auth::user()->name . ' with id ' . $id);
            $emd = Emds::find($id);
            if ($emd) {
                // Delete the related records in the other tables
                switch ($emd->instrument_type) {
                    case '1':
                        EmdDemandDraft::where('emd_id', $id)->delete();
                        Log::info('EmdDemandDraft deleted successfully');
                        break;
                    case '2':
                        EmdFdr::where('emd_id', $id)->delete();
                        Log::info('EmdFdr deleted successfully');
                        break;
                    case '3':
                        EmdCheque::where('emd_id', $id)->delete();
                        Log::info('EmdCheque deleted successfully');
                        break;
                    case '4':
                        EmdBg::where('emd_id', $id)->delete();
                        Log::info('EmdBg deleted successfully');
                        break;
                    case '5':
                        BankTransfer::where('emd_id', $id)->delete();
                        Log::info('BankTransfer deleted successfully');
                        break;
                    case '6':
                        PayOnPortal::where('emd_id', $id)->delete();
                        Log::info('PayOnPortal deleted successfully');
                        break;
                }
                $emd->delete();
                Log::info('Emd deleted successfully');
                return redirect()->back()->with('success', 'EMD deleted successfully');
            } else {
                return redirect()->back()->with('error', 'EMD not found');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    // ===================== MAILS =====================

    public function bgAccountForm1Mail($id)
    {
        try {
            $bg = EmdBg::find($id);
            $ccRoles = User::whereIn('role', ['admin', 'team-leader', 'coordinator'])
                ->pluck('email', 'role')
                ->toArray();
            $ccMail = [
                $ccRoles['admin'] ?? null,
                $ccRoles['team-leader'] ?? 'abs.gyankr@gmail.com',
                $ccRoles['coordinator'] ?? null,
            ];
            $ccMail = array_filter($ccMail);
            $data = [
                'purpose' => $bg->bg_purpose,
                'in_favor_of' => $bg->bg_favour,
                'bg_address' => $bg->bg_address,
                'bg_needs' => $bg->bg_needs,
                'bg_expiry_date' => date('d-m-Y', strtotime($bg->bg_expiry)),
                'bg_claim_date' => date('d-m-Y', strtotime($bg->bg_claim)),
                'amount' => format_inr($bg->bg_amt),
                'bg_stamp' => format_inr($bg->bg_stamp),
                'bg_fdr_percent' => $bg->bg_fdr_percent,
                'beneficiary_name' => $bg->bg_bank_name,
                'account_no' => $bg->bg_bank_acc,
                'ifsc_code' => $bg->bg_bank_ifsc,
            ];
            $sender = User::where('email', 'imran@volksenergie.in')->first();
            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to('abs.gyankr@gmail.com')->cc($ccMail)->send(new BgAccountForm1Mail($data));
            return redirect()->back()->with('success', 'Mail sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function bgAccountForm2Mail($id)
    {
        try {
            $bg = EmdBg::find($id);
            $ccRoles = User::whereIn('role', ['admin', 'team-leader', 'coordinator'])
                ->pluck('email', 'role')
                ->toArray();
            $ccMail = [
                $ccRoles['admin'] ?? null,
                $ccRoles['team-leader'] ?? 'abs.gyankr@gmail.com',
                $ccRoles['coordinator'] ?? null,
            ];
            $ccMail = array_filter($ccMail);
            $data = [
                'purpose' => $bg->bg_purpose,
                'assignee' => $bg->emds->tender->users->name,
                'status' => $bg->status,
                'docket_no' => $bg->courier->docket_no,
                'courier_provider' => $bg->courier->courier_provider,
                'remarks' => $bg->remarks,
                'files' => [$bg->courier->docket_slip],
            ];
            $sender = User::where('email', 'imran@volksenergie.in')->first();
            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to($bg->emds->tender->users->email)->cc($ccMail)->send(new BgAccountForm2Mail($data));
            return redirect()->back()->with('success', 'Mail sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function bgAccountForm3Mail($id)
    {
        try {
            $bg = EmdBg::find($id);
            $ccRoles = User::whereIn('role', ['admin', 'team-leader', 'coordinator'])
                ->pluck('email', 'role')
                ->toArray();
            $ccMail = [
                $ccRoles['admin'] ?? null,
                $ccRoles['team-leader'] ?? 'abs.gyankr@gmail.com',
                $ccRoles['coordinator'] ?? null,
                $bg->emds->tender->users->email
            ];
            $ccMail = array_filter($ccMail);
            $data = [
                'bg_no' => $bg->bg_no,
                'purpose' => $bg->bg_purpose,
                'tender_no' => $bg->emds->tender_no,
                'expiry_date' => date('d-m-Y', strtotime($bg->bg_expiry)),
                'claim_date' => date('d-m-Y', strtotime($bg->bg_claim)),
                'favor' => $bg->bg_favour,
                'amount' => format_inr($bg->bg_amt),
                'bg_stamp' => format_inr($bg->bg_stamp),
                'courier_provider' => $bg->courier->courier_provider,
                'docket_no' => $bg->courier->docket_no,
                'files' => [$bg->courier->docket_slip],
                'attachments' => [$bg->sfms_conf, $bg->fdr_copy],
            ];
            $sender = User::where('email', 'imran@volksenergie.in')->first();
            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to('abs.gyankr@gmail.com')->cc($ccMail)->send(new BgAccountForm3Mail($data));
            return redirect()->back()->with('success', 'Mail sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function bgRequestExtensionMail($id)
    {
        try {
            $bg = EmdBg::find($id);

            Log::info('bgRequestExtensionMail', ['bg_data' => $bg->toArray()]);

            $ccRoles = User::whereIn('role', ['coordinator', 'team-leader'])->pluck('email', 'role')->toArray();
            $ccMail = [
                $ccRoles['coordinator'],
                $ccRoles['team-leader'],
                'accounts@volksenergie.in',
                'puja.gupta@yesbank.in',
                'dlcorpdesknehruplace@yesbank.in'
            ];
            $data = array_merge($bg->toArray(), [
                'soft_copy' => optional($bg->courier)->courier_docs ?? '',
            ]);

            Log::info('bgRequestExtensionMail Data', ['mail_data' => $data]);

            $ccMail = array_filter($ccMail);
            $to = ['divya.khurana1@yesbank.in', 'dhiraj.kumar3@yesbank.in', 'nikhil.abrol@yesbank.in'];

            Log::info('bgRequestExtensionMail Recipients', [
                'to' => $to,
                'cc' => $ccMail
            ]);

            MailHelper::configureMailer('goyal@volksenergie.in', 'jydjcdhjiuhmtwgq', 'Piyush Goyal');
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to('socialgyan69@gmail.com')
                // ->cc($ccMail)
                ->send(new BgRequestExtensionMail($data));

            Log::info('bgRequestExtensionMail Mail sent successfully.');
            return redirect()->back()->with('success', 'Mail sent successfully.');
        } catch (\Throwable $th) {
            Log::error('bgRequestExtensionMail failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function bgReturnedViaCourierMail($id)
    {
        try {
            $bg = EmdBg::find($id);
            $ccRoles = User::whereIn('role', ['admin', 'team-leader', 'coordinator'])
                ->pluck('email', 'role')
                ->toArray();
            $ccMail = [
                $ccRoles['admin'] ?? null,
                $ccRoles['team-leader'] ?? 'abs.gyankr@gmail.com',
                $ccRoles['coordinator'] ?? null,
                'accounts@volksenergie.in'
            ];
            $ccMail = array_filter($ccMail);
            $followup = FollowUps::where('emd_id', $id)->first();
            $followup_persons = FollowUpPersons::whereHas('followup', function ($query) use ($id) {
                $query->where('emd_id', $id);
            })->get();
            $data = [
                'bg' => $bg,
                'followup' => $followup,
                'followup_persons' => $followup_persons,
            ];
            $sender = User::where('email', 'imran@volksenergie.in')->first();
            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to('abs.gyankr@gmail.com')->cc($ccMail)->send(new BgReturnedCourierMail($data));
            return redirect()->back()->with('success', 'Mail sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function bgRequestCancellationMail($id)
    {
        try {
            $bg = EmdBg::find($id);
            $ccRoles = User::whereIn('role', ['admin', 'team-leader', 'coordinator'])
                ->pluck('email', 'role')
                ->toArray();
            $ccMail = [
                $ccRoles['admin'] ?? null,
                $ccRoles['team-leader'] ?? 'abs.gyankr@gmail.com',
                $ccRoles['coordinator'] ?? null,
                'accounts@volksenergie.in'
            ];
            $ccMail = array_filter($ccMail);
            $followup = FollowUps::where('emd_id', $id)->first();
            $followup_persons = FollowUpPersons::whereHas('followup', function ($query) use ($id) {
                $query->where('emd_id', $id);
            })->get();
            $data = [
                'bg' => $bg,
                'followup' => $followup,
                'followup_persons' => $followup_persons,
            ];
            $sender = User::where('email', 'imran@volksenergie.in')->first();
            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to('abs.gyankr@gmail.com')->cc($ccMail)->send(new BgRequestCancellationMail($data));
            return redirect()->back()->with('success', 'Mail sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function bgCancellationConfirmationMail($id)
    {
        try {
            $bg = EmdBg::find($id);
            $creator = User::where('name', 'LIKE', $bg->team_member . '%')->first();
            $ccRoles = User::where('team', $creator->team)->whereIn('role', ['team-leader', 'coordinator'])
                ->pluck('email', 'role')
                ->toArray();
            $ccMail = [
                $ccRoles['team-leader'] ?? 'abs.gyankr@gmail.com',
                $ccRoles['coordinator'] ?? null,
                'accounts@volksenergie.in'
            ];
            $ccMail = array_filter($ccMail);
            $data = [
                'beneficiary_name' => $bg->beneficiary_name,
                'bg_no' => $bg->beneficiary_name,
                'bg_date' => $bg->beneficiary_name,
                'bg_value' => $bg->beneficiary_name,
                'beneficiary_name' => $bg->beneficiary_name,
                'bg_amount' => $bg->beneficiary_name,
                'fdr_no' => $bg->beneficiary_name,
                'fdr_value' => $bg->beneficiary_name,
            ];
            $to = ['divya.khurana1@yesbank.in', 'Dhiraj.kumar3@yesbank.in', 'Nikhil.abrol@yesbank.in'];
            $sender = User::where('email', 'goyal@volksenergie.in')->first();
            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to($to)->cc($ccMail)->send(new BgCancellationMail($data));
            return redirect()->back()->with('success', 'Mail sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function bgFDRCancellationConfirmationMail($id)
    {
        try {
            $bg = EmdBg::find($id);
            $ccRoles = User::whereIn('role', ['admin', 'team-leader', 'coordinator'])
                ->pluck('email', 'role')
                ->toArray();
            $ccMail = [
                $ccRoles['admin'] ?? null,
                $ccRoles['team-leader'] ?? 'abs.gyankr@gmail.com',
                $ccRoles['coordinator'] ?? null,
                'accounts@volksenergie.in'
            ];
            $ccMail = array_filter($ccMail);
            $followup = FollowUps::where('emd_id', $id)->first();
            $followup_persons = FollowUpPersons::whereHas('followup', function ($query) use ($id) {
                $query->where('emd_id', $id);
            })->get();
            $data = [
                'bg' => $bg,
                'followup' => $followup,
                'followup_persons' => $followup_persons,
            ];
            $sender = User::where('email', 'imran@volksenergie.in')->first();
            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            Mail::mailer($mailer)->to('abs.gyankr@gmail.com')->send(new BgFDRCancellationMail($data));
            return redirect()->back()->with('success', 'Mail sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function popStatusMail($emdId)
    {
        try {
            $emd = Emds::find($emdId);
            $tender = TenderInfo::find($emd->tender_id);

            $teamMember = User::where('name', 'LIKE', $emd->requested_by . '%')->first();
            $recipientEmail = $teamMember->email ?? 'gyanprakashk55@gmail.com';
            $assignee = $teamMember->name ?? 'gyanprakash';

            $userRoles = User::where('team', 'DC')->whereIn('role', ['admin', 'team-leader', 'coordinator'])->pluck('email', 'role')->toArray();
            $adminMail = $userRoles['admin'] ?? 'gyanprakashk55@gmail.com';
            $tlMail = $userRoles['team-leader'] ?? 'gyanprakashk55@gmail.com';
            $cooMail = $userRoles['coordinator'] ?? 'gyanprakashk55@gmail.com';
            $pop = PayOnPortal::where('emd_id', $emdId)->first();
            $data = [
                'purpose' => $pop->purpose,
                'assignee' => $assignee,
                'tenderNo' => $tender ? $tender->tender_no : 'NA',
                'status' => $pop->status,
                'utr' => $pop->utr,
                'reason' => $pop->reason,
                'remarks' => $pop->remarks,
            ];
            Log::info("POP Status Mail Data: " . json_encode($data));

            $accountant = User::where('role', 'account-executive')->first();
            $accountantName = $accountant->name ?? 'Shivani';
            $accountantEmail = $accountant->email ?? 'shivani@volksenergie.in';
            $appPassword = $accountant->app_password ?? '12345678';

            MailHelper::configureMailer($accountantEmail, $appPassword, $accountantName);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

            Mail::mailer($mailer)->to($recipientEmail)
                ->cc([$cooMail, $adminMail, 'accounts@volksenergie.in', $tlMail])
                ->send(new PopStatusMail($data));

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("POP Status Mail Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    public function btStatusMail($emdId)
    {
        try {
            $emd = Emds::find($emdId);
            $tender = TenderInfo::find($emd->tender_id);
            $bt = BankTransfer::where('emd_id', $emdId)->first();

            $teamMember = User::where('name', 'LIKE', $emd->requested_by . '%')->first();
            Log::info("Team Member: " . json_encode($teamMember));
            $recipientEmail = $teamMember->email ?? 'gyanprakashk55@gmail.com';
            $assignee = $teamMember->name ?? 'gyanprakash';

            $userRoles = User::where('team', 'DC')->whereIn('role', ['admin', 'team-leader', 'coordinator'])->pluck('email', 'role')->toArray();
            $adminMail = $userRoles['admin'] ?? 'gyanprakashk55@gmail.com';
            $tlMail = $userRoles['team-leader'] ?? 'gyanprakashk55@gmail.com';
            $cooMail = $userRoles['coordinator'] ?? 'gyanprakashk55@gmail.com';
            $data = [
                'purpose' => $bt->purpose,
                'assignee' => $assignee,
                'tenderNo' => $tender ? $tender->tender_no : '',
                'tenderName' => $bt->project_name,
                'status' => $bt->status,
                'utr' => $bt->utr,
                'utr_message' => $bt->utr_message,
                'reason' => $bt->reason,
                'remarks' => $bt->remarks,
            ];
            Log::info("BT Status Mail Data: " . json_encode($data));

            $accountant = User::where('role', 'account-executive')->first();
            $accountantName = $accountant->name ?? 'Shivani';
            $accountantEmail = $accountant->email ?? 'shivani@volksenergie.in';
            $appPassword = $accountant->app_password ?? '12345678';

            MailHelper::configureMailer($accountantEmail, $appPassword, $accountantName);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

            Mail::mailer($mailer)->to($recipientEmail)
                ->cc([$cooMail, $adminMail, 'accounts@volksenergie.in', $tlMail])
                ->send(new BankTransferStatus($data));

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("BT Status Mail Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    public function chequeAccountForm($emdId)
    {
        try {
            $cheque = EmdCheque::where('id', $emdId)->first();
            $emd = Emds::find($cheque->emd_id);
            $tender = TenderInfo::find($emd->tender_id);

            $teamMember = User::find($tender->team_member);
            $assignee = $teamMember->name ?? 'Gyan Prakash';
            $assigneeMail = $teamMember->email ?? 'gyanprakashk55@gmail.com';

            $teamLeader = User::where('role', 'team-leader')->where('team', 'DC')->first();
            if (!$teamLeader) {
                $teamLeader = User::where('role', 'team-leader')->first();
            }
            $userRoles = User::where('team', 'DC')->whereIn('role', ['admin', 'team-leader'])->pluck('email', 'role')->toArray();
            $userRoles['team-leader'] = $teamLeader->email;
            $adminMail = $userRoles['admin'] ?? 'gyanprakashk55@gmail.com';
            $tlMail = $userRoles['team-leader'] ?? 'gyanprakashk55@gmail.com';

            $acc = User::where('role', 'account-executive')->where('email', 'kailash@volksenergie.in')->first();
            if (!$acc) {
                Log::error("Cheque Account Form Mail Error: Account Executive Record not found for Email: kailash@volksenergie.in");
            }
            $accMail = $acc->email ?? 'gyanprakashk55@gmail.com';
            $appPass = $acc->app_password ?? '12345678';
            $accName = $acc->name ?? 'Gyan Prakash';

            $data = [
                'purpose' => $cheque->cheque_reason,
                'assignee' => $assignee,
                'status' => $cheque->status,
                'remarks' => $cheque->remarks,
                'reason' => $cheque->reason,
                'files' => explode(',', $cheque->cheq_img)
            ];

            Log::info("Cheque Account Form Mail Data: " . json_encode($data));

            MailHelper::configureMailer($accMail, $appPass, $accName);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($assigneeMail)
                ->cc([$adminMail, $tlMail, 'accounts@volksenergie.in'])
                ->send(new ChequeAcForm($data));

            if ($mail) {
                Log::info("Cheque Account Form Mail Sent Successfully");
            } else {
                Log::error("Failed to send Cheque Account Form Mail" . $mail);
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("Cheque Account Form Mail Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    public function chequeStopMail($id)
    {
        try {
            $cheque = EmdCheque::find($id);

            $adminMail = User::where('role', 'admin')->where('team', 'DC')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->where('team', 'DC')->first()->email ?? 'gyanprakashk55@gmail.com';
            $coo = User::where('role', 'coordinator')->where('team', 'DC')->first();
            $cooMail = $coo->email ?? 'gyanprakashk55@gmail.com';
            $cooName = $coo->name ?? 'Gyan';
            $cooPass = $coo->app_password ?? '12345678';
            $to = 'kailash@volksenergie.in';

            $data = [
                'for' => $cheque->cheque_reason,
                'chequeNo' => $cheque->cheque_no,
                'partyName' => $cheque->cheque_favour,
                'amount' => $cheque->cheque_amt,
                'dueDate' => date('d-m-Y', strtotime($cheque->duedate)),
                'reason' => $cheque->stop_reason_text,
                'status' => $cheque->status,
            ];
            Log::info("Cheque Stop Mail Data: " . json_encode($data));

            MailHelper::configureMailer($cooMail, $cooPass, $cooName);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($to)
                ->cc([$adminMail, $tlMail, $cooMail])
                ->send(new ChequeStopMail($data));

            if ($mail) {
                Log::info("Cheque Stop Mail Sent Successfully");
            } else {
                Log::error("Failed to send Cheque Stop Mail" . $mail);
            }

            // return redirect()->back()->with('success', 'Cheque Stop Mail Sent Successfully');
        } catch (\Throwable $th) {
            Log::error("Cheque Stop Mail Error: " . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function chqDueDateReminder()
    {
        try {
            $cheques = EmdCheque::whereNull('dd_id')->get();

            Log::info("Cheque Due Date Reminder Data: " . json_encode($cheques));

            foreach ($cheques as $cheque) {
                $dueDate = Carbon::parse($cheque->duedate);
                $today = Carbon::now();
                $daysUntilDue = round($today->diffInDays($dueDate, false));

                $reminderDays = [15, 7, 3, 2, 1];
                Log::info("DD: {$dueDate} TD: {$today} DUD: {$daysUntilDue}");
                if (in_array($daysUntilDue, $reminderDays)) {
                    $admin = User::where('role', 'admin')->first();
                    $coo = User::where('role', 'coordinator')->first();

                    $data = [
                        'for' => $cheque->cheque_reason,
                        'chequeNo' => $cheque->cheq_no,
                        'amount' => format_inr($cheque->cheque_amt),
                        'dueDate' => date('d-m-Y', strtotime($cheque->duedate)),
                        'partyName' => $cheque->cheque_favour,
                        'daysLeft' => $daysUntilDue,
                        'purpose' => $cheque->cheque_reason
                    ];

                    Log::info("Cheque Due Date Reminder Mail Data: " . json_encode($data));


                    $cc = array_filter([
                        'accounts@volksenergie.in',
                        $admin->email ?? 'admin@volksenergie.in',
                        $coo->email ?? 'coo@volksenergie.in'
                    ]);

                    MailHelper::configureMailer($coo->email, $coo->app_password, $coo->name);
                    $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
                    Mail::mailer($mailer)
                        ->to('abs.gyankr@gmail.com')
                        ->cc($cc)
                        ->send(new ChequeDueDateReminder($data));

                    Log::info("Cheque due date reminder sent for Cheque #{$cheque->id}, {$daysUntilDue} days before due date");
                } else {
                    Log::info("Cheque due date reminder not sent for Cheque #{$cheque->id}, {$daysUntilDue} days before due date");
                }
            }

            return response()->json(['success' => true, 'message' => 'Cheque due date reminders processed successfully']);
        } catch (\Throwable $th) {
            Log::error("Cheque DueDate Reminder Mail Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    public function DdChqAccept($id, $pdfFiles)
    {
        try {
            $ddChq = EmdCheque::find($id);
            Log::info('DD/Chq: ' . json_encode($ddChq));

            $sender = User::where('email', 'shivani@volksenergie.in')->first();
            // $ccRoles = User::where('team', 'DC')
            $ccRoles = User::whereIn('role', ['admin', 'team-leader', 'coordinator'])
                ->pluck('email', 'role')
                ->toArray();
            $ccMail = [
                $ccRoles['admin'] ?? null,
                $ccRoles['team-leader'] ?? 'abs.gyankr@gmail.com',
                $ccRoles['coordinator'] ?? null,
                'accounts@volksenergie.in'
            ];
            $ccMail = array_filter($ccMail); // Remove null values

            // =Tender due date time - Expected Courier Delivery time
            $tender = TenderInfo::find($ddChq->emds->tender_id)->first();
            $dueDT = "$tender->due_date $tender->due_time";
            $eCD = time() - $ddChq->chqDd->courier_deadline * 3600;
            Log::info("$dueDT - $eCD = " . (strtotime($dueDT) - $eCD));
            $remainingHrs = (strtotime($dueDT) - $eCD) / 3600;
            $hrs = (int) floor($remainingHrs);
            $data = [
                'purpose' => $ddChq->cheque_reason,
                'cheque_no' => $ddChq->cheq_no,
                'due_date' => date('d-m-Y', strtotime($ddChq->duedate)),
                'amount' => $ddChq->cheque_amt,
                'time_limit' => $hrs,
                'beneficiary_name' => 'YESBANK',
                'payable_at' => $ddChq->chqDd->dd_payable,
                'courier_address' => $ddChq->chqDd->courier_add,
                'link' => route('dd-action', $ddChq->dd_id),
                'files' => explode(',', $ddChq->cheq_img),
                'pdf' => $pdfFiles
            ];
            Log::info('DD/Chq Accept Mail Data: ' . json_encode($data));

            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to(['kailash@volksenergie.in'])
                ->cc($ccMail)
                ->send(new DdChqAcceptMail($data));

            if ($mail) {
                Log::info("DD/Chq Accept Mail Sent Successfully");
                foreach ($pdfFiles as $file) {
                    Log::info("Deleting temp file: " . storage_path('app/temp/' . basename($file)));
                    Storage::delete('temp/' . basename($file));
                }
            } else {
                Log::error("Failed to send DD/Chq Accept Mail" . $mail);
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("DD/Chq Accept Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    public function DdChqReject($id)
    {
        try {
            $ddChq = EmdCheque::find($id);

            $sender = User::where('email', 'shivani@volksenergie.in')->first();
            $ccRoles = User::where('team', 'DC')
                ->whereIn('role', ['admin', 'team-leader', 'coordinator'])
                ->pluck('email', 'role')
                ->toArray();
            $ccMail = [
                $ccRoles['admin'] ?? null,
                $ccRoles['team-leader'] ?? null,
                $ccRoles['coordinator'] ?? null,
                'accounts@volksenergie.in'
            ];
            $ccMail = array_filter($ccMail); // Remove null values

            $te_name = User::where('id', $ddChq->emds->tender->team_member)->first()->name;
            $te_email = User::where('id', $ddChq->emds->tender->team_member)->first()->email;

            $data = [
                'purpose' => $ddChq->cheque_reason,
                'te_name' => $te_name,
                'reason' => $ddChq->reason,
            ];

            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($te_email)
                ->cc($ccMail)
                ->send(new DdChqRejMail($data));

            if ($mail) {
                Log::info("DD/Chq Reject Mail Sent Successfully");
            } else {
                Log::error("Failed to send DD/Chq Reject Mail" . $mail);
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("DD/Chq Reject Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    public function ddAccountForm($id)
    {
        try {
            $dd = EmdDemandDraft::find($id);
            Log::info('DD/Chq: ' . json_encode($dd));

            $sender = User::where('email', 'shivani@volksenergie.in')->first();
            // $ccRoles = User::where('team', 'DC')
            $ccRoles = User::whereIn('role', ['admin', 'team-leader', 'coordinator'])
                ->pluck('email', 'role')
                ->toArray();
            $ccMail = [
                $ccRoles['admin'] ?? null,
                $ccRoles['team-leader'] ?? 'abs.gyankr@gmail.com',
                $ccRoles['coordinator'] ?? null,
                'accounts@volksenergie.in'
            ];
            $ccMail = array_filter($ccMail);

            $te_name = User::where('id', $dd->emd->tender->team_member)->first()->name;
            $te_email = User::where('id', $dd->emd->tender->team_member)->first()->email;

            $data = [
                'purpose' => $dd->cheque_reason,
                'te_name' => $te_name,
                'dd_date' => date('d-m-Y', strtotime($dd->dd_date)),
                'dd_no' => $dd->dd_no,
                'beneficiary_name' => 'YESBANK',
                'payable_at' => $dd->dd_payable,
                'amount' => format_inr($dd->dd_amt),
                'docket_no' => $dd->courier->docket_no,
                'status' => $dd->status,
                'remarks' => $dd->remarks,
                'files' => $dd->courier->courier_docs
            ];

            Log::info("DD/Chq Account Form Mail Data: ", $data);

            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($te_email)
                ->cc($ccMail)
                ->send(new DdAccountFormMail($data));

            if ($mail) {
                Log::info("DD/Chq Account Form Mail Sent Successfully");
            } else {
                Log::error("Failed to send DD/Chq Account Form Mail" . $mail);
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('DD/Chq Account Form Error: ' . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
    public function ddCancelling($id, $pdfFiles)
    {
        try {
            $dd = EmdDemandDraft::find($id);
            $sender = User::where('email', 'shivani@volksenergie.in')->first();
            // $ccRoles = User::where('team', 'DC')
            $ccRoles = User::whereIn('role', ['admin', 'team-leader', 'coordinator'])
                ->pluck('email', 'role')
                ->toArray();
            $ccMail = [
                $ccRoles['admin'] ?? null,
                $ccRoles['team-leader'] ?? 'abs.gyankr@gmail.com',
                $ccRoles['coordinator'] ?? null,
                'accounts@volksenergie.in'
            ];
            $ccMail = array_filter($ccMail);
            $to = ['Dhiraj.kumar3@yesbank.in', 'divya.khurana1@yesbank.in', 'dtcorpdesknehruplace@yesbank.in'];

            $data = [
                'purpose' => $dd->dd_purpose,
                'dd_no' => $dd->dd_no,
                'beneficiary_name' => 'YESBANK',
                'amount' => format_inr($dd->dd_amt),
                'pdf' => $pdfFiles,
            ];

            Log::info("DD/Chq Cancelling Mail Data: " . json_encode($data));

            MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($to)
                ->cc($ccMail)
                ->send(new DdCancellationMail($data));

            if ($mail) {
                Log::info("DD/Chq Cancelling Mail Sent Successfully");
                foreach ($pdfFiles as $file) {
                    Log::info("Deleting temp file: " . storage_path('app/temp/' . basename($file)));
                    Storage::delete('temp/' . basename($file));
                }
            } else {
                Log::error("Failed to send DD/Chq Cancelling Mail" . $mail);
            }
        } catch (\Throwable $th) {
            Log::error('DD/Chq Cancelling Error: ' . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    public function bgClaimPeriodMail($id)
    {
        try {
            $bg = EmdBg::find($id);
            if (!$bg) {
                return response()->json(['success' => false, 'error' => 'BG not found']);
            }
            $tender = TenderInfo::find($bg->emds->tender_id);
            if (!$tender) {
                $user = User::where('name', 'LIKE', $bg->emds->requested_by . '%')->first();
            } else {
                $user = User::where('id', $tender->team_member)->first();
            }


            // Check if BG has crossed expiry date
            $currentDate = now();
            $bgExpiryDate = Carbon::parse($bg->bg_expiry);

            if ($currentDate->lte($bgExpiryDate)) {
                return response()->json(['success' => false, 'message' => 'BG has not crossed expiry date yet']);
            }

            $admin = User::where('role', 'admin')->where('team', $user->team)->first();
            $coo = User::where('role', 'coordinator')->where('team', $user->team)->first();
            $cc = array_filter([
                'accounts@volksenergie.in',
                $admin->email,
            ]);

            $data = [
                'assignee' => $user->name,
                'bg_no' => $bg->bg_no,
                'bg_validity' => date('d-m-Y', strtotime($bg->bg_validity)),
                'bg_claim_period_expiry' => date('d-m-Y', strtotime($bg->claim_expiry)),
                'favor' => $bg->bg_favour,
                'amount' => format_inr($bg->bg_amt),
                'form_link' => route('bg-action', $bg->id)
            ];
            Log::info('BG Reminder Mail Data: ' . json_encode($data));
            MailHelper::configureMailer($coo->email, $coo->app_password, $coo->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)
                ->to($user->email)
                ->cc($cc)
                ->send(new BgClaimPeriodMail($data));
            if ($mail) {
                Log::info("Claim period notification sent successfully for BG #{$bg->id}");
            } else {
                Log::error("Failed to send claim period notification for BG #{$bg->id}");
            }

            return response()->json(['success' => true, 'message' => 'Claim period notification sent successfully']);
        } catch (\Exception $th) {
            Log::error('Claim Period Mail Error: ' . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    public function bgExpiryReminder()
    {
        try {
            $bgs = EmdBg::all();

            foreach ($bgs as $bg) {
                if ($bg->bg_expiry == null) {
                    Log::info("BG Expiry Date is null for BG #{$bg->id}");
                    continue;
                }
                $dueDate = Carbon::parse($bg->bg_expiry);
                $today = Carbon::now();
                $daysUntilDue = round($today->diffInDays($dueDate, false));
                // print all tree values in log
                Log::info("DD: {$dueDate} TD: {$today} DUD: {$daysUntilDue}");
                $tender = TenderInfo::find($bg->emds->tender_id);
                if (!$tender) {
                    $user = User::where('name', 'LIKE', $bg->emds->requested_by . '%')->first();
                } else {
                    $user = User::where('id', $tender->team_member)->first();
                }
                Log::info("Assignee: " . $user->name);
                $reminderDays = [90, 60, 45, 30, 15, 7, 1];

                if (in_array($daysUntilDue, $reminderDays)) {
                    $admin = User::where('role', 'admin')->where('team', $user->team)->first();
                    $coo = User::where('role', 'coordinator')->where('team', $user->team)->first();

                    $data = [
                        'days' => $daysUntilDue,
                        'assignee' => $user->name,
                        'bg_no' => $bg->bg_no,
                        'bg_validity' => date('d-m-Y', strtotime($bg->bg_validity)),
                        'bg_claim_period_expiry' => date('d-m-Y', strtotime($bg->claim_expiry)),
                        'favor' => $bg->bg_favour,
                        'amount' => format_inr($bg->bg_amt),
                        'form_link' => route('bg-action', $bg->id)
                    ];
                    Log::info('BG Reminder Mail Data: ' . json_encode($data));
                    MailHelper::configureMailer($coo->email, $coo->app_password, $coo->name);

                    $cc = array_filter([
                        'accounts@volksenergie.in',
                        $admin->email,
                    ]);

                    // Send reminder email
                    $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
                    Mail::mailer($mailer)
                        ->to($user->email)
                        ->cc($cc)
                        ->send(new BgReminderMail($data));

                    Log::info("Bank Guarantee due date reminder sent for Bank Guarantee #{$bg->id}, {$daysUntilDue} days before due date");
                } else {
                    Log::info("Bank Guarantee due date reminder not sent for Bank Guarantee #{$bg->id}, {$daysUntilDue} days before due date");
                }
            }

            return response()->json(['success' => true, 'message' => 'Bank Guarantee due date reminders processed successfully']);
        } catch (\Throwable $th) {
            Log::error("Bank Guarantee DueDate Reminder Mail Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
}

// ALTER TABLE emd_bgs
//   ADD COLUMN new_stamp_charge_deducted DECIMAL(10,2) NULL AFTER generated_pdfs,
//   ADD COLUMN new_bg_bank_name VARCHAR(255) NULL AFTER new_stamp_charge_deducted,
//   ADD COLUMN new_bg_amt DECIMAL(15,2) NULL AFTER new_bg_bank_name,
//   ADD COLUMN new_bg_expiry DATE NULL AFTER new_bg_amt,
//   ADD COLUMN new_bg_claim DATE NULL AFTER new_bg_expiry;
