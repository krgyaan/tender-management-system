<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Emds;
use App\Models\User;
use App\Models\FollowUps;
use App\Mail\FollowupStop;
use App\Helpers\MailHelper;
use App\Models\FollowupFor;
use App\Support\MailRender;
use Illuminate\Support\Str;
use App\Mail\BgFollowupMail;
use App\Mail\BtFollowupMail;
use App\Mail\DdFollowupMail;
use Illuminate\Http\Request;
use App\Mail\ChqFollowupMail;
use App\Mail\FdrFollowupMail;
use App\Mail\PopFollowupMail;
use App\Jobs\SendFollowupMail;
use App\Mail\FollowupAssigned;
use App\Models\Clintdirectory;
use App\Models\FollowUpPersons;
use App\Mail\FollowupPersonMail;
use Illuminate\Http\JsonResponse;
use App\Services\GmailSendService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendAssigneeFollowupMail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;


class FollowUpsController extends Controller
{
    private $bg_purpose = [
        'advance' => 'Advance Payment',
        'deposit' => 'Security Bond/ Deposit',
        'bid' => 'Bid Bond',
        'performance' => 'Performance',
        'financial' => 'Financial',
        'counter' => 'Counter Guarantee',
    ];

    public function index()
    {
        $followups = FollowUps::all();
        $ongoing = $followups->where('frequency', '!=', '6');
        $achieved = $followups->where('frequency', '6')->where('stop_reason', '2');
        $angry = $followups->where('frequency', '6')->whereIn('stop_reason', [1, 3]);
        $future = $followups->where('start_from', '>', Carbon::now()->format('Y-m-d'));

        $amount = FollowUps::select('assigned_to', DB::raw('SUM(amount) as total_amount'))
            ->where(function ($query) {
                if (Auth::user()->role != 'admin') {
                    $query->where('assigned_to', Auth::user()->id);
                }
            })
            ->groupBy('assigned_to')
            ->get()
            ->mapWithKeys(function ($item) {
                $achieved = FollowUps::where('assigned_to', $item->assigned_to)
                    ->where('stop_reason', '2')
                    ->sum('amount');
                return [
                    $item->assigned_to => [
                        'total_amount' => $item->total_amount,
                        'achieved_amount' => $achieved,
                    ],
                ];
            });

        return view('followups.index', compact('ongoing', 'achieved', 'angry', 'future', 'amount'));
    }
    public function create()
    {
        $users = User::where('role', '!=', 'admin')->where('status', 1)->get();
        $reasons = FollowupFor::all();
        return view('followups.create', compact('users', 'reasons'));
    }
    public function store(Request $request)
    {
        Log::info('Storing followup', ['request' => $request->all()]);
        try {
            $request->validate([
                'area' => 'required|string|max:255',
                'party_name' => 'required|string|max:255',
                'amount' => 'required|numeric',
                'followup_for' => 'required|string',
                'assigned_to' => 'required|exists:users,id',
                'fp.*.name' => 'nullable|string|max:255',
                'fp.*.phone' => 'nullable|digits_between:10,15',
                'fp.*.email' => 'nullable|email|max:255',
                'created_by' => 'required|exists:users,id',
                'comment' => 'string',
            ]);

            $followup = new FollowUps();
            $followup->area = $request->area;
            $followup->party_name = $request->party_name;
            $followup->followup_for = $request->followup_for;
            $followup->amount = $request->amount;
            $followup->assign_initiate = 'Followup Assigned';
            $followup->assigned_to = $request->assigned_to;
            $followup->created_by = $request->created_by;
            $followup->comment = $request->comment;
            $followup->save();

            Log::info('Followup created', ['followup_id' => $followup->id]);

            if ($request->has('fp') && is_array($request->fp)) {
                foreach ($request->fp['name'] as $key => $value) {
                    if (!empty($value) || !empty($request->fp['phone'][$key]) || !empty($request->fp['email'][$key])) {
                        $existingDir = Clintdirectory::where('email', $request->fp['email'][$key])
                            ->orWhere('phone_no', $request->fp['phone'][$key])
                            ->first();
                        if (!$existingDir) {
                            $dir = new Clintdirectory();
                            $dir->name = $request->fp['name'][$key];
                            $dir->phone_no = $request->fp['phone'][$key];
                            $dir->email = $request->fp['email'][$key];
                            $dir->save();
                        } else {
                            Log::warning("Duplicate entry found in Clintdirectory.");
                        }

                        $fup = new FollowUpPersons();
                        $fup->follwup_id = $followup->id;
                        $fup->name = $value;
                        $fup->phone = $request->fp['phone'][$key];
                        $fup->email = $request->fp['email'][$key];
                        $fup->save();
                    }
                }
                Log::info('Followup Persons while Assign: ' . json_encode($request->fp));
            }

            // Queue mail to assignee instead of sending synchronously
            SendAssigneeFollowupMail::dispatch($followup->id)->onQueue('mail');
            Log::info('Mail to assignee queued', ['followup_id' => $followup->id]);
            return redirect()->route('followups.index')->with('success', 'Followup created; mail queued to assignee');
        } catch (\Throwable $th) {
            Log::error('Error followup store: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function show(FollowUps $followUps)
    {
        //
    }
    public function edit($id)
    {
        $fup = FollowUps::find($id);
        $users = User::where('role', '!=', 'admin')->where('status', 1)->get();
        $reasons = FollowupFor::all();
        return view('followups.edit', compact('fup', 'users', 'reasons'));
    }
    public function update(Request $request)
    {
        Log::info('Followup update request: ' . json_encode($request->all()));
        try {
            // dd($request->all());
            $validator = Validator::make($request->all(), [
                'fp.*' => 'nullable|array',
                'fp.name.*' => 'nullable|string|max:255',
                'fp.phone.*' => 'nullable',
                'fp.email.*' => 'nullable|email|max:255',
                'frequency' => 'required|in:1,2,3,4,5,6',
                'stop_reason' => 'required_if:frequency,6',
                'proof_text' => 'required_if:stop_reason,2',
                'proof_img' => 'nullable|image|mimes:jpg,png,jpeg',
                'stop_rem' => 'nullable|string|max:500',
                'detailed' => 'required|string',
                'start_from' => 'required|date',
                'attachments' => 'nullable',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $attachments = [];

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $key => $value) {
                    Log::info('Attachment: ' . json_encode($value));
                    $image = $value;
                    $file = strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
                    $oname = str_replace(' ', '_', $file);
                    $imageName = time() . '_' . $oname . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads/accounts'), $imageName);
                    $attachments[] = $imageName;
                }
            }

            if ($request->has('fp') && is_array($request->fp)) {
                foreach ($request->fp['name'] as $key => $value) {
                    $fup = new FollowUpPersons();
                    $fup->follwup_id = $request->id;
                    $fup->name = $request->fp['name'][$key];
                    $fup->phone = $request->fp['phone'][$key];
                    $fup->email = $request->fp['email'][$key];
                    $fup->save();
                    Log::info('Followup Person created: ' . json_encode($fup));

                    $existingDir = Clintdirectory::where('email', $request->fp['email'][$key])
                        ->orWhere('phone_no', $request->fp['phone'][$key])
                        ->first();
                    if (!$existingDir) {
                        $dir = new Clintdirectory();
                        $dir->name = $request->fp['name'][$key];
                        $dir->phone_no = $request->fp['phone'][$key];
                        $dir->email = $request->fp['email'][$key];
                        $dir->save();
                        Log::info('Client directory created: ' . json_encode($dir));
                    } else {
                        Log::warning("Duplicate entry found in Clintdirectory.");
                    }
                }
            }
            Log::info('Followup Persons: ' . json_encode($request->fp));
            $start_from = $request->start_from;
            $frequency = $request->frequency;
            $stop_reason = $request->stop_reason;
            $proof_text = $request->proof_text;
            $stop_rem = $request->stop_rem;

            if ($frequency == '6' && empty($stop_reason)) {
                Log::error('Stop reason is required when frequency is "Stop".');
                return redirect()->back()->with('error', 'Stop reason is required when frequency is "Stop".');
            }

            if ($stop_reason == '2' && empty($proof_text)) {
                Log::error('Please provide proof when stop reason is "Followup Objective achieved".');
                return redirect()->back()->with('error', 'Please provide proof when stop reason is "Followup Objective achieved".');
            }

            if ($request->hasFile('proof_img')) {
                $image = $request->file('proof_img');
                $file = strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
                $oname = str_replace(' ', '_', $file);
                $imageName = time() . '_' . $oname . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/accounts'), $imageName);
                $proof_img = $imageName;
            } else {
                $proof_img = null;
            }

            $followup = FollowUps::find($request->id);
            if (!$followup) {
                Log::error('Followup not found');
                return redirect()->back()->with('error', 'Followup not found');
            }

            $followup->assign_initiate = 'Followup Initiated';
            $followup->details = $request->detailed;
            $followup->frequency = $frequency;
            $followup->start_from = $start_from;
            $followup->stop_reason = $stop_reason;
            $followup->proof_text = $proof_text;
            $followup->proof_img = $proof_img;
            $followup->stop_rem = $stop_rem;
            $followup->attachments = $attachments ? json_encode($attachments) : $followup->attachments;
            $followup->save();

            Log::info('Followup updated successfully: ' . json_encode($followup));
            if ($this->followupMail($followup->id)) {
                return redirect()->route('followups.index')->with('success', 'Followup initiated and mail sent to targeted persons successfully');
            } else {
                return redirect()->route('followups.index')->with('error', 'Followup initiated successfully but mail not sent to targeted persons');
            }
        } catch (\Throwable $th) {
            Log::error('Error followup update: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $fup = FollowUps::find($id);
            if ($fup->delete()) {
                FollowUpPersons::where('follwup_id', $id)->delete();
            }
            return redirect()->route('followups.index')->with('success', 'Followup and Followup Person deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('followups.index')->with('error', $th->getMessage());
        }
    }
    public function deletePerson($id)
    {
        try {
            $fup = FollowUpPersons::findOrFail($id);
            if ($fup->delete()) {
                return response()->json(['success' => true]);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function updateFollowup(Request $request)
    {
        try {
            $request->validate([
                "latest_comment" => 'required|string',
                "frequency" => 'nullable|in:1,2,3,4,5,6',
                "stop_reason" => 'required_if:frequency,6',
                "proof_text" => 'required_if:stop_reason,2',
                "proof_img" => 'nullable|image|mimes:jpg,png,jpeg',
                "stop_rem" => 'nullable|string|max:500',
            ]);
            $attachments = [];
            $fup = FollowUps::find($request->id);
            if (!$fup) {
                return back()->with('error', 'Follow-up not found.');
            }

            $fup->frequency = $request->frequency;
            if ($request->frequency == '4' && empty($request->stop_reason)) {
                return back()->with('error', 'Stop reason is required when frequency is "Stop".');
            }
            $fup->stop_reason = $request->stop_reason;
            if ($request->stop_reason === '2' && empty($request->proof_text)) {
                return back()->with('error', 'Please provide proof when stop reason is "Followup Objective achieved".');
            }
            $fup->proof_text = $request->proof_text;
            $fup->stop_rem = $request->stop_rem;
            if ($request->hasFile('proof_img')) {
                $image = $request->file('proof_img');
                $file = strtolower(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
                $oname = str_replace(' ', '_', $file);
                $imageName = time() . '_' . $oname . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/accounts'), $imageName);
                $fup->proof_img = $imageName;
                $attachments[] = $imageName;
            }

            $fup->latest_comment = $request->latest_comment;
            $fup->save();
            if ($request->frequency == '6') {
                if ($this->followupStopMail($fup->id, $attachments)) {
                    return back()->with('success', 'Follow-up updated and mail sent successfully.');
                } else {
                    return back()->with('error', 'Follow-up updated successfully but mail not sent.');
                }
            } else {
                return back()->with('success', 'Follow-up updated successfully.');
            }
        } catch (\Throwable $th) {
            Log::error('Error followup status update: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function FollowupFor(Request $request)
    {
        try {
            $fors = FollowupFor::all();
            return view('master.followupfors', compact('fors'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function FollowupForAdd(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            FollowupFor::create([
                'name' => $request->name
            ]);
            return redirect()->back()->with('success', 'Followup Category added successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function FollowupForUpdate(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'name' => 'required|string|max:255',
            ]);

            $fup = FollowupFor::find($request->id);
            $fup->name = $request->name;
            $fup->save();
            return redirect()->back()->with('success', 'Followup Category updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function FollowupForDelete($id)
    {
        try {
            $fup = FollowupFor::find($id);
            $fup->delete();
            return redirect()->back()->with('success', 'Followup Category deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    // === MAILS ===

    public function mailToAssignee($id)
    {
        // Queue the assignee mail and return immediately
        try {
            SendAssigneeFollowupMail::dispatch((int) $id)->onQueue('mail');
            return response()->json(['queued' => true]);
        } catch (\Throwable $e) {
            Log::error('Error queuing assignee mail: ' . $e->getMessage());
            return response()->json(['queued' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function followupMail(int $id): JsonResponse
    {
        try {
            SendFollowupMail::dispatch($id)->onQueue('mail');
            return response()->json(['queued' => true]);
        } catch (\Throwable $th) {
            Log::error('Error queuing followup mail: ' . $th->getMessage());
            return response()->json(['queued' => false, 'error' => $th->getMessage()], 500);
        }
    }

    public function followupStopMail($id, $attachments)
    {
        $stop = [
            '1' => 'The person is getting angry/or has requested to stop',
            '2' => 'Followup Objective achieved',
            '3' => 'External Followup Initiated',
            '4' => 'Remarks',
        ];
        try {
            $fu = FollowUps::find($id);
            $creator = User::where('id', $fu->created_by)->first(['email', 'name']);

            $recipients = User::whereIn('role', ['admin', 'team-leader', 'coordinator'])->pluck('email')->toArray();
            $assignee = User::where('id', $fu->assigned_to)->first(['email', 'app_password', 'name']);

            $reason = $stop[$fu->stop_reason];
            $data = [
                'assigner' => $creator->name,
                'follow_up_for' => $fu->followup_for,
                'organization_name' => $fu->party_name,
                'reason' => $reason,
                'remarks' => $fu->stop_rem,
                'proofs' => $fu->proof_text,
                'assignee' => $assignee->name,
                'files' => $attachments,
            ];
            $mailer = MailHelper::configureMailer($assignee->email, $assignee->app_password, $assignee->name);
            // $mailer = MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Gyan');
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';

            $mail = Mail::mailer($mailer)->to($creator->email)
                ->cc($recipients)
                ->send(new FollowupStop($data));
            if ($mail) {
                Log::info('Followup Mail sent to targeted persons successfully');
            } else {
                Log::error('Followup Mail not sent: ' . $mail);
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('Error followup stop mail: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function DailyFollowupMail()
    {
        $dailyMail = FollowUps::where('frequency', '1')->get();
        foreach ($dailyMail as $value) {
            try {
                SendFollowupMail::dispatch($value->id)->onQueue('mail');
                Log::info('Daily Followup Mail queued', ['followup_id' => $value->id]);
            } catch (\Throwable $th) {
                Log::error('Error queueing Daily Followup Mail: ' . $th->getMessage());
            }
        }
    }

    public function AlternateFollowupMail()
    {
        $alternateMail = FollowUps::where('frequency', '2')->get();
        foreach ($alternateMail as $value) {
            try {
                SendFollowupMail::dispatch($value->id)->onQueue('mail');
                Log::info('Alternate Followup Mail queued', ['followup_id' => $value->id]);
            } catch (\Throwable $th) {
                Log::error('Error queueing Alternate Followup Mail: ' . $th->getMessage());
            }
        }
    }

    public function TwiceADayFollowupMail()
    {
        $twiceADayMail = FollowUps::where('frequency', '3')->get();
        foreach ($twiceADayMail as $value) {
            try {
                SendFollowupMail::dispatch($value->id)->onQueue('mail');
                Log::info('Twice A Day Followup Mail queued', ['followup_id' => $value->id]);
            } catch (\Throwable $th) {
                Log::error('Error queueing Twice A Day Followup Mail: ' . $th->getMessage());
            }
        }
    }

    public function WeeklyFollowupMail()
    {
        $weeklyMail = FollowUps::where('frequency', '4')->get();
        foreach ($weeklyMail as $value) {
            try {
                SendFollowupMail::dispatch($value->id)->onQueue('mail');
                Log::info('Weekly Followup Mail queued', ['followup_id' => $value->id]);
            } catch (\Throwable $th) {
                Log::error('Error queueing Weekly Followup Mail: ' . $th->getMessage());
            }
        }
    }

    public function TwiceAWeekFollowupMail()
    {
        $twiceAWeekMail = FollowUps::where('frequency', '5')->get();
        foreach ($twiceAWeekMail as $value) {
            try {
                SendFollowupMail::dispatch($value->id)->onQueue('mail');
                Log::info('Twice A Week Followup Mail queued', ['followup_id' => $value->id]);
            } catch (\Throwable $th) {
                Log::error('Error queueing Twice A Week Followup Mail: ' . $th->getMessage());
            }
        }
    }

    public function AutoMailNow()
    {
        $mails = FollowUps::where('assigned_to', '13')->get();
        foreach ($mails as $value) {
            try {
                SendFollowupMail::dispatch($value->id)->onQueue('mail');
                Log::info('Auto Mail Now queued', ['followup_id' => $value->id]);
            } catch (\Throwable $th) {
                Log::error('Error queueing Auto Mail Now: ' . $th->getMessage());
            }
        }
    }
}
