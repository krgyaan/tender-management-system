<?php

namespace App\Http\Controllers;

use App\Models\ReqExt;
use App\Models\TenderInfo;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Helpers\MailHelper;
use App\Mail\RequestExtensionMail;

class ReqExtController extends Controller
{
    public function create(Request $request, TenderInfo $id)
    {
        return view('tender.req-ext', compact('id'));
    }


    public function store(Request $request)
    {
        try {
            Log::info('Starting ReqExtController: store() method by ' . Auth::user()->name);
            $request->validate([
                'days' => 'required|integer|min:1',
                'reason' => 'required|string',
                'client_org' => 'required|string|max:255',
                'client_name' => 'required|string|max:255',
                'client_email' => 'required|email',
                'client_phone' => 'required|string|max:20',
            ]);

            $query = ReqExt::create([
                'tender_id' => $request->tender_id,
                'days' => $request->days,
                'reason' => $request->reason,
                'client_org' => $request->client_org,
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
                'status' => 'pending'
            ]);

            Log::info('Extension request submitted successfully by ' . Auth::user()->name);
            // Send email notification
            if ($this->sendMail($query->id)) {
                return redirect()->route('tender.index')
                    ->with('success', 'Extension request submitted and notification sent successfully.');
            }
            return redirect()->route('tender.index')
                ->with('success', 'Extension request submitted but notification failed to send.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit request: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function sendMail($queryId)
    {
        try {
            $query = ReqExt::with('tender')->find($queryId);
            $tender = $query->tender;
            $adminMail = User::where('role', 'admin')->where('team', 'DC')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->where('team', 'DC')->first()->email ?? 'gyanprakashk55@gmail.com';
            $coo = User::where('role', 'coordinator')->where('team', 'DC')->first();

            $data = [
                'tender_no' => $tender->tender_no,
                'tender_name' => $tender->tender_name,
                'days' => $query->days,
                'reason' => $query->reason,
                'assignee' => $tender->users->name,
                'te_mobile' => $tender->users->mobile,
                'te_email' => $tender->users->email,
                've_address' => 'B1/D8, 2nd Floor, Mohan Cooperative Industrial Estate, New Delhi - 110044',
            ];

            Log::info("Extension Request Mail Data: " . json_encode($data));

            // Configure mailer
            MailHelper::configureMailer($tender->users->email, $tender->users->app_password, $tender->users->name);

            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

            // Send mail to team member and cc to admin, team leader
            $mail = Mail::mailer($mailer)
                ->to($query->client_email)
                ->cc([$tlMail, $adminMail, $coo->email])
                ->send(new RequestExtensionMail($data));

            if ($mail) {
                Log::info("Extension Request Email sent successfully using " . $mailer);
                return true;
            } else {
                Log::error("Extension Request Email failed to send");
                return false;
            }
        } catch (\Throwable $th) {
            Log::error("Extension Request Mail Error: " . $th->getMessage());
            return false;
        }
    }
}
