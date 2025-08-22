<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wodetails;
use App\Helpers\MailHelper;
use App\Mail\MinuteOfMeetingMail;
use App\Mail\KickoffMeetingMail;
use App\Models\Basic_detail;
use Illuminate\Http\Request;
use App\Services\TimerService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class KickoffmeetingController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function kickmeeting_dashbord()
    {
        $basic = Basic_detail::with('tenderName', 'wo_details')->get();
        // dd($basic);
        return view('kickmeeting.meetingdashboard', compact('basic'));
    }

    public function viewbutten_dashboard($id)
    {
        $basic = Basic_detail::with('wo_details', 'wo_acceptance_yes')->findOrFail($id);
        $woDetails = $basic->wo_details;
        if ($woDetails) {
            $woData = [
                'departments' => json_decode($woDetails->departments, true),
                'name' => json_decode($woDetails->name, true),
                'designation' => json_decode($woDetails->designation, true),
                'phone' => json_decode($woDetails->phone, true),
                'email' => json_decode($woDetails->email, true),
            ];
        } else {
            $woData = [];
            Log::warning('Wodetails not found for basic detail ID.', ['basic_id' => $id]);
        }
        $woAcceptance = $basic->wo_acceptance_yes;
        if ($woAcceptance?->wo_yes) {
            $changes = [
                'page' => json_decode($woAcceptance->page_no, true),
                'clause' => json_decode($woAcceptance->clause_no, true),
                'current' => json_decode($woAcceptance->current_statement, true),
                'correct' => json_decode($woAcceptance->corrected_statement, true),
            ];
        } else {
            $changes = [];
            Log::warning('Wo_acceptance_yes not found for basic detail ID.', ['basic_id' => $id]);
        }

        return view('kickmeeting.viewbutten_dashboard', compact('basic', 'woData', 'changes'));
    }

    public function initiate_meeting($id)
    {
        $datawo = Wodetails::where('id', $id)->first();
        $tender = $datawo->basic_detail?->tenderName;
        if ($datawo) {
            $woData = [
                'organization' => json_decode($datawo->organization, true),
                'departments' => json_decode($datawo->departments, true),
                'name' => json_decode($datawo->name, true),
                'designation' => json_decode($datawo->designation, true),
                'phone' => json_decode($datawo->phone, true),
                'email' => json_decode($datawo->email, true),
            ];
        } else {
            $woData = [];
            Log::warning('Wodetails not found for basic detail ID.', ['basic_id' => $id]);
        }
        return view('kickmeeting.initiate_meeting', compact('datawo', 'tender', 'woData'));
    }

    public function initiate_meeting_post(Request $request)
    {
        Wodetails::where('id', $request->id)->update([
            'name' => json_encode($request->name),
            'organization' => json_encode($request->organization),
            'departments' => json_encode($request->departments),
            'phone' => json_encode($request->phone),
            'email' => json_encode($request->email),
            'designation' => json_encode($request->designation),
            'meeting_date_time' => $request->meeting_date_time,
            'google_meet_link' => $request->google_meet_link,
        ]);

        // Stop kickoff_meeting timer
        $wo = Wodetails::find($request->id);
        $tender = $wo->basic_detail?->tenderName;
        $this->timerService->stopTimer($tender, 'kickoff_meeting');

        if ($this->initialate_meeting_mail($wo->id)) {
            Log::info('Kickoff meeting mail sent successfully.');
            return redirect()->route('kickmeeting_dashbord')->with('success', 'Kickoff meeting data updated and mail sent successfully.');
        } else {
            Log::error('Failed to send kickoff meeting mail.');
            return redirect()->route('kickmeeting_dashbord')->with('success', 'Kickoff meeting data updated but mail not sent.');
        }
    }

    public function uplode_mom(Request $request)
    {
        Log::info('KickoffmeetingController::uplode_mom called', ['request' => $request->all()]);

        if ($request->hasFile('uplode_mom')) {
            Log::info('Uploading MoM file');
            $existingMom = Wodetails::where('basic_detail_id', $request->id)->value('upload_mom');
            if ($existingMom) {
                Log::info('Deleting existing MoM file');
                unlink(public_path("upload/applicable/$existingMom"));
            }
            $momName = time() . 'MOM.' . $request->uplode_mom->extension();
            $request->uplode_mom->move(public_path('upload/applicable'), $momName);
            Wodetails::where('basic_detail_id', $request->id)->update([
                'upload_mom' => $momName
            ]);
            Log::info('MoM file uploaded successfully');
        }

        Log::info('Sending MoM mail');
        if ($this->mom_mail($request->id)) {
            Log::info('MoM mail sent successfully.');
            return redirect()->route('kickmeeting_dashbord')->with('success', 'MoM data updated and mail sent successfully.');
        } else {
            Log::error('Failed to send MoM mail.');
            return redirect()->route('kickmeeting_dashbord')->with('success', 'MoM data updated but mail not sent.');
        }
    }

    public function initialate_meeting_mail($id)
    {
        Log::info('KickoffmeetingController::initialate_meeting_mail called', ['id' => $id]);

        $wo = Wodetails::findOrFail($id);
        $tender = $wo->basic_detail?->tenderName;

        $user = $tender->users ?? Auth::user();
        $data = [
            'date' => date('d-m-Y', strtotime($wo->meeting_date_time ?? '')),
            'time' => date('h:i A', strtotime($wo->meeting_date_time ?? '')),
            'link' => $wo->google_meet_link,
            'te_name' => $user->name ?? '',
            'te_mob' => $user->mobile ?? '',
            'te_mail' => $user->email ?? '',
        ];

        Log::info('KickoffmeetingController::initialate_meeting_mail data prepared', ['data' => $data]);

        $to = [];
        if ($wo && is_array($emails = json_decode($wo->email, true)) && count($emails)) {
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $to[] = $email;
                } else {
                    Log::warning('Invalid email found in Wodetails email field.', ['email' => $email]);
                }
            }
        } else {
            Log::warning('Wodetails email field is not an array or is empty.', ['email' => $wo?->email]);
        }

        $cc = array_merge(
            User::where('role', 'team-leader')->where('team', $user->team)->pluck('email')->toArray(),
            User::where('role', 'admin')->pluck('email')->toArray()
        );

        // MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Gyan');
        MailHelper::configureMailer($user->email, $user->app_password, $user->name);
        $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

        $mail = Mail::mailer($mailer)
            ->to($to)
            ->cc($cc)
            ->send(new KickoffMeetingMail($data));

        if ($mail) {
            Log::info('KickoffmeetingController::initialate_meeting_mail email sent successfully', ['to' => $to, 'cc' => $cc]);
            return true;
        } else {
            Log::error('KickoffmeetingController::initialate_meeting_mail failed to send email');
            return false;
        }
    }

    public function mom_mail($id)
    {
        Log::info('KickoffmeetingController::mom_mail called', ['id' => $id]);

        $wo = Wodetails::where('basic_detail_id', $id)->first();

        $tender = $wo->basic_detail?->tenderName;
        
        $user = $tender->users ?? Auth::user();
        $data = [
            'wo_no' => $wo->basic_detail?->number,
            'date' => date('d-m-Y h:i A', strtotime($wo->meeting_date_time ?? '')),
            'te_name' => $user->name ?? '',
            'te_mob' => $user->mobile ?? '',
            'te_mail' => $user->email ?? '',
            'files' => [$wo->upload_mom]
        ];

        Log::info('KickoffmeetingController::mom_mail data prepared', ['data' => $data]);

        $to = [];
        if ($wo && is_array($emails = json_decode($wo->email, true)) && count($emails)) {
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $to[] = $email;
                } else {
                    Log::warning('Invalid email found in Wodetails email field.', ['email' => $email]);
                }
            }
        } else {
            Log::warning('Wodetails email field is not an array or is empty.', ['email' => $wo?->email]);
        }

        $cc = array_merge(
            User::where('role', 'team-leader')->where('team', $user->team)->pluck('email')->toArray(),
            User::where('role', 'admin')->pluck('email')->toArray()
        );

        // MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Gyan');
        MailHelper::configureMailer($user->email, $user->app_password, $user->name);
        $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

        $mail = Mail::mailer($mailer)
            ->to($to)
            ->cc($cc)
            ->send(new MinuteOfMeetingMail($data));

        if ($mail) {
            Log::info('KickoffmeetingController::mom_mail email sent successfully', ['to' => $to, 'cc' => $cc]);
            return true;
        } else {
            Log::error('KickoffmeetingController::mom_mail failed to send email');
            return false;
        }
    }
}
