<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelper;
use App\Mail\CostingApprovedMail;
use App\Mail\CostingRejectedMail;
use App\Models\VendorOrg;
use App\Models\Rfq;
use App\Models\TenderInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\TimerService;
use Illuminate\Validation\Rule;

class CostingApprovalController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function index()
    {
        $pendingTenders = TenderInfo::with('users')
            ->whereHas('sheet', function ($q) {
                $q->whereNotNull('final_price');
            })
            ->where('tlStatus', '1')
            ->where('deleteStatus', '0')
            ->where('costing_status', null)
            ->orWhere('costing_status', 'Rejected/Redo')
            ->orderBy('due_date', 'desc')
            ->get();

        $approvedTenders = TenderInfo::with('users')
            ->whereHas('sheet', function ($q) {
                $q->whereNotNull('final_price');
            })
            ->where('tlStatus', '1')
            ->where('deleteStatus', '0')
            ->where('costing_status', 'Approved')
            ->orderBy('due_date', 'desc')
            ->get();

        // dd($pendingTenders, $approvedTenders);
        $oems = VendorOrg::all();
        return view('costing_approval.index', compact('pendingTenders', 'approvedTenders', 'oems'));
    }

    public function approveSheet(Request $request, $id)
    {
        Log::info("📄 Starting costing sheet approval process for Tender ID: $id");

        try {
            $validated = $request->validate([
                'costing_status' => ['required', Rule::in(['Approved', 'Rejected/Redo'])],
                'final_price' => 'required_if:costing_status,Approved|numeric|min:0',
                'receipt' => 'required_if:costing_status,Approved|numeric|min:0',
                'budget' => 'required_if:costing_status,Approved|numeric|min:0',
                'gross_margin' => 'required_if:costing_status,Approved|numeric|min:0|max:100',
                'oem' => 'required_if:costing_status,Approved|array|min:1',
                'costing_remarks' => 'nullable|string|max:255',
            ]);

            $tender = TenderInfo::findOrFail($id);

            $tender->costing_status = $validated['costing_status'];
            $tender->costing_remarks = $validated['costing_remarks'] ?? null;
            $tender->status = 7;
            $tender->save();

            $tender->sheet->update([
                'final_price' => $validated['final_price'],
                'receipt' => $validated['receipt'],
                'budget' => $validated['budget'],
                'gross_margin' => $validated['gross_margin'],
                'oem' => implode(',', $validated['oem']),
            ]);

            Log::info('✅ Costing sheet status updated', [
                'tender_id' => $tender->id,
                'status' => $tender->costing_status,
            ]);

            if ($tender->costing_status === 'Approved') {
                $this->timerService->stopTimer($tender, 'costing_sheet_approval');

                // countdown to 24 hours before the tender due date and time
                $dueDate = new \Carbon\Carbon("{$tender->due_date} {$tender->due_time}");
                $cutoffDate = (clone $dueDate)->subHours(24); // Timer hits zero here
                $now = \Carbon\Carbon::now();

                // This gives positive or negative hours naturally
                $hrs = $now->diffInHours($cutoffDate, false);

                Log::info('Due Date: ' . $dueDate->toDateTimeString() .
                    ' | Cutoff Date: ' . $cutoffDate->toDateTimeString() .
                    ' | Current Time: ' . $now->toDateTimeString() .
                    ' | Hours until/since cutoff: ' . $hrs);

                $this->timerService->startTimer($tender, 'bid_submission', $hrs);

                $tender->status = 7;
                $tender->save();

                // $this->CostingApproval($tender->id);
            } elseif ($tender->costing_status === 'Rejected/Redo') {
                $this->CostingRejected($tender->id);

                // In case of "Reject/Redo Costing", entry deleted and tender sent back to "Costing sheet Pending" and timer in that dashboards starts wherever it had stoped.
                $this->timerService->deleteTimer($tender, 'costing_sheet_approval');
                $this->timerService->restartTimer($tender, 'costing_sheet');

                $tender->costing_status = null;
                $tender->costing_remarks = null;
                $tender->status = 3;
                $tender->save();

                if ($tender->sheet) {
                    $tender->sheet->final_price = null;
                    $tender->sheet->budget = null;
                    $tender->sheet->gross_margin = null;
                    $tender->sheet->remarks = null;
                    $tender->sheet->save();
                }
            }

            return redirect()->back()->with(
                'success',
                'Costing sheet ' . strtolower($tender->costing_status) . ' successfully'
            );
        } catch (\Throwable $e) {
            Log::error("❌ Costing Approval Error: " . $e->getMessage(), [
                'tender_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Error updating costing sheet: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $tender = TenderInfo::findOrFail($id);
            $rfq = Rfq::where('tender_id', $id)->first();
            return view('costing_approval.show', compact('tender', 'rfq'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    // === MAILS ===

    public function CostingApproval($id)
    {
        try {
            $tl = User::where('role', 'team-leader')->firstOrFail();
            $tlMail = $tl->email;
            $tlName = $tl->name;
            $tlPass = $tl->app_password;

            $ceo = User::where('role', 'admin')->firstOrFail();
            $ceoMail = $ceo->email;

            $tender = TenderInfo::findOrFail($id);
            $te = User::where('id', $tender->team_member)->firstOrFail();
            $teMail = $te->email;

            $data = [
                'te_name' => $te->name,
                'tender_name' => $tender->tender_name,
                'costing_sheet_link' => $tender->sheet->driveid,
                'due_date_time' => date('d-m-Y', strtotime($tender->due_date)) . ' ' . date('h:i A', strtotime($tender->due_time)),
                'tl_name' => $tlName,
                'tender_value' => format_inr($tender->gst_values),
                'approved_final_price' => format_inr($tender->sheet->final_price),
                'remarks' => $tender->costing_remarks,
            ];

            MailHelper::configureMailer($tlMail, $tlPass, $tlName);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

            $mail = Mail::mailer($mailer)->to($teMail)
                ->cc([$ceoMail])->send(new CostingApprovedMail($data));

            if ($mail) {
                Log::info('Costing Approved Mail Success: ' . $mail);
            } else {
                Log::info('Costing Approved Mail Failed');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error("Costing Approved Error: " . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function CostingRejected($id)
    {
        try {
            $tl = User::where('role', 'team-leader')->firstOrFail();
            $tlMail = $tl->email;
            $tlName = $tl->name;
            $tlPass = $tl->app_password;

            $ceo = User::where('role', 'admin')->firstOrFail();
            $ceoMail = $ceo->email;

            $tender = TenderInfo::findOrFail($id);
            $te = User::where('id', $tender->team_member)->firstOrFail();
            $teMail = $te->email;

            $data = [
                'teName' => $te->name,
                'tenderName' => $tender->tender_name,
                'costingSheetLink' => $tender->sheet->driveid,
                'dueDate' => date('d-m-Y', strtotime($tender->due_date)),
                'dueTime' => date('h:i A', strtotime($tender->due_time)),
                'tlName' => $tlName,
            ];

            MailHelper::configureMailer($tlMail, $tlPass, $tlName);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

            $mail = Mail::mailer($mailer)->to($teMail)
                ->cc([$ceoMail])->send(new CostingRejectedMail($data));

            if ($mail) {
                Log::info('Costing Rejected Mail Success: ' . $mail);
            } else {
                Log::info('Costing Rejected Mail Failed');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error("Costing Rejected Error: " . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
