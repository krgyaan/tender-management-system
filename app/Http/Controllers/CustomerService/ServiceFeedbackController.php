<?php

namespace App\Http\Controllers\CustomerService;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Helpers\MailHelper;
use App\Services\TimerService;
use App\Models\CustomerComplaint;
use App\Models\ServiceEngineer;
use App\Models\ServiceReport;
use App\Models\ServiceFeedback;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\CustomerService\ComplaintServiceCoordinatorMail;
use App\Mail\CustomerService\ServiceResolvedMail;
use App\Mail\CustomerService\ServiceNotResolvedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;

class ServiceFeedbackController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function index($id)
    {
        $complaint = CustomerComplaint::findOrFail($id);
        // dd($complaint);
        return view('service.customer.customer_feedback.create', compact('complaint'));
    }

    
    public function success(){
        return view('service.customer.customer_feedback.success');
    }

    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'complaint_id'     => 'required|integer|exists:customer_complaints,id',
            'problem_resolved' => 'required|in:1,0',
            'satisfaction'     => 'nullable|in:1,0',
            'rating'           => 'nullable|integer|min:1|max:10',
            'suggestions'      => 'nullable|string|max:1000',
        ];
        // dd($rules);

        $validated = $request->validate($rules);
        
        
        // If problem not resolved -> ignore other fields
        if ($validated['problem_resolved'] === '0') {
            $validated['satisfaction'] = null;
            $validated['rating'] = null;
            $validated['suggestions'] = null;
        }

        // Save feedback
        ServiceFeedback::create($validated);

        return redirect()->route('service_feedback.success');
    }




    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('lead.index')->with('success', 'Lead deleted successfully.');
    }


    public function serviceFeedbackMail($id)
    {
        try {
            $complaint = CustomerComplaint::findOrFail($id);
            $report = ServiceReport::where('complaint_id' , '=', $id)->first();
            dd($report);
            
            $coordinator = User::where('role' ,'=','coordinator')->where('team', '=', 'DC')->first(); 
             
            // if($coordinator->role != 'coordintor'){
            //     return redirect()->back()->with('error', 'Authorization error, User Role is not Co-ordinator');
            // }            
            $user = User::where('role' ,'=','coordinator')->where('team', '=', 'service')->first();
            // dd($user);

            
            $serviceCoordinatorEmail = $user->email;
            // $serviceCoordinatorEmail = "abhijeetgaur777@gmail.com";
            $serviceCoordinatorName = $user->name;
            $coordinatorName = $coordinator->name;
            // $coordinatorEmail = $coordinator->email;
            // dd($complaint);

            $clientName = $complaint->name;
            $organization= $complaint->organization;
            $siteName = $complaint->site_project_name;
            $contactNo = $complaint->phone;
            $issueFaced = $complaint->issue_faced;
            $resolution_remark = $report->remarks;
            $phone = $coordinator->mobile;

            $data = [
                'clientName' => $clientName,
                'organization' => $organization,
                'siteName' => $siteName,
                'contactNo' => $contactNo,
                'issueFaced' => $issueFaced,
                'attachment' => $complaint->attachment,
                'resolution_remark' => $resolution_remark,
                'phone' => $phone,
            ];

            // Log::info('data: ' . json_encode($data));
            // Log::info('To: ' . $serviceCoordinatorEmail . ' From: ' . $coordinatorEmail);
            $mailer = MailHelper::configureMailer($coordinator->email, $coordinator->app_password, $coordinator->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            // Mail::mailer($mailer)->to($serviceCoordinatorEmail)
            //     ->send(new FollowupAssigned($data));

            Mail::mailer($mailer)->to($serviceCoordinatorEmail)
                ->send(new ServiceResolvedMail($data));  
            
            Log::info('Created Service Mail sent to Service Co-ordinator successfully');
            
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('Error Sending mail: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }



}