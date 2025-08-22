<?php

namespace App\Http\Controllers;

use App\Models\Rfq;
use App\Models\User;
use App\Models\VendorOrg;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;
use App\Services\TimerService;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Mail\CostingApprovedMail;
use App\Mail\CostingRejectedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class CostingApprovalController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function index()
    {
        $oems = VendorOrg::all();
        return view('costing_approval.index', compact('oems'));
    }

    public function getCostingApproval(Request $request, $type)
    {
        $user = Auth::user();
        $team = $request->input('team');
        Log::info("getCostingSheet: Starting with type=$type and team=$team");

        $query = TenderInfo::with('users')
            ->whereHas('sheet', function ($q) {
                $q->whereNotNull('final_price');
            })
            ->where('tlStatus', '1')
            ->where('deleteStatus', '0')
            ->whereNotIn('status', ['8', '9', '10', '11', '12', '13', '14', '15', '38', '39']);

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

        // Pending or Submitted logic
        if ($type === 'pending') {
            $query->where('costing_status', null);
        } elseif ($type === 'submitted') {
            $query->where('costing_status', 'Approved');
        } elseif ($type === 'rejected') {
            $query->where('costing_status', 'Rejected/Redo');
        }

        // $query->orderByDesc('due_date');
        // Order by due_date
        if (!$request->filled('order')) {
            $query->orderByDesc('due_date');
        }

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('tender_name', 'like', "%{$search}%")
                            ->orWhere('tender_no', 'like', "%{$search}%")
                            ->orWhere('due_date', 'like', "%{$search}%")
                            ->orWhereHas('users', function ($uq) use ($search) {
                                $uq->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('statuses', function ($sq) use ($search) {
                                $sq->where('name', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->addColumn('tender_name', function ($tender) {
                return "<strong>{$tender->tender_name}</strong> <br>
            <span class='text-muted'>{$tender->tender_no}</span>";
            })
            ->addColumn('users.name', function ($tender) {
                return optional($tender->users)->name ?? 'N/A';
            })
            ->addColumn('due_date', function ($tender) {
                return '<span class="d-none">' . strtotime($tender->due_date) . '</span>' .
                    date('d-m-Y', strtotime($tender->due_date)) . '<br>' .
                    (isset($tender->due_time) ? date('h:i A', strtotime($tender->due_time)) : '');
            })
            ->addColumn('emd', function ($tender) {
                return format_inr($tender->emd);
            })
            ->addColumn('tender_value', function ($tender) {
                return format_inr($tender->gst_values);
            })
            ->addColumn('final_price', function ($tender) {
                return format_inr(optional($tender->sheet)->final_price) ?? '-';
            })
            ->addColumn('budget', function ($tender) {
                return format_inr(optional($tender->sheet)->budget) ?? '-';
            })
            ->addColumn('gross_margin', function ($tender) {
                return (optional($tender->sheet)->gross_margin ? optional($tender->sheet)->gross_margin . '%' : '-');
            })
            ->addColumn('status', function ($tender) {
                return $tender->statuses ? $tender->statuses->name : '-';
            })
            ->addColumn('timer', function ($tender) use ($type) {
                return view('partials.costing-approval-timer', ['tdr' => $tender])->render();
            })
            ->addColumn('action', function ($tender) use ($type) {
                return view('partials.costing-approval-action', ['tdr' => $tender])->render();
            })
            ->rawColumns(['tender_name', 'due_date', 'timer', 'action'])
            ->make(true);
    }

    public function approveSheet(Request $request, $id)
    {
        Log::info('📄 Starting costing sheet approval process for Tender ID: ' . $id);

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

                $this->CostingApproval($tender->id);
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
            $tender = TenderInfo::findOrFail($id);
            $te = User::where('id', $tender->team_member)->firstOrFail();
            $teMail = $te->email;

            $tl = User::where('role', 'team-leader')->where('team', $te->team)->firstOrFail();
            $tlMail = $tl->email;
            $tlName = $tl->name;
            $tlPass = $tl->app_password;

            $ceo = User::where('role', 'admin')->where('team', $te->team)->firstOrFail();
            $ceoMail = $ceo->email;

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
            
            Mail::mailer($mailer)->to($teMail)
                ->cc([$ceoMail])->send(new CostingApprovedMail($data));
            
            Log::info('Costing Approved Mail sent successfully to ' . $teMail);

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

            Mail::mailer($mailer)->to($teMail)
                ->cc([$ceoMail])->send(new CostingRejectedMail($data));
            
            // Log a simple success message
            Log::info('Costing Rejected Mail sent successfully to ' . $teMail);

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error("Costing Rejected Error: " . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
