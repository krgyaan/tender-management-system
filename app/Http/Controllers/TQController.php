<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Tq_type;
use App\Models\Tq_missed;
use App\Mail\TqMissedMail;
use App\Models\TenderInfo;
use App\Models\Tq_replied;
use App\Helpers\MailHelper;
use App\Mail\TqRepliedMail;
use App\Models\Tq_received;
use App\Mail\TqReceivedMail;
use Illuminate\Http\Request;
use App\Models\Wo_acceptance_yes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use App\Services\TimerService;

class TQController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function tq_type()
    {
        $tq_type = Tq_type::where('status', '1')->get();
        return view('tq_menagement.tq_type', ['tq_type' => $tq_type]);
    }

    public function tq_type_add(Request $request)
    {
        try {
            Log::info('Adding new TQ Type', ['tq_type' => $request->tq_type]);

            $data = new Tq_type;
            $data->tq_type = $request->tq_type;
            $data->ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $data->save();

            Log::info('TQ Type added successfully', ['id' => $data->id]);

            return redirect()->route('tq_type')->with('success', 'TQ Type Added.');
        } catch (\Exception $e) {
            Log::error('Failed to add TQ Type', ['error' => $e->getMessage()]);
            return redirect()->route('tq_type')->with('error', 'Failed to add TQ Type.');
        }
    }

    public function tq_type_update(Request $request)
    {
        $data = Tq_type::find($request->id);
        if ($data) {
            $data->tq_type = $request->tq_type;
            $data->save();
            return redirect()->route('tq_type')->with('success', 'Update successfully completed.');
        }
        Log::error('Failed to update TQ Type', ['error' => 'Record not found.']);
        return redirect()->route('tq_type')->with('error', 'Record not found.');
    }

    public function tq_type_delete($id)
    {
        try {
            Log::info('Deleting TQ Type', ['id' => Crypt::decrypt($id)]);
            Tq_type::destroy(Crypt::decrypt($id));
            Log::info('TQ Type deleted successfully', ['id' => Crypt::decrypt($id)]);
            return redirect()->back()->with('success', 'Deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete TQ Type', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete TQ Type.');
        }
    }

    public function tq_dashboard()
    {
        // 1. Tenders where TQ is received — show first, newest bid_submission_date on top
        $tqReceived = TenderInfo::with('bs')
            ->where('deleteStatus', '0')
            ->where('tlStatus', '1')
            ->where('status', '19')
            ->whereHas('bs', function ($query) {
                $query->whereNotNull('bid_submission_date');
            })
            ->get()
            ->sortByDesc(function ($tender) {
                return optional($tender->bs)->bid_submission_date;
            });

        // 2. Remaining tenders — show after, oldest bid_submission_date on top
        $tqPending = TenderInfo::with('bs')
            ->where('deleteStatus', '0')
            ->where('tlStatus', '1')
            ->where('status', '17')
            ->whereHas('bs', function ($query) {
                $query->whereNotNull('bid_submission_date');
            })
            ->get()
            ->sortBy(function ($tender) {
                return optional($tender->bs)->bid_submission_date;
            });

        // 3. Merge lists: received first, pending next
        $finalList = $tqReceived->values()->merge($tqPending->values());
        // TQ Submitted: Status 18 to 25
        $tqSubmitted = TenderInfo::with('bs')->where('deleteStatus', '0')
            ->where('tlStatus', '1')
            ->whereIn('status', ['18', '20', '21', '22', '23', '24', '25'])
            ->get();

        return view('tq_menagement.tq_dashboard', compact('tqPending', 'tqSubmitted'));
    }

    public function view_butten($id)
    {
        $tender_id = Crypt::decrypt($id);
        $tender = DB::table('tender_infos')->find($tender_id);
        $tq_type = Tq_type::where('status', '1')->get();
        $tq_missed = Tq_missed::where('tender_id', $tender_id)->first();
        $tq_replied = Tq_replied::where('tender_id', $tender_id)->first();
        $tq_received = Tq_received::where('tender_id', $tender_id)->first() ?? new Wo_acceptance_yes();

        $tq_received->tq_type = $tq_received->tq_type ? json_decode($tq_received->tq_type, true) : [];
        $tq_received->description = $tq_received->description ? json_decode($tq_received->description, true) : [];

        return view('tq_menagement.viewbutten', compact('tender', 'tq_received', 'tq_type', 'tq_replied', 'tq_missed'));
    }

    public function tq_received_form($id)
    {
        $tender_id = Crypt::decrypt($id);
        $typedata = Tq_type::where('status', '1')->get();
        return view('tq_menagement.tq_received_form', compact('typedata', 'tender_id'));
    }

    public function tq_received_form_post(Request $request)
    {
        Log::info('Adding new TQ Received Form', ['request' => $request->all()]);

        $request->validate([
            'tender_id' => 'required|integer',
            'tq_type_id' => 'nullable|array',
            'description' => 'nullable|array',
            'date' => 'required|date',
            'time' => 'required',
            'tq_img' => 'nullable|file',
        ]);

        try {
            if ($request->hasFile('tq_img')) {
                $fileName = time() . '_tq_rec.' . $request->tq_img->getClientOriginalExtension();
                $request->tq_img->move(public_path('uploads/tq'), $fileName);
                $tq_img = $fileName;
            }

            $tqReceived = Tq_received::create([
                'tender_id' => $request->tender_id,
                'tq_type' => json_encode($request->tq_type_id ?? []),
                'description' => json_encode($request->description ?? []),
                'tq_submission_date' => $request->date,
                'tq_submission_time' => $request->time,
                'ip' => $request->ip(),
                'tq_document' => $tq_img ?? null,
                'strtotime' => Carbon::parse("{$request->date} {$request->time}")->timezone(config('app.timezone'))->timestamp,
            ]);

            Log::info('TQ Received Form Submitted successfully');

            $tender = TenderInfo::where('id', $request->tender_id)->first();
            $tender->update(['status' => 19]);

            $dd = Carbon::parse("{$request->date} {$request->time}");
            $timeDiff = $dd->diffInHours(Carbon::now());
            $hrs = round($timeDiff);

            $this->timerService->startTimer($tender, 'tq_replied', $hrs);

            if ($this->tqReceivedMail($tqReceived->id)) {
                Log::info('TQ Received Mail sent successfully');
            } else {
                Log::error('Failed to sent TQ Received mail');
            }

            return redirect()->route('tq_dashboard')->with('success', 'TQ Received Form Submitted.');
        } catch (\Exception $e) {
            Log::error('Failed to add TQ Received Form', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function tq_replied_form($id)
    {
        $tender_id = Crypt::decrypt($id);
        return view('tq_menagement.tq_replied_form', compact('tender_id'));
    }

    public function tq_replied_form_post(Request $request)
    {
        $request->validate([
            'tender_id' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'tq_img' => 'required|file',
            'proof_submission' => 'required|file'
        ]);

        try {
            $data = new Tq_replied();
            $data->fill($request->only(['tender_id', 'tq_submission_date', 'tq_submission_time']));

            if ($request->hasFile('tq_img')) {
                $fileName = time() . '_tq_rep.' . $request->tq_img->getClientOriginalExtension();
                $request->tq_img->move(public_path('uploads/tq'), $fileName);
                $data->tq_document = $fileName;
            }

            if ($request->hasFile('proof_submission')) {
                $fileName = time() . '_proof_sub.' . $request->proof_submission->getClientOriginalExtension();
                $request->proof_submission->move(public_path('uploads/tq'), $fileName);
                $data->proof_submission = $fileName;
            }

            $data->ip = $request->ip();
            $data->strtotime = Carbon::parse($data->tq_submission_date . ' ' . $data->tq_submission_time)
                ->timezone('Asia/Kolkata')
                ->timestamp;

            $data->save();

            TenderInfo::where('id', $request->tender_id)->update(['status' => '20']);

            if ($this->tqRepliedMail($data->id)) {
                Log::info('TQ Replied Mail sent successfully');
            } else {
                Log::error('Failed to sent TQ Replied mail');
            }

            return redirect()->route('tq_dashboard')->with('success', 'TQ Replied Form Submitted.');
        } catch (\Exception $e) {
            Log::error('Failed to add TQ Replied Form', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Something went wrong while saving the data.');
        }
    }

    public function tq_missed_form($id)
    {
        $tender_id = Crypt::decrypt($id);
        return view('tq_menagement.tq_missed_form', compact('tender_id'));
    }

    public function tq_missed_form_post(Request $request)
    {
        $request->validate([
            'tender_id' => 'required',
            'reason_missing' => 'required|string',
            'would_repeated' => 'required|string',
            'tms_system' => 'nullable|string',
        ]);

        try {
            Tq_missed::create([
                'tender_id' => $request->input('tender_id'),
                'reason_missing' => $request->input('reason_missing'),
                'would_repeated' => $request->input('would_repeated'),
                'tms_system' => $request->input('tms_system'),
                'ip' => $request->ip(),
            ]);

            TenderInfo::where('id', $request->tender_id)->update(['status' => '21']);

            return redirect()->route('tq_dashboard')->with('success', 'TQ Missed Form Submitted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong while saving the data.');
        }
    }

    // MAILS
    public function tqReceivedMail($id)
    {
        try {
            $tq = Tq_received::findOrFail($id);
            $tender = TenderInfo::findOrFail($tq->tender_id);

            Log::info("📧 Sending TQ Received Mail for TQ ID: $id");

            $te = User::find($tender->team_member);
            if (!$te) {
                Log::error("❌ Tender Executive not found for tender_id: {$tender->id}");
                return back()->with('error', 'Tender Executive not found.');
            }

            $admin = User::where('role', 'admin')->pluck('email')->toArray();
            $cord = User::where('role', 'coordinator')->where('team', $te->team)->first();
            $tl = User::where('role', 'team-leader')->where('team', $te->team)->first();

            Log::info("TQ Received Mail: Users", [
                'te' => $te?->name,
                'admin' => $admin,
                'cord' => $cord?->name,
                'tl' => $tl?->name,
            ]);

            $cc = array_merge($admin, [$tl?->email]);
            // $to = $te->email;
            $to = 'abs.gyankr@gmail.com';
            // MailHelper::configureMailer($cord->email, $cord->app_password, $cord->name);
            MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Denji');
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';

            $tqData = [];
            $tqTypes = json_decode($tq->tq_type, true) ?? [];
            $descriptions = json_decode($tq->description, true) ?? [];
            $tqTypeNames = Tq_type::pluck('tq_type', 'id')->toArray();
            foreach ($tqTypes as $index => $typeId) {
                $tqData[] = [
                    'type' => $tqTypeNames[$typeId] ?? "Type-$typeId",
                    'desc' => $descriptions[$index] ?? '',
                ];
            }

            $data = [
                'te' => $te->name,
                'tender_name' => $tender->tender_name,
                'due' => date('d-m-Y', strtotime($tender->due_date . ' ' . $tender->due_time)),
                'coordinator' => $cord->name,
                'files' => $tq->tq_document,
                'tqData' => $tqData
            ];
            Log::info("TQ Received Mail Data: ", $data);
            $mail = Mail::mailer($mailer)
                ->to($to)
                // ->cc($cc)
                ->send(new TqReceivedMail($data));

            if ($mail) {
                Log::info("TQ Received Mail: Mail sent successfully for TQ id: $id");
                return redirect()->route('tq_dashboard')->with('success', 'TQ Received Mail sent successfully.');
            } else {
                Log::error("TQ Received Mail: Failed to send mail for TQ id: $id");
                return redirect()->route('tq_dashboard')->with('error', 'Failed to send TQ Received Mail.');
            }
        } catch (\Exception $e) {
            Log::error('TQ Received Mail: Exception occurred', ['error' => $e->getMessage()]);
            return redirect()->route('tq_dashboard')->with('error', $e->getMessage());
        }
    }
    public function tqRepliedMail($id)
    {
        try {
            $tq = Tq_received::findOrFail($id);
            $tender = TenderInfo::findOrFail($tq->tender_id);

            Log::info("📧 Sending TQ Received Mail for TQ ID: $id");

            $te = User::find($tender->team_member);
            if (!$te) {
                Log::error("❌ Tender Executive not found for tender_id: {$tender->id}");
                return back()->with('error', 'Tender Executive not found.');
            }

            $admin = User::where('role', 'admin')->pluck('email')->toArray();

            $cord = User::where('role', 'coordinator')->where('team', $te->team)->first();
            $tl = User::where('role', 'tl')->where('team', $te->team)->first();

            Log::info("TQ Received Mail: Users", [
                'te' => $te->name,
                'admin' => $admin,
                'cord' => $cord->name,
                'tl' => $tl->name,
            ]);

            $cc = array_merge($admin, [$cord->email]);
            // $to = $tl->email;
            $to = 'abs.gyankr@gmail.com';
            // MailHelper::configureMailer($te->email, $te->app_password, $te->name);
            MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Denji');
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $timeBeforeDeadline = Carbon::parse($tender->due_date . ' ' . $tender->due_time)
                ->diffInHours(Carbon::parse($tq->tq_submission_date . ' ' . $tq->tq_submission_time));

            $data = [
                'tlName' => $tl->name,
                'tender_name' => $tender->tender_name,
                'dueDate' => date('d-m-Y', strtotime($tender->due_date . ' ' . $tender->due_time)),
                'tqSubmissionDate' => date('d-m-Y', strtotime($tq->tq_submission_date)),
                'timeBeforeDeadline' => abs($timeBeforeDeadline) . ' Hrs ',
                'teName' => $te->name,
                'tq_document' => $tq->tq_document,
                'proof_submission' => $tq->proof_submission,
            ];

            Log::info("TQ Replied Mail DATA: " . json_encode($data));
            $mail = Mail::mailer($mailer)
                ->to($to)
                // ->cc($cc)
                ->send(new TqRepliedMail($data));
            if ($mail) {
                Log::info("TQ Replied Mail: Mail sent successfully for TQ id: $id");
                return redirect()->route('tq_dashboard')->with('success', 'TQ Replied Mail sent successfully.');
            } else {
                Log::error("TQ Replied Mail: Failed to send mail for TQ id: $id");
                return redirect()->route('tq_dashboard')->with('error', 'Failed to send TQ Replied Mail.');
            }
        } catch (\Throwable $th) {
            Log::error("TQ Replied Mail: Failed to send mail for TQ id: $id. Error: " . $th->getMessage());
            return redirect()->route('tq_dashboard')->with('error', 'Failed to send TQ Replied Mail.');
        }
    }

    public function tqMissedMail($id)
    {
        $tq = Tq_received::findOrFail($id);
        $tender = TenderInfo::findOrFail($tq->tender_id);

        Log::info("📧 Sending TQ Received Mail for TQ ID: $id");

        $te = User::find($tender->team_member);
        if (!$te) {
            Log::error("❌ Tender Executive not found for tender_id: {$tender->id}");
            return back()->with('error', 'Tender Executive not found.');
        }

        $admin = User::where('role', 'admin')->pluck('email')->toArray();

        $cord = User::where('role', 'coordinator')->where('team', $te->team)->first();
        $tl = User::where('role', 'tl')->where('team', $te->team)->first();

        $cc = array_merge($admin, [$cord->email]);
        // $to = $tl->email;
        $to = 'abs.gyankr@gmail.com';
        // MailHelper::configureMailer($te->email, $te->app_password, $te->name);
        MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Denji');
        $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';

        $data = [
            'tl_name' => $tl->name,
            'tender_name' => $tender->tender_name,
            'tq_due_date_time' => date('d-m-Y', strtotime($tender->due_date . ' ' . $tender->due_time)),
            'reason_missing' => $tq->reason_missing,
            'would_repeated' => $tq->would_repeated,
            'tms_system' => $tq->tms_system,
            'te_name' => $te->name,
        ];

        try {
            Log::info("TQ Missed Mail DATA: " . json_encode($data));
            $mail = Mail::mailer($mailer)
                ->to($to)
                // ->cc($cc)
                ->send(new TqMissedMail($data));
            if ($mail) {
                Log::info("TQ Missed Mail: Mail sent successfully for TQ id: $id");
                return redirect()->route('tq_dashboard')->with('success', 'TQ Missed Mail sent successfully.');
            }
        } catch (\Exception $e) {
            Log::error("TQ Missed Mail: Failed to send mail for TQ id: $id");
            return redirect()->route('tq_dashboard')->with('error', 'Failed to send TQ Missed Mail.');
        }
    }

    // Auto stop Timer
    public function autoStopTimer()
    {
        Log::info("Auto Stop Timer for TQ: Running...");
        // Get all TQ Received records
        $tqReceivedRecords = Tq_received::all();
        foreach ($tqReceivedRecords as $record) {
            // Get the tender info
            $tender = TenderInfo::find($record->tender_id);
            if ($tender) {
                // Check if the timer is running
                $tq_deadline = Carbon::parse("{$record->tq_submission_date} {$record->tq_submission_time}");
                if ($tq_deadline->isPast() && $this->timerService->isTimerRunning($tender, 'tq_replied')) {
                    // Stop the timer
                    $this->timerService->stopTimer($tender, 'tq_replied');
                    Log::info("Auto stopped timer for TQ ID: {$record->id}");
                }
            }
        }
    }
}
