<?php

namespace App\Http\Controllers;

use Swift_Mailer;
use Carbon\Carbon;
use Swift_Message;
use App\Models\Emds;
use App\Models\User;
use Swift_Attachment;
use App\Models\FollowUps;
use App\Mail\FollowupStop;
use App\Helpers\MailHelper;
use App\Models\FollowupFor;
use App\Mail\BgFollowupMail;
use App\Mail\BtFollowupMail;
use App\Mail\DdFollowupMail;
use Illuminate\Http\Request;
use App\Mail\ChqFollowupMail;
use App\Mail\FdrFollowupMail;
use App\Mail\PopFollowupMail;
use App\Mail\FollowupAssigned;
use App\Models\Clintdirectory;
use App\Models\FollowUpPersons;
use App\Mail\FollowupPersonMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Swift_SmtpTransport as EsmtpTransport;
use App\Services\GmailSendService;
use App\Support\MailRender;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;


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

            if ($this->mailToAssignee($followup->id)) {
                Log::info('Mail sent to assignee successfully');
                return redirect()->route('followups.index')->with('success', 'Followup created and mail sent to assignee successfully');
            } else {
                Log::error('Mail not sent to assignee');
                return redirect()->route('followups.index')->with('error', 'Followup created successfully but mail not sent to assignee');
            }
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
        $gmail = app(GmailSendService::class);
        try {
            $fup = FollowUps::findOrFail($id);

            $assigneeUser = User::findOrFail($fup->assigned_to);
            $initiator    = User::findOrFail($fup->created_by);
            $admin        = User::where('role', 'admin')->first();
            $cooMail      = optional(User::where('role', 'coordinator')->first())->email ?? 'gyanprakashk55@gmail.com';

            $to   = [$assigneeUser->email];
            $cc   = array_filter([$admin?->email, $cooMail]);
            $bcc  = []; // keep empty unless needed

            $data = [
                'team_member'       => $assigneeUser->name,
                'organization_name' => $fup->party_name,
                'follow_up_for'     => $fup->followup_for,
                'form_link'         => route('followups.edit', $fup->id),
                'follow_up_initiator' => $initiator->name,
            ];

            $html = MailRender::html(new FollowupAssigned($data));
            $subject = "Follow Up Assigned — {$fup->followup_for}";

            // Stable thread key for this follow-up stream
            $conversationKey = "followup:{$fup->id}:assigned";

            $result = $gmail->send([
                'user_id'         => $initiator->id, // send AS the initiator
                'to'              => $to,
                'cc'              => $cc,
                'bcc'             => $bcc,
                'subject'         => $subject,
                'html'            => $html,
                'conversation_key' => $conversationKey,
                'idempotency_key' => (string) Str::uuid(),
            ]);

            Log::info('Created Followup Mail sent', $result);
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('Error followup mail: ' . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()], 500);
        }
    }

    public function followupMail(int $id): JsonResponse
    {
        try {
            $gmail = app(GmailSendService::class);

            $fu = FollowUps::find($id);
            if (!$fu) {
                Log::error("FollowUp not found for ID: {$id}");
                return response()->json(['error' => 'FollowUp not found'], 404);
            }

            $creator   = User::findOrFail($fu->created_by);
            $assignee  = User::findOrFail($fu->assigned_to);
            $adminMail = optional(User::where('role', 'admin')->first())->email ?? 'gyanprakashk55@gmail.com';
            $cooMail   = optional(User::where('role', 'coordinator')->first())->email ?? 'gyanprakashk55@gmail.com';

            $recipients = FollowUpPersons::where('follwup_id', $id)
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();

            $startDate = Carbon::parse($fu->start_from)->startOfDay();
            $today     = Carbon::now()->startOfDay();
            if ($startDate->gt($today)) {
                Log::info("No followup mail sent; start date {$startDate->toDateString()} is in the future.");
                return response()->json(['success' => true, 'skipped' => true]);
            }
            $day = max(1, $startDate->diffInDays($today, false));

            $data = [
                'for'      => $fu->followup_for,
                'since'    => $day,
                'reminder' => $fu->reminder_no,
                'mail'     => $fu->details,
                'files'    => json_decode($fu->attachments, true) ?: [],
            ];

            // Map to your Mailable class (reuse your switch/cases)
            $class = FollowupPersonMail::class; // default
            if ($fu->emd_id) {
                $emd = Emds::find($fu->emd_id);
                if (!$emd) {
                    Log::warning('Invalid EMD ID: ' . $fu->emd_id);
                    return response()->json(['error' => 'Invalid EMD ID'], 400);
                }
                $ins = $emd->instrument_type;
                switch ($ins) {
                    case 1:
                        $class = DdFollowupMail::class;
                        $data['for'] = $fu->followup_for;
                        $data['name'] = $fu->party_name;
                        $data['tenderNo'] = $fu->dd->emd->tender->tender_no ?? null;
                        $data['projectName'] = $fu->dd->emd->project_name ?? null;
                        $data['status'] = $fu->dd->emd->tender->statuses->name ?? null;
                        $data['amount'] = format_inr($fu->dd->dd_amt);
                        $data['date'] = date('d-m-Y', strtotime($fu->dd->dd_date));
                        $data['ddNo'] = $fu->dd->dd_no;
                        $data['accountNo'] = '1234567890';
                        $data['ifscCode'] = 'SBIN0000001';
                        break;
                    case 2:
                        $class = FdrFollowupMail::class;
                        // fill as needed
                        break;
                    case 3:
                        $class = ChqFollowupMail::class;
                        $data['for'] = $fu->followup_for;
                        $data['name'] = $fu->party_name;
                        $data['chequeNo'] = $fu->chq->cheque_no ?? null;
                        $data['status'] = $fu->chq->status ?? null;
                        $data['date'] = date('d-m-Y', strtotime($fu->chq->cheque_date ?? now()));
                        $data['amount'] = format_inr($fu->chq->cheque_amt ?? 0);
                        break;
                    case 4:
                        $class = BgFollowupMail::class;
                        $bg_purpose = $bg_purpose ?? []; // ensure defined if you use it
                        $data['for'] = $fu->followup_for;
                        $data['name'] = $fu->party_name;
                        $data['tenderNo'] = $fu->bg->emds->tender_no ?? null;
                        $data['projectName'] = $fu->bg->emds->project_name ?? null;
                        $data['status'] = $fu->bg->emds->tender->statuses->name ?? null;
                        $data['amount'] = format_inr($fu->bg->bg_amt ?? 0);
                        $data['bg_no'] = $fu->bg->bg_no ?? null;
                        $data['purpose'] = isset($fu->bg->bg_purpose) ? ($bg_purpose[$fu->bg->bg_purpose] ?? '') : '';
                        $data['bg_validity'] = date('d-m-Y', strtotime($fu->bg->bg_expiry ?? now()));
                        $data['bg_claim_period_expiry'] = date('d-m-Y', strtotime($fu->bg->bg_claim ?? now()));
                        $data['favor'] = $fu->bg->bg_favor ?? null;
                        break;
                    case 5:
                        $class = BtFollowupMail::class;
                        $data['for'] = $fu->followup_for;
                        $data['name'] = $fu->party_name;
                        $data['tenderNo'] = $fu->bt->emd->tender->tender_no ?? null;
                        $data['projectName'] = $fu->bt->emd->project_name ?? null;
                        $data['status'] = $fu->bt->emd->tender->statuses->name ?? null;
                        $data['amount'] = format_inr($fu->bt->bt_amount ?? 0);
                        $data['date'] = date('d-m-Y', strtotime($fu->bt->transfer_date ?? now()));
                        $data['utr'] = $fu->bt->utr ?? null;
                        break;
                    case 6:
                        $class = PopFollowupMail::class;
                        $data['for'] = $fu->followup_for;
                        $data['name'] = $fu->party_name;
                        $data['tenderNo'] = $fu->pop->emd->tender->tender_no ?? null;
                        $data['projectName'] = $fu->pop->emd->project_name ?? null;
                        $data['status'] = $fu->pop->emd->tender->statuses->name ?? null;
                        $data['amount'] = format_inr($fu->pop->amount ?? 0);
                        $data['date'] = date('d-m-Y', strtotime($fu->pop->transfer_date ?? now()));
                        $data['utr'] = $fu->pop->utr ?? null;
                        $data['accountNo'] = '1234567890';
                        $data['ifsc'] = 'SBIN0000001';
                        break;
                    default:
                        $class = FollowupPersonMail::class;
                }
            }

            $html    = (new $class($data))->render();
            $subject = 'Follow Up for ' . ($data['for'] ?? 'Update');

            $MAX_RAW_BYTES  = 35 * 1024 * 1024;
            $SAFETY_MARGIN  = 2 * 1024 * 1024;
            $B64_FACTOR     = 4 / 3;
            $budget         = $MAX_RAW_BYTES - $SAFETY_MARGIN;

            $attachmentsInput = $data['files'] ?? [];
            $attachments      = [];
            $skipped          = [];
            $encodedSoFar     = strlen(base64_encode($html)) + 4096;

            foreach ($attachmentsInput as $file) {
                Log::info("Processing attachment: {$file}");
                $path = public_path("uploads/accounts/$file");
                if (!is_file($path)) {
                    Log::warning("Attachment not found: {$path}");
                    $skipped[] = ['file' => $file, 'reason' => 'missing'];
                    continue;
                } else {
                    Log::info("Attachment found: {$path}");
                }

                $content = file_get_contents($path);
                if ($content === false) {
                    Log::warning("Attachment unreadable: {$path}");
                    $skipped[] = ['file' => $file, 'reason' => 'unreadable'];
                    continue;
                } else {
                    Log::info("Attachment read successfully: {$path}");
                }

                // Estimate base64 size increment for this part
                $plainSize   = strlen($content);
                $encodedSize = (int) ceil($plainSize * $B64_FACTOR) + 1024; // + MIME headers cushion

                if (($encodedSoFar + $encodedSize) > $budget) {
                    Log::warning("Skipping attachment (size limit): {$file}");
                    $skipped[] = ['file' => $file, 'reason' => 'size_limit'];
                    continue;
                }

                $attachments[] = [
                    'filename' => basename($path),
                    'content'  => $content,
                    'mime'     => File::mimeType($path) ?: 'application/octet-stream',
                ];
                $encodedSoFar += $encodedSize;
            }

            Log::info("Total attachments included: " . count($attachments) . ", skipped: " . count($skipped));

            // If we skipped any, append a short note to the HTML so recipients know
            if (!empty($skipped)) {
                $skippedList = implode(', ', array_map(fn($s) => $s['file'], $skipped));
                $note = "<p><em>Note:</em> Some attachments were omitted due to size limits: {$skippedList}</p>";
                $html .= $note;
            }

            // All reminders for the same followup append to one Gmail thread.
            $conversationKey = "followup:{$fu->id}:main";

            $result = $gmail->send([
                'user_id'          => $assignee->id,
                'to'               => $recipients,
                'bcc'              => [],
                'subject'          => $subject,
                'html'             => $html,
                'attachments'      => $attachments,
                'conversation_key' => $conversationKey,
                'idempotency_key'  => (string) Str::uuid(),
            ]);

            Log::info('Followup mail sent', [
                'to' => $recipients,
                'result' => $result,
                'attachments' => $result['attachments'] ?? [],
                'skipped' => $skipped
            ]);

            return response()->json([
                'success' => true,
                'threadId' => $result['threadId'] ?? null,
                'messageId' => $result['messageId'] ?? null,
                'attachments' => $result['attachments'] ?? [],
                'skipped_attachments' => $skipped,
            ]);
        } catch (\Throwable $th) {
            Log::error('Error during followupMail process: ' . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()], 500);
        }
    }

    public function followupMailOld($id)
    {
        try {

            $fu = FollowUps::find($id);
            if (!$fu) {
                Log::error("FollowUp not found for ID: $id");
                return response()->json(['error' => 'FollowUp not found'], 404);
            }

            $creator = User::where('id', $fu->created_by)->first();
            $creatorMail = $creator->email;
            Log::info("Creator found: {$creator->name}, Email: $creatorMail");

            $adminMail = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $cooMail = User::where('role', 'coordinator')->first()->email ?? 'gyanprakashk55@gmail.com';
            $email = FollowUpPersons::where('follwup_id', $id)
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();
            Log::info('Collected emails for followup: ' . json_encode($email));

            $start_date = Carbon::parse($fu->start_from);
            $today = Carbon::now()->format('Y-m-d');
            $diff_in_days = $start_date->diffInDays($today, false);
            $day = max(1, round($diff_in_days));

            Log::info("Start date: $start_date - Today: $today = Days since start: $day");

            if ($start_date->lte($today)) {
                $data = [
                    'for' => $fu->followup_for,
                    'since' => $day,
                    'reminder' => $fu->reminder_no,
                    'mail' => $fu->details,
                    'files' => json_decode($fu->attachments),
                ];
                $emdClassMap = [
                    1 => DdFollowupMail::class,
                    2 => FdrFollowupMail::class,
                    3 => ChqFollowupMail::class,
                    4 => BgFollowupMail::class,
                    5 => BtFollowupMail::class,
                    6 => PopFollowupMail::class,
                ];

                if ($fu->emd_id) {
                    $emd = Emds::find($fu->emd_id);
                    if ($emd) {
                        $ins = $emd->instrument_type;
                        Log::info('EMD found: ' . json_encode($emd));
                        Log::info('EMD: ' . json_encode($fu));
                    } else {
                        Log::warning('Invalid EMD ID: ' . $fu->emd_id);
                        return response()->json(['error' => 'Invalid EMD ID'], 400);
                    }

                    $class = $emdClassMap[$ins] ?? FollowupPersonMail::class;

                    switch ($ins) {
                        case 1:
                            $data['for'] = $fu->followup_for;
                            $data['name'] = $fu->party_name;
                            $data['tenderNo'] = $fu->dd->emd->tender->tender_no;
                            $data['projectName'] = $fu->dd->emd->project_name;
                            $data['status'] = $fu->dd->emd->tender->statuses->name;
                            $data['amount'] = format_inr($fu->dd->dd_amt);
                            $data['date'] = date('d-m-Y', strtotime($fu->dd->dd_date));
                            $data['ddNo'] = $fu->dd->dd_no;
                            $data['accountNo'] = '1234567890';
                            $data['ifscCode'] = 'SBIN0000001';
                            break;
                        case 2:
                            break;
                        case 3:
                            $data['for'] = $fu->followup_for;
                            $data['name'] = $fu->party_name;
                            $data['chequeNo'] = $fu->chq->cheque_no;
                            $data['status'] = $fu->chq->status;
                            $data['date'] = date('d-m-Y', strtotime($fu->chq->cheque_date));
                            $data['chequeNo'] = $fu->chq->cheque_no;
                            $data['amount'] = format_inr($fu->chq->cheque_amt);
                            break;
                        case 4:
                            $data['for'] = $fu->followup_for;
                            $data['name'] = $fu->party_name;
                            $data['tenderNo'] = $fu->bg->emds->tender_no ?? null;
                            $data['projectName'] = $fu->bg->emds->project_name;
                            $data['status'] = $fu->bg->emds->tender->statuses->name ?? null;
                            $data['amount'] = format_inr($fu->bg->bg_amt);
                            $data['bg_no'] = $fu->bg->bg_no;
                            $data['purpose'] = $fu->bg->bg_purpose ? $bg_purpose[$fu->bg->bg_purpose] : '';
                            $data['bg_validity'] = date('d-m-Y', strtotime($fu->bg->bg_expiry));
                            $data['bg_claim_period_expiry'] = date('d-m-Y', strtotime($fu->bg->bg_claim));
                            $data['favor'] = $fu->bg->bg_favor;
                            break;
                        case 5:
                            $data['for'] = $fu->followup_for;
                            $data['name'] = $fu->party_name;
                            $data['tenderNo'] = $fu->bt->emd->tender->tender_no;
                            $data['projectName'] = $fu->bt->emd->project_name;
                            $data['status'] = $fu->bt->emd->tender->statuses->name;
                            $data['amount'] = format_inr($fu->bt->bt_amount);
                            $data['date'] = date('d-m-Y', strtotime($fu->bt->transfer_date));
                            $data['utr'] = $fu->bt->utr;
                            break;
                        case 6:
                            $data['for'] = $fu->followup_for;
                            $data['name'] = $fu->party_name;
                            $data['tenderNo'] = $fu->pop->emd->tender->tender_no;
                            $data['projectName'] = $fu->pop->emd->project_name;
                            $data['status'] = $fu->pop->emd->tender->statuses->name;
                            $data['amount'] = format_inr($fu->pop->amount);
                            $data['date'] = date('d-m-Y', strtotime($fu->pop->transfer_date));
                            $data['utr'] = $fu->pop->utr;
                            $data['accountNo'] = '1234567890';
                            $data['ifsc'] = 'SBIN0000001';
                            break;
                    }
                } else {
                    $class =  FollowupPersonMail::class;
                }
                Log::info('Data: ' . json_encode($data));

                $user = User::where('id', $fu->assigned_to)->first();
                $assignee = $user->name;
                $userMail = $user->email;
                $appPass = $user->app_password;
                Log::info("Assignee found: $assignee, Email:  $userMail");

                Artisan::call('config:clear');
                app('cache')->forget('laravel.config.cache');

                $transport = (new EsmtpTransport('smtp.gmail.com', 587, 'tls'))
                    ->setUsername($userMail)
                    ->setPassword($appPass);

                $swift = new Swift_Mailer($transport);
                Log::error('Failed to send mail from class error:yyyyyyyyy ' . $class);
                $message = (new Swift_Message())
                    ->setFrom([$userMail => $assignee . ' From ' . config('app.name')])
                    ->setTo($email)
                    ->setCc([$adminMail, $cooMail, $creatorMail])
                    ->setSubject('Follow Up for ' . $data['for'])
                    ->setBody((new $class($data))->render(), 'text/html');

                if (!empty($data['files'])) {
                    foreach ($data['files'] as $file) {
                        $filePath = public_path('uploads/accounts/' . $file);
                        if (file_exists($filePath)) {
                            $message->attach(
                                Swift_Attachment::fromPath($filePath)
                                    ->setFilename($file)
                            );
                            Log::info('Attached file: ' . $file);
                        } else {
                            Log::warning('File not found: ' . $filePath);
                        }
                    }
                }
                $result = $swift->send($message);

                if ($result) {
                    Log::info('Mail sent successfully from: ' . $userMail . ' to: ' . json_encode($email));
                } else {
                    Log::error('Failed to send mail from: ' . $userMail);
                }
            } else {
                Log::info('No followup mail sent as the start date is in the future');
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('Error during followupMail process: ' . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
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
        // Log::info('Daily Followup Mail: ' . json_encode($dailyMail));
        foreach ($dailyMail as $key => $value) {
            Log::info('Daily Followup Mail: ' . json_encode($value));
            try {
                $sent = $this->followupMail($value->id);
                if ($sent->original['success'] ?? false) {
                    $reminder_no = $value->reminder_no + 1;
                    FollowUps::where('id', $value->id)->update(['reminder_no' => $reminder_no]);
                    Log::info('Reminder updated for ID: ' . $value->id . ' Reminder: ' . $reminder_no);
                } else {
                    Log::error('Mail not sent for ID: ' . $value->id);
                }
            } catch (\Throwable $th) {
                Log::error('Error during Daily Followup Mail: ' . $th->getMessage());
            }
        }
    }


    public function AlternateFollowupMail()
    {
        $alternateMail = FollowUps::where('frequency', '2')->get();
        foreach ($alternateMail as $key => $value) {
            Log::info('Alternate Followup Mail: ' . json_encode($value));
            try {
                $sent = $this->followupMail($value->id);
                if ($sent->original['success'] ?? false) {
                    $reminder_no = $value->reminder_no + 1;
                    FollowUps::where('id', $value->id)->update(['reminder_no' => $reminder_no]);
                    Log::info('Reminder updated for ID: ' . $value->id . ' Reminder: ' . $reminder_no);
                } else {
                    Log::error('Mail not sent for ID: ' . $value->id);
                }
            } catch (\Throwable $th) {
                Log::error('Error during Daily Followup Mail: ' . $th->getMessage());
            }
        }
    }

    public function TwiceADayFollowupMail()
    {
        $twiceADayMail = FollowUps::where('frequency', '3')->get();
        foreach ($twiceADayMail as $key => $value) {
            Log::info('Twice A Day Followup Mail: ' . json_encode($value));
            try {
                $sent = $this->followupMail($value->id);
                if ($sent->original['success'] ?? false) {
                    $reminder_no = $value->reminder_no + 1;
                    FollowUps::where('id', $value->id)->update(['reminder_no' => $reminder_no]);
                    Log::info('Reminder updated for ID: ' . $value->id . ' Reminder: ' . $reminder_no);
                } else {
                    Log::error('Mail not sent for ID: ' . $value->id);
                }
            } catch (\Throwable $th) {
                Log::error('Error during Daily Followup Mail: ' . $th->getMessage());
            }
        }
    }

    public function WeeklyFollowupMail()
    {
        $weeklyMail = FollowUps::where('frequency', '4')->get();
        foreach ($weeklyMail as $key => $value) {
            Log::info('Weekly Followup Mail: ' . json_encode($value));
            try {
                $sent = $this->followupMail($value->id);
                if ($sent->original['success'] ?? false) {
                    $reminder_no = $value->reminder_no + 1;
                    FollowUps::where('id', $value->id)->update(['reminder_no' => $reminder_no]);
                    Log::info('Reminder updated for ID: ' . $value->id . ' Reminder: ' . $reminder_no);
                } else {
                    Log::error('Mail not sent for ID: ' . $value->id);
                }
            } catch (\Throwable $th) {
                Log::error('Error during Daily Followup Mail: ' . $th->getMessage());
            }
        }
    }

    public function TwiceAWeekFollowupMail()
    {
        $twiceAWeekMail = FollowUps::where('frequency', '5')->get();
        foreach ($twiceAWeekMail as $key => $value) {
            Log::info('Twice A Week Followup Mail: ' . json_encode($value));
            try {
                $sent = $this->followupMail($value->id);
                if ($sent->original['success'] ?? false) {
                    $reminder_no = $value->reminder_no + 1;
                    FollowUps::where('id', $value->id)->update(['reminder_no' => $reminder_no]);
                    Log::info('Reminder updated for ID: ' . $value->id . ' Reminder: ' . $reminder_no);
                } else {
                    Log::error('Mail not sent for ID: ' . $value->id);
                }
            } catch (\Throwable $th) {
                Log::error('Error during Daily Followup Mail: ' . $th->getMessage());
            }
        }
    }

    public function AutoMailNow()
    {
        $mails = FollowUps::where('assigned_to', '13')->get();
        foreach ($mails as $key => $value) {
            Log::info('Auto Mail Now: ' . json_encode($value));
            try {
                $sent = $this->followupMail($value->id);
                if ($sent->original['success'] ?? false) {
                    $reminder_no = $value->reminder_no + 1;
                    FollowUps::where('id', $value->id)->update(['reminder_no' => $reminder_no]);
                    Log::info('Reminder updated for ID: ' . $value->id . ' Reminder: ' . $reminder_no);
                } else {
                    Log::error('Mail not sent for ID: ' . $value->id);
                }
            } catch (\Throwable $th) {
                Log::error('Error during Auto Mail Now: ' . $th->getMessage());
            }
        }
    }
}
