<?php

namespace App\Jobs;

use App\Helpers\MailHelper;
use App\Mail\FollowupPersonMail;
use App\Models\FollowUpPersons;
use App\Models\FollowUps;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FollowupSendMail implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('Starting Job For Sending Followup Mail');
        try {
            Log::info('followupMail process for ID: ' . $this->id);

            $fu = FollowUps::find($this->id);
            if (!$fu) {
                Log::error('FollowUp not found for ID: ' . $this->id);
                return response()->json(['error' => 'FollowUp not found'], 404);
            }

            $creator = User::where('id', $fu->created_by)->first();
            $creatorMail = $creator->email;
            Log::info('Creator found: ' . $creator->name . ', Email: ' . $creatorMail);

            $user = User::where('id', $fu->assigned_to)->first();
            $assignee = $user->name;
            $userMail = $user->email;
            $appPass = $user->app_password;
            Log::info('Assignee found: ' . $assignee . ', Email: ' . $userMail);

            $fup = FollowUpPersons::where('follwup_id', $this->id)->get();
            $adminMail = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $cooMail = User::where('role', 'coordinator')->first()->email ?? 'gyanprakashk55@gmail.com';
            $email = [];

            foreach ($fup as $key => $value) {
                Log::info('Processing Followup Person: ' . json_encode($value));
                if (empty($value->email)) {
                    Log::warning('Skipping Followup Person with empty email: ' . $value->name);
                    continue;
                }
                $email[] = $value->email;
            }
            Log::info('Collected emails for followup: ' . json_encode($email));

            $start_date = Carbon::parse($fu->start_from);
            $today = Carbon::now();
            Log::info('Start date: ' . $start_date . ', Today: ' . $today);

            if ($start_date->lte($today)) {
                $day = max(1, round($today->diffInDays($start_date)));
                $data = [
                    'for' => $fu->followup_for,
                    'since' => $day,
                    'mail' => $fu->details,
                    'files' => json_decode($fu->attachments),
                ];
                Log::info('Mail data prepared: ' . json_encode($data));

                MailHelper::configureMailer($userMail, $appPass, $assignee);
                $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
                Log::info('Using mailer: ' . $mailer);
                $newMail = new Mail();
                $newMail::mailer($mailer)->to($email)
                    ->cc([$adminMail, $cooMail, $creatorMail])
                    ->send(new FollowupPersonMail($data));
                Log::info('Followup Mail sent successfully to: ' . json_encode($email));
            } else {
                Log::info('No followup mail sent as the start date is in the future');
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('Error during followupMail process: ' . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}

/*
MailHelper::configureMailer($this->userMail, $this->appPass, $this->assignee);
$mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
Log::info('Using mailer: ' . $mailer);
$newMail = new Mail();
$newMail::mailer($mailer)->to($this->email)
    ->cc([$this->adminMail, $this->cooMail, $this->creatorMail])
    ->send(new FollowupPersonMail($this->data));

*/
