<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Rfq;
use App\Models\User;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;
use App\Models\BidSubmission;
use App\Services\TimerService;
use Yajra\DataTables\DataTables;
use App\Mail\BidSubmissionMissed;
use Illuminate\Support\Facades\Log;
use App\Mail\BidSubmissionSubmitted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class BidSubmissionController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function index()
    {
        return view('bid_submission.index');
    }

    public function getBidSubmission(Request $request, $type)
    {
        $user = Auth::user();
        $team = $request->input('team');
        Log::info("getCostingSheet: Starting with type=$type and team=$team");

        $query = TenderInfo::with('bs')
            ->where('tlStatus',  '1')
            ->whereNotIn( 'status', ['8', '9', '10', '11', '12', '13', '14', '15', '38', '39'])
            ->where('deleteStatus',  '0');

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
            $query->whereDoesntHave('bs');
        } elseif ($type === 'submitted') {
            $query->whereHas('bs');
        }

        $query->orderByDesc('due_date');

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
            ->addColumn('status', function ($tender) {
                return $tender->bs ? $tender->bs->status : 'Submission Pending';
            })
            ->addColumn('timer', function ($tender) use ($type) {
                return view('partials.bid-submission-timer', ['tdr' => $tender])->render();
            })
            ->addColumn('action', function ($tender) use ($type) {
                return view('partials.bid-submission-action', ['tdr' => $tender])->render();
            })
            ->rawColumns(['tender_name', 'due_date', 'timer', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        try {
            $tender = TenderInfo::where('id', $id)->first();
            $rfq = Rfq::where('tender_id', $id)->first();
            return view('bid_submission.show', compact('tender', 'rfq'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function submitBid(Request $request, $id)
    {
        Log::info('Starting bid submission process');

        try {
            $validated = $request->validate([
                'tender_id' => 'required',
                'submission_datetime' => 'required|date',
                'bid_documents' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpeg,jpg,png',
                'submission_proof' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpeg,jpg,png',
                'final_price' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpeg,jpg,png'
            ]);

            // Handle file uploads
            $paths = [];
            $files = [];

            if ($request->hasFile('bid_documents')) {
                $files['bid_documents'] = 'bid_docs';
            }

            if ($request->hasFile('submission_proof')) {
                $files['submission_proof'] = 'submission_proof';
            }

            if ($request->hasFile('final_price')) {
                $files['final_price'] = 'final_price';
            }
            $attachment = [];

            foreach ($files as $input => $prefix) {
                if ($request->hasFile($input)) {
                    $file = $request->file($input);
                    $filename = $prefix . '_' . time() . '_' . $file->getClientOriginalName();
                    $path = $file->move(public_path('bid_submissions'), $filename);
                    $paths[$input] = $filename;
                }
            }

            // Check if bid submission exists
            $bidSubmission = BidSubmission::where('tender_id', $request->tender_id)->first();

            if ($bidSubmission) {
                // Update existing record
                $bidSubmission->update([
                    'bid_submissions_date' => $request->submission_datetime,
                    'submitted_bid_documents' => $paths['bid_documents'] ?? '',
                    'proof_of_submission' => $paths['submission_proof'],
                    'final_bidding_price' => $paths['final_price'],
                    'status' => 'Bid Submitted'
                ]);
            } else {
                // Create new record
                $bidSubmission = BidSubmission::create([
                    'tender_id' => $request->tender_id,
                    'bid_submissions_date' => $request->submission_datetime,
                    'submitted_bid_documents' => $paths['bid_documents'] ?? '',
                    'proof_of_submission' => $paths['submission_proof'],
                    'final_bidding_price' => $paths['final_price'],
                    'status' => 'Bid Submitted'
                ]);
            }

            $attachment = [$bidSubmission->proof_of_submission, $bidSubmission->final_bidding_price];
            // Send email notification
            if ($this->bidSubmitted($bidSubmission->id, $attachment)) {
                Log::info('Bid submission email sent successfully');
            } else {
                Log::error('Failed to send bid submission email');
            }

            // update tender status to 17
            TenderInfo::where('id', $request->tender_id)->update(['status' => 17]);
            $tender = TenderInfo::where('id', $request->tender_id)->first();
            $this->timerService->stopTimer($tender, 'bid_submission');

            Log::info('Bid submission completed successfully');
            return redirect()->back()->with('success', 'Bid submitted successfully');
        } catch (Exception $e) {
            Log::error("Bid Submission Error: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error submitting bid: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function markAsMissed(Request $request, $id)
    {
        Log::info('Starting tender missed process');

        try {
            $validated = $request->validate([
                'tender_id' => 'required',
                'reason' => 'required|string',
                'prevention_steps' => 'required|string',
                'system_improvements' => 'required|string'
            ]);

            // Check if bid submission exists
            $bidSubmission = BidSubmission::where('tender_id', $request->tender_id)->first();

            if ($bidSubmission) {
                // Update existing record
                $bidSubmission->update([
                    'reason_for_missing' => $request->reason,
                    'not_repeat_reason' => $request->prevention_steps,
                    'tms_improvements' => $request->system_improvements,
                    'status' => 'Tender Missed'
                ]);
            } else {
                // Create new record
                $bidSubmission = BidSubmission::create([
                    'tender_id' => $request->tender_id,
                    'reason_for_missing' => $request->reason,
                    'not_repeat_reason' => $request->prevention_steps,
                    'tms_improvements' => $request->system_improvements,
                    'status' => 'Tender Missed'
                ]);
            }

            // Send email notification
            if ($this->bidMissed($bidSubmission->id)) {
                Log::info('Tender missed email sent successfully');
            } else {
                Log::error('Failed to send tender missed email');
            }

            // update tender status to 8
            TenderInfo::where('id', $request->tender_id)->update(['status' => 8]);

            Log::info('Tender marked as missed successfully');
            return redirect()->back()->with('success', 'Tender marked as missed successfully');
        } catch (Exception $e) {
            Log::error("Tender Missed Error: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error marking tender as missed: ' . $e->getMessage())
                ->withInput();
        }
    }

    // === MAILS ===

    public function bidSubmitted($id, $files)
    {
        try {
            Log::info("Bid submission process started for Bid ID: $id");

            $bid = BidSubmission::find($id);
            if (!$bid) {
                Log::error("❌ Bid not found for ID: $id");
                return;
            }

            Log::info("✔ Bid retrieved", ['bid' => $bid->toArray()]);

            $tender = TenderInfo::find($bid->tender_id);
            if (!$tender || !$tender->users) {
                Log::error("❌ Tender or associated user not found", ['tender_id' => $bid->tender_id]);
                return;
            }

            $user = $tender->users;
            Log::info("✔ Tender & user retrieved", ['user' => $user->toArray()]);

            $teMail = $user->email ?? null;
            $teName = $user->name ?? 'Unknown';
            $tePass = $user->app_password ?? null;

            Log::info("TE Mail Credentials", [
                'email' => $teMail,
                'name' => $teName,
                'hasPassword' => $tePass ? 'yes' : 'no'
            ]);

            if (!$teMail || !$tePass) {
                Log::error("❌ Missing credentials for tender executive");
                return;
            }

            $ceo = User::where('role', 'admin')->where('team', $user->team)->first()?->email ?? 'goyal@volksenergie.in';
            $coo = User::where('designation', 'coo')->first()?->email ?? 'arathi@volksenergie.in';
            $cord = User::where('role', 'coordinator')->where('team', $user->team)->first()?->email ?? 'kainaat@volksenergie.in';
            $tl = User::where('role', 'team-leader')->where('team', $user->team)->first();

            Log::info("Team members fetched", [
                'ceo' => $ceo,
                'coo' => $coo,
                'coordinator' => $cord,
                'team_leader' => $tl?->email
            ]);

            if (!$tl || !$tl->email) {
                Log::error("❌ Team Leader not found for team: {$user->team}");
                return;
            }

            $timeBeforeDeadlineInMinutes = Carbon::parse("{$tender->due_date} {$tender->due_time}")->diffInMinutes($bid->bid_submissions_date);
            $hours = intdiv($timeBeforeDeadlineInMinutes, 60);
            $minutes = $timeBeforeDeadlineInMinutes % 60;
            $timeBeforeDeadline = sprintf('%02d hr %02d min', $hours, $minutes);

            $data = [
                'tlName' => $tl->name,
                'tenderName' => $tender->tender_name,
                'dueDate' => Carbon::parse("{$tender->due_date} {$tender->due_time}")->format('d-m-Y h:i A'),
                'bidSubmissionDate' => Carbon::parse($bid->bid_submissions_date)->format('d-m-Y h:i A'),
                'timeBeforeDeadline' => $timeBeforeDeadline,
                'teName' => $teName,
                'files' => $files
            ];

            Log::info("📩 Preparing to send email", ['mail_data' => $data]);

            MailHelper::configureMailer($teMail, $tePass, $teName);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

            Mail::mailer($mailer)->to($tl->email)
                ->cc([$ceo, $coo, $cord])
                ->send(new BidSubmissionSubmitted($data));

            Log::info("✅ Bid submission email sent successfully to TL: {$tl->email}");
            return true;
        } catch (Exception $e) {
            Log::error("❌ Failed to send bid submission email", ['exception' => $e->getMessage()]);
            return false;
        }
    }

    public function bidMissed($id)
    {
        try {
            Log::info("Bid submission process started for Bid ID: $id");

            $bid = BidSubmission::find($id);
            if (!$bid) {
                Log::error("❌ Bid not found for ID: $id");
                return;
            }

            Log::info("✔ Bid retrieved", ['bid' => $bid->toArray()]);

            $tender = TenderInfo::find($bid->tender_id);
            if (!$tender || !$tender->users) {
                Log::error("❌ Tender or associated user not found", ['tender_id' => $bid->tender_id]);
                return;
            }

            $user = $tender->users;
            Log::info("✔ Tender & user retrieved", ['user' => $user->toArray()]);

            $teMail = $user->email ?? null;
            $teName = $user->name ?? 'Unknown';
            $tePass = $user->app_password ?? null;

            Log::info("TE Mail Credentials", [
                'email' => $teMail,
                'name' => $teName,
                'hasPassword' => $tePass ? 'yes' : 'no'
            ]);

            if (!$teMail || !$tePass) {
                Log::error("❌ Missing credentials for tender executive");
                return;
            }

            $ceo = User::where('role', 'admin')->where('team', $user->team)->first()?->email ?? 'goyal@volksenergie.in';
            $coo = User::where('designation', 'coo')->first()?->email ?? 'arathi@volksenergie.in';
            $cord = User::where('role', 'coordinator')->where('team', $user->team)->first()?->email ?? 'kainaat@volksenergie.in';
            $tl = User::where('role', 'team-leader')->where('team', $user->team)->first();

            Log::info("Team members fetched", [
                'ceo' => $ceo,
                'coo' => $coo,
                'coordinator' => $cord,
                'team_leader' => $tl?->email
            ]);

            if (!$tl || !$tl->email) {
                Log::error("❌ Team Leader not found for team: " . $user->team);
                return;
            }

            $data = [
                'tl_name' => $tl->name,
                'tender_name' => $tender->tender_name,
                'due_date_time' => date('d-m-Y h:i', strtotime($tender->due_date . ' ' . $tender->due_time)),
                'reason' => $bid->reason_for_missing,
                'prevention' => $bid->not_repeat_reason,
                'tms_improvements' => $bid->tms_improvements,
                'te_name' => $teName,
            ];

            MailHelper::configureMailer($teMail, $tePass, $teName);
            // MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Gyan');
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

            $mail = Mail::mailer($mailer)
                ->to($tl->email)
                ->cc([$ceo, $coo, $cord])
                ->send(new BidSubmissionMissed($data));

            if ($mail) {
                Log::info('Bid Missed email sent successfully');
                return true;
            } else {
                Log::error('Failed to send bid submission email');
                return true;
            }
        } catch (Exception $e) {
            Log::error("Failed to send bid submission email: " . $e->getMessage());
        }
    }
}
