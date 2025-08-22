<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rfq;
use App\Models\User;
use App\Models\RaMgmt;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use Illuminate\Support\Str;
use App\Mail\RaScheduleMail;
use App\Mail\RaResultMail;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class RaMgmtController extends Controller
{
    public function index()
    {
        return view('ra_mgmt.index');
    }

    public function getRaData(Request $request, $type)
    {
        $user = Auth::user();
        $team = $request->input('team');
        Log::info('Fetching RA Data', ['type' => $type]);

        $query = TenderInfo::query()
            ->whereHas('info', fn($q) => $q->where('rev_auction', '1'))
            ->whereNotIn('status', ['8', '9', '10', '11', '12', '13', '14', '15', '38', '39'])
            ->where('deleteStatus', '0')
            ->where('tlStatus', '1');

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

        if ($type === 'pending') {
            $query->where(function ($q) {
                $q->whereHas('ra_mgmt', fn($q) => $q->whereNull('result'))
                  ->orDoesntHave('ra_mgmt');
            });
        } elseif ($type === 'completed') {
            $query->whereHas('ra_mgmt', fn($q) => $q->whereNotNull('result'));
        }

        $tenders = $query->with(['bs', 'info', 'users', 'itemName', 'statuses', 'ra_mgmt'])->get();
        $tenders = $tenders->sortBy(fn($t) => Carbon::parse(optional($t->bs)->bid_submissions_date ?? now()));

        $dataTable = DataTables::of($tenders)
            ->addColumn('tender_name', function ($tender) {
                return "<strong>{$tender->tender_name}</strong> <br>
                <span class='text-muted'>{$tender->tender_no}</span>";
            })
            ->addColumn('users.name', function ($tender) {
                return optional($tender->users)->name ?? 'N/A';
            })
            ->addColumn('gst_values', function ($tender) {
                return format_inr($tender->gst_values) ?? '0';
            })
            ->addColumn('item_name.name', function ($tender) {
                return $tender->itemName->name ?? 'N/A';
            })
            ->addColumn('tender_status', function ($tender) {
                return $tender->statuses->name ?? 'N/A';
            })
            ->addColumn('bid_submissions_date', function ($tender) {
                if ($tender->bs) {
                    return '<span class="d-none">' . strtotime($tender->bs->bid_submissions_date) . '</span>' .
                        date('d-m-Y', strtotime($tender->bs->bid_submissions_date)) . '<br>' .
                        (isset($tender->bs->bid_submissions_date) ? date('h:i A', strtotime($tender->bs->bid_submissions_date)) : '');
                }
                return 'Not Found';
            })
            ->addColumn('ra_status', function ($tender) {
                /*
                RA Scheduled (When Schedule RA is filled and Technically Qualified is Yes)
                Disqualified (When Schedule RA is filled and Technically Qualified is No)
                RA Started (When RA Start time is reached)
                Won (When Upload RA Result is filled and Result is Won)
                Lost (When Upload RA Result is filled and Result is Lost)
                Lost - H1 Elimination (When Upload RA Result is filled and Result is H1 Elimination)
                */
                $ra_status = 'Under Evaluation';
                $ra = $tender->ra_mgmt->first();
                if ($ra) {
                    if ($ra->technically_qualified === 'yes') {
                        $ra_status = 'RA Scheduled';
                    } elseif ($ra->technically_qualified === 'no') {
                        $ra_status = 'Disqualified';
                    } elseif ($ra->status === 'Won') {
                        $ra_status = 'Won';
                    } elseif ($ra->status === 'Lost') {
                        $ra_status = 'Lost';
                    } elseif ($ra->status === 'Lost - H1 Elimination') {
                        $ra_status = 'Lost - H1 Elimination';
                    }

                    // Check if RA Start time is reached
                    if ($ra->start_time) {
                        $raStartTime = Carbon::parse($ra->start_time);
                        if (Carbon::now()->greaterThanOrEqualTo($raStartTime)) {
                            $ra_status = 'RA Started';
                        }
                    }

                    // Check if RA End time is reached
                    if ($ra->end_time) {
                        $raEndTime = Carbon::parse($ra->end_time);
                        if (Carbon::now()->greaterThanOrEqualTo($raEndTime)) {
                            $ra_status = 'RA Ended';
                        }
                    }
                }

                // Return the status
                return $ra_status;
            })
            ->addColumn('action', function ($tender) use ($type) {
                return view('partials.ra-action', ['tdr' => $tender]);
            })
            ->rawColumns(['tender_name', 'bid_submissions_date', 'action'])
            ->make(true);
        return $dataTable;
    }

    public function show($id)
    {
        try {
            $tender = TenderInfo::where('id', $id)->first();
            if (!$tender) {
                return redirect()->back()->with('error', 'Tender not found.');
            }

            $rfq = Rfq::where('tender_id', $id)->first();
            return view('ra_mgmt.show', compact('tender', 'rfq'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function schedule(Request $request, $id)
    {
        Log::info('Starting RA scheduling process');

        // Validate request first
        $validated = $request->validate([
            'tender_no' => 'required|string',
            'technically_qualified' => 'required|in:yes,no',
            'disqualification_reason' => 'nullable',
            'qualified_parties_count' => 'nullable|nullable|integer',
            'qualified_parties.*' => 'nullable|nullable|string',
            'start_time' => 'required_if:technically_qualified,yes|nullable|date',
            'end_time' => 'required_if:technically_qualified,yes|nullable|date|after:start_time',
        ]);

        // Check if RA exists for this tender
        $ra = RaMgmt::where('tender_no', $request->tender_no)->first();

        if (!$ra) {
            // Create new RA if doesn't exist
            $ra = RaMgmt::create([
                'tender_no' => $request->tender_no,
            ]);
            Log::info('New RA created for tender: ' . $request->tender_no);
        }

        if ($request->technically_qualified === 'no') {
            $ra->update([
                'status' => 'Disqualified',
                'technically_qualified' => 'no',
                'disqualification_reason' => $request->disqualification_reason
            ]);

            // Update the tender status to 'Disqualified' means 22
            $tstatus = TenderInfo::where('id', $ra->tender_no)->update(['status' => '22']);
            if (!$tstatus) {
                Log::error('Failed to update tender status to Disqualified');
                return redirect()->back()->with('error', 'Failed to update tender status.');
            }
            Log::info('RA has been marked as disqualified.');
            return redirect()->back()->with('success', 'Tender has been marked as disqualified.');
        }

        // Schedule RA
        $ra->update([
            'technically_qualified' => 'yes',
            'qualified_parties' => json_encode($request->qualified_parties),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'RA Scheduled'
        ]);
        // Update the tender status to 'RA Scheduled' means 23
        $tstatus = TenderInfo::where('id', $ra->tender_no)->update(['status' => '23']);
        if (!$tstatus) {
            Log::error('Failed to update tender status to RA Scheduled');
            return redirect()->back()->with('error', 'Failed to update tender status.');
        }

        Log::info('RA has been scheduled successfully.');
        return redirect()->back()->with('success', 'RA has been scheduled successfully.');
    }

    public function uploadResult(Request $request, $id)
    {
        Log::info('Starting RA result upload process');

        try {
            // Validate request first
            $validated = $request->validate([
                'tender_no' => 'required|string',
                'ra_result' => 'required|in:won,lost,h1_elimination',
                've_l1_start' => 'required|in:yes,no',
                'ra_start_price' => 'required|numeric',
                'ra_close_price' => 'required|numeric',
                'ra_close_time' => 'required|date',
                'qualified_parties_screenshot' => 'required|file|mimes:jpeg,png,pdf',
                'decrements_screenshot' => 'required|file|mimes:jpeg,png,pdf',
                'final_result' => 'required|file|mimes:jpeg,png,pdf',
            ]);

            // Check if RA exists for this tender
            $ra = RaMgmt::where('tender_no', $request->tender_no)->first();

            if (!$ra) {
                // Create new RA if doesn't exist
                $ra = RaMgmt::create([
                    'tender_no' => $request->tender_no,
                ]);
                Log::info('New RA created for tender: ' . $request->tender_no);
            }

            Log::info('Validation passed for RA result upload');

            // Handle file uploads with error checking
            $paths = [];
            $files = [
                'qualified_parties_screenshot' => 'qualified_parties',
                'decrements_screenshot' => 'decrements',
                'final_result' => 'final_result'
            ];

            foreach ($files as $input => $prefix) {
                if ($request->hasFile($input)) {
                    $file = $request->file($input);
                    $filename = $prefix . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

                    try {
                        $path = $file->move(public_path('ra_results'), $filename);
                        if (!$path) {
                            throw new \Exception("Failed to store file: {$input}");
                        }
                        $paths[$input] = $filename; // Store only filename in database
                        Log::info("File uploaded successfully: {$input}");
                    } catch (\Exception $e) {
                        Log::error("File upload error for {$input}: " . $e->getMessage());
                        return redirect()->back()
                            ->with('error', "Failed to upload {$input}")
                            ->withInput();
                    }
                }
            }

            if ($request->ra_result === 'h1_elimination') {
                $status = 'Lost - H1 Elimination';
                TenderInfo::where('id', $ra->tender_no)->update(['status' => '24']);
            } else if ($request->ra_result === 'won') {
                $status = 'Won';
                TenderInfo::where('id', $ra->tender_no)->update(['status' => '25']);
            } else if ($request->ra_result === 'lost') {
                $status = 'Lost';
                TenderInfo::where('id', $ra->tender_no)->update(['status' => '24']);
            }
            // Update RA details
            $ra->update([
                'result' => $request->ra_result,
                've_start_of_ra' => $request->ve_l1_start,
                'start_price' => $request->ra_start_price,
                'close_price' => $request->ra_close_price,
                'close_time' => $request->ra_close_time,
                'screenshot_qualified_parties' => $paths['qualified_parties_screenshot'] ?? null,
                'screenshot_decrements' => $paths['decrements_screenshot'] ?? null,
                'final_result_screenshot' => $paths['final_result'] ?? null,
                'status' => $status
            ]);
            
            // send mail
            if ($this->sendRaResultMail($ra->tender_no)) {
                Log::info('RA result email sent successfully');
            } else {
                Log::error('Failed to send RA result email');
            }
            
            Log::info('RA details updated successfully');
            return redirect()->back()->with('success', 'RA results have been uploaded successfully.');
        } catch (\Exception $e) {
            Log::error("RA Result Upload Error: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error uploading RA results: ' . $e->getMessage());
        }
    }
    
    // MAILS

    public function sendRaScheduledMail($id)
    {
        try {
            $ra = RaMgmt::where('tender_no', $id)->first();
            if (!$ra) {
                return redirect()->back()->with('error', 'RA not found.');
            }

            $tender = TenderInfo::where('id', $ra->tender_no)->first();
            if (!$tender) {
                return redirect()->back()->with('error', 'Tender not found.');
            }

            $te = User::find($tender->team_member);
            if (!$te) {
                Log::error("❌ Tender Executive not found for tender_id: {$tender->id}");
                return back()->with('error', 'Tender Executive not found.');
            }
            $admin = User::where('role', 'admin')->pluck('email')->toArray();
            $cord = User::where('role', 'coordinator')->where('team', $te->team)->first();
            $tl = User::where('role', 'team-leader')->where('team', $te->team)->first();

            // Calculate time until start
            $raStartTime = Carbon::parse($ra->start_time);
            $now = Carbon::now();
            $timeUntilStart = $raStartTime->diffInMinutes($now);
            $timeUntilStart = ($timeUntilStart < 0) ? '0' : $raStartTime->diff($now)->format('%H:%I');

            $data = [
                'tl_name' => $tl->name,
                'tender_no' => $ra->tender_no,
                'tender_name' => $tender->tender_name,
                'ra_start_time' => Carbon::parse($ra->start_time)->format('d-m-Y H:i'),
                'ra_end_time' => Carbon::parse($ra->end_time)->format('d-m-Y H:i'),
                'time_until_start' => $timeUntilStart,
            ];

            MailHelper::configureMailer($cord->email, $cord->app_password, $cord->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)
                ->to([$tl->email, $te->email])
                ->cc($admin)
                ->send(new RaScheduleMail($data));

            if ($mail->failures()) {
                Log::error('Failed to send RA scheduled email: ' . implode(', ', Mail::failures()));
            }
            Log::info('RA scheduled email sent successfully to: ' . implode(', ', [$tl->email, $te->email]));

            return redirect()->back()->with('success', 'RA scheduled email sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function sendRaResultMail($id)
    {
        try {
            $ra = RaMgmt::where('tender_no', $id)->first();
            if (!$ra) {
                return redirect()->back()->with('error', 'RA not found.');
            }
            $tender = TenderInfo::where('id', $ra->tender_no)->first();
            if (!$tender) {
                return redirect()->back()->with('error', 'Tender not found.');
            }

            $te = User::find($tender->team_member);
            if (!$te) {
                Log::error("❌ Tender Executive not found for tender_id: {$tender->id}");
                return back()->with('error', 'Tender Executive not found.');
            }
            $admin = User::where('role', 'admin')->pluck('email')->toArray();
            $cord = User::where('role', 'coordinator')->where('team', $te->team)->first();
            $tl = User::where('role', 'team-leader')->where('team', $te->team)->first();

            // Calculate time until start
            $raStartTime = Carbon::parse($ra->start_time);
            $now = Carbon::parse($ra->close_time);
            $timeUntilStart = $raStartTime->diffInMinutes($now);
            $timeUntilStart = ($timeUntilStart < 0) ? '0' : $raStartTime->diff($now)->format('%H:%I');

            $data = [
                'tl_name' => $tl->name,
                'tender_no' => $tender->tender_no,
                'tender_name' => $tender->tender_name,
                'ra_result' => $ra->result,
                've_l1_start' => $ra->ve_start_of_ra,
                'ra_start_price' => $ra->start_price,
                'ra_close_price' => $ra->close_price,
                'ra_duration' => $timeUntilStart,
                'qualified_parties_screenshot' => $ra->screenshot_qualified_parties,
                'decrements_screenshot' => $ra->screenshot_qualified_parties,
                'final_result' => $ra->final_result_screenshot,
            ];

            MailHelper::configureMailer($cord->email, $cord->app_password, $cord->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)
                ->to([$te->email, $tl->email])
                ->cc($admin)
                ->send(new RaResultMail($data));

            if (!$mail) {
                Log::error('Failed to send RA scheduled email.');
            }
            Log::info('RA scheduled email sent successfully to: ' . implode(', ', [$tl->email, $te->email]));
            return redirect()->back()->with('success', 'RA result email sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
