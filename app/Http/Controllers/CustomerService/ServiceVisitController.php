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

class ServiceVisitController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function index()
        {
            return view('service.customer.service_visit.index');
        }

       // Show first page: details form
    public function step_1_show($complaintId)
    {
        $complaint = CustomerComplaint::findOrFail($complaintId);
        $serviceReport = ServiceReport::where('complaint_id', $complaintId)->first();
        
        if($serviceReport){
            return view('service.customer.service_visit.alreadySubmitted');
        }
        return view('service.customer.service_visit.step_1', compact('complaintId'));
    }

    // Handle form submission and store in session
    public function step_1_store(Request $request)
    {
        $complaintId = $request->complaint_id;
        $complaint = CustomerComplaint::findOrFail($complaintId);
        $serviceReport = ServiceReport::where('complaint_id', $complaintId)->first();
        
        if($serviceReport){
             return view('service.customer.service_visit.alreadySubmitted');
            
        }
        $validated = $request->validate([
            'visit_done'      => 'required|in:0,1',
            'visit_datetime'  => 'required|date',
            'resolution_done' => 'required|in:0,1',
            'remarks'         => 'nullable|string',
        ]);

        // Store in session under complaintId
        session()->put("service_visit_form.$complaintId", $validated);

        // Redirect to uploads page
        return redirect()->route('service_visit.public.step2', $complaintId)
            ->with('success', 'Details saved successfully, now upload files.');
    }

    public function step_2_show($complaintId)
    {
        $step1Data = session()->get("service_visit_form.$complaintId");

        if (!$step1Data) {
            return redirect()->route('service_visit.public.step1', $complaintId)
                ->with('error', 'Step 1 details not found. Please fill them again.');
        }
        // $complaint = CustomerComplaint::findOrFail($complaintId);
        // $serviceReport = ServiceReport::where('complaint_id', $complaintId)->first();
        return view('service.customer.service_visit.step_2', compact('complaintId'));
    }


    // public function createPublic($complaintId)
    // {
    //     $complaint = CustomerComplaint::findOrFail($complaintId);
    //     $serviceReport = ServiceReport::where('complaint_id', $complaintId)->first();
        
    //     if($serviceReport){
    //          return view('service.customer.service_visit.alreadySubmitted');
            
    //     }

    //     return view('service.customer.service_visit.service-visit-public', compact('complaint','complaintId'));
    // }


    public function step_2_store(Request $request)
    {
        $request->validate([
            'complaint_id'           => 'required|integer|exists:customer_complaints,id',
            // Only validate uploads here
            'resolution_photos.*'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'unsigned_visit_report.*'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'visit_report.*'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $complaintId = $request->complaint_id;

        // -----------------
        // 1. Get stored session values from step 1
        // -----------------
        $step1Data = session()->get("service_visit_form.$complaintId");

        if (!$step1Data) {
            return redirect()->route('service_visit.public.step1', $complaintId)
                ->with('error', 'Step 1 details not found. Please fill them again.');
        }

        $complaint = CustomerComplaint::with('serviceEngineer')->findOrFail($complaintId);

        // -----------------
        // 2. Prepare base data (merge session + engineer ID)
        // -----------------
        $data = [
            'complaint_id'        => $complaintId,
            'service_engineer_id' => $complaint->serviceEngineer->id,
            'remarks'             => $step1Data['remarks'] ?? null,
            'resolution_done'     => $step1Data['resolution_done'],
            'visit_done'          => $step1Data['visit_done'],
            'visit_datetime'      => $step1Data['visit_datetime'],
            'unsigned_photo'      => json_encode([]),
            'signed_photo'        => json_encode([]),
            'resolved_photo'      => json_encode([]),
        ];

        // -----------------
        // 2. Handle session-based (camera) photos
        // -----------------
        foreach (['signed', 'unsigned', 'resolved'] as $type) {
            $photos = session()->get("service_visit_photos.$type", []);

            $storedPaths = [];
            if (!empty($photos)) {
                foreach ($photos as $photo) {
                    $dataUri = preg_replace('#^data:image/\w+;base64,#i', '', $photo);
                    $decoded = base64_decode($dataUri);
                    $path = "service_photos/$type/" . uniqid() . ".jpg";

                    Storage::disk('public')->put($path, $decoded);
                    $storedPaths[] = $path;
                }
            }

            $data[$type . '_photo'] = json_encode($storedPaths);
        }

        // -----------------
        // 3. Handle file uploads
        // -----------------
        $resolvedPaths = [];
        if ($request->hasFile('resolution_photos')) {
            foreach ($request->file('resolution_photos') as $file) {
                $path = $file->store('service_photos/resolved', 'public');
                $resolvedPaths[] = $path;
            }
        }
        $data['resolved_photo'] = json_encode(array_merge(json_decode($data['resolved_photo'], true), $resolvedPaths));

        $unsignedPaths = [];
        if ($request->hasFile('unsigned_visit_report')) {
             foreach ($request->file('unsigned_visit_report') as $file) {
                $path = $file->store('visit_reports/unsigned', 'public');
                $unsignedPaths[] = $path;
             }
        }
        $data['unsigned_photo'] = json_encode(array_merge(json_decode($data['unsigned_photo'], true), $unsignedPaths));

        $signedPaths = [];
        if ($request->hasFile('visit_report')) {
            foreach ($request->file('visit_report') as $file) {
                $path = $file->store('visit_reports/signed', 'public');
                $signedPaths[] = $path;
            }
        }
        $data['signed_photo'] = json_encode(array_merge(json_decode($data['signed_photo'], true), $signedPaths));

        // dd($data);
        // -----------------
        // 4. Save in DB
        // -----------------
        ServiceReport::create($data);


        //Mailing 

        // dd($data['resolution_done']);
        if($data['resolution_done']){
            // dd('heelo1');
            if ($this->serviceResolvedMail($data)) {
                Log::info('Mail sent to Service Resolved successfully');
            } else {
                Log::error('Mail not Service Resolved successfully');
            }
        }
        else {
            // dd('hello');
            if ($this->serviceNotResolvedMail($data)) {
                Log::info('Mail sent to Service Not Resolved successfully');
            } else {
                Log::error('Mail not sent to Service Not Resolved successfully');
            }
        }

        // -----------------
        // 5. Cleanup
        // -----------------
        session()->forget('service_visit_photos');
        session()->forget("service_visit_form.$complaintId");

        return redirect()->route('service_visit.public.success')
            ->with('success', 'Service Visit logged successfully.');
    }
    
    // public function storePublic(Request $request)
    // {

    //     $request->validate([
    //         'complaint_id'         => 'required|integer|exists:customer_complaints,id',
    //         'visit_done'           => 'required|string',
    //         'visit_datetime'       => 'required|date',
    //         'resolution_done'      => 'required|string|in:0,1',
    //         'remarks'              => 'nullable|string',
    //         // File validations
    //         'resolution_photos.*'  => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
    //         'unsigned_visit_report.*'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    //         'visit_report.*'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    //     ]);
    //     $complaintId = request()->complaint_id;


    //     $complaint = CustomerComplaint::with('serviceEngineer')->findOrFail($request->complaint_id);

    //     // -----------------
    //     // 1. Prepare data
    //     // -----------------
    //     $data = [
    //         'complaint_id'        => $request->complaint_id,
    //         'service_engineer_id' => $complaint->serviceEngineer->id,
    //         'remarks'             => $request->remarks,
    //         'resolution_done'     => $request->resolution_done,
    //         // store as JSON arrays
    //         'unsigned_photo'      => json_encode([]),
    //         'signed_photo'        => json_encode([]),
    //         'resolved_photo'      => json_encode([]),
    //     ];



    //     // -----------------
    //     // 2. Handle session-based (camera) photos
    //     // -----------------
    //     foreach (['signed', 'unsigned', 'resolved'] as $type) {
    //         $photos = session()->get("service_visit_photos.$type", []);

    //         $storedPaths = [];
    //         if (!empty($photos)) {
    //             foreach ($photos as $photo) {
    //                 $dataUri = preg_replace('#^data:image/\w+;base64,#i', '', $photo);
    //                 $decoded = base64_decode($dataUri);
    //                 $path = "service_photos/$type/" . uniqid() . ".jpg";

    //                 Storage::disk('public')->put($path, $decoded);
    //                 $storedPaths[] = $path;
    //             }
    //         }

    //         $data[$type . '_photo'] = json_encode($storedPaths);
    //     }

    //     // -----------------
    //     // 3. Handle file uploads
    //     // -----------------
    //     $resolvedPaths = [];
    //     if ($request->hasFile('resolution_photos')) {
    //         foreach ($request->file('resolution_photos') as $file) {
    //             $path = $file->store('service_photos/resolved', 'public');
    //             $resolvedPaths[] = $path;
    //         }
    //     }
    //     $data['resolved_photo'] = json_encode(array_merge(json_decode($data['resolved_photo'], true), $resolvedPaths));

    //     $unsignedPaths = [];
    //     if ($request->hasFile('unsigned_visit_report')) {
    //          foreach ($request->file('unsigned_visit_report') as $file) {
    //             $path = $file->store('visit_reports/unsigned', 'public');
    //             $unsignedPaths[] = $path;
    //          }
    //     }
    //     $data['unsigned_photo'] = json_encode(array_merge(json_decode($data['unsigned_photo'], true), $unsignedPaths));

    //     $signedPaths = [];
    //     if ($request->hasFile('visit_report')) {
    //         foreach ($request->file('visit_report') as $file) {
    //             $path = $file->store('visit_reports/signed', 'public');
    //             $signedPaths[] = $path;
    //         }
    //     }
    //     $data['signed_photo'] = json_encode(array_merge(json_decode($data['signed_photo'], true), $signedPaths));

    //     // dd($data);
    //     // -----------------
    //     // 4. Save in DB
    //     // -----------------
    //     ServiceReport::create($data);


    //     //Mailing 

    //     // dd($request->resolution_done);
    //     if($request->resolution_done){
    //         if ($this->serviceResolvedMail($data['complaint_id'])) {
    //             Log::info('Mail sent to Service Co-ordinator successfully');
    //         } else {
    //             Log::error('Mail not sent to Service Co-ordinator');
    //         }
    //     }
    //     else {
    //         if ($this->serviceResolvedMail($data['complaint_id'])) {
    //             Log::info('Mail sent to Service Co-ordinator successfully');
    //         } else {
    //             Log::error('Mail not sent to Service Co-ordinator');
    //         }
    //     }

    //     // -----------------
    //     // 5. Cleanup
    //     // -----------------
    //     session()->forget('service_visit_photos');
    //     session()->forget("service_visit_form.$complaintId");

    //     return redirect()->route('service_visit.public.success')
    //         ->with('success', 'Service Visit logged successfully.');
    // }

    public function showCamera($type , $complaintId, Request $request){
        // dd($request->all());
        // $formData = $request->except(['_token']); 
        // session()->put("service_visit_form.$complaintId", $formData);
        
        return view('service.customer.service_visit.click-photos', compact('type', 'complaintId'));
    }

    public function saveTemp(Request $request)
    {
        session()->put('service_visit_form', $request->except('_token', 'redirect_to'));

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo === 'before') {
            return redirect()->route('service_visit.public.camera', ['type' => 'before']);
        }
    }

    public function storePhotos(Request $request)
    {

        $complaintId = $request->input('complaintId');
        $type   = $request->input('type'); // signed / unsigned / resolved
        $photos = $request->input('captured_images', []);

        if (!$type) {
            return back()->with('error', 'Photo type is required.');
        }
        // Store photos in session under correct type
        $key = "service_visit_photos.$type"; // e.g. captured_photos.signed
        session()->put($key, $photos);

        return redirect()->route('service_visit.public.step2', $complaintId)->with('success', ucfirst($type) . ' photos saved.');
    }


    public function success(){
        return view('service.customer.service_visit.success');
    }


    public function getCustomerComplaintsData(Request $request)
    {
        $query = CustomerComplaint::with(['serviceEngineer', 'serviceReport'])
            ->where('status', '>=' ,'2')
            ->orderBy('created_at', 'desc');


        // Global search (search box)
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('ticket_no', 'like', "%{$search}%")
                    ->orWhere('organization', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('site_project_name', 'like', "%{$search}%")
                    ->orWhere('po_no', 'like', "%{$search}%")
                    ->orWhere('site_location', 'like', "%{$search}%")
                    ->orWhere('issue_faced', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addColumn('ticket_no', fn($complaint) => 
                "{$complaint->ticket_no}"
            )
            ->addColumn('customer', fn($complaint) => 
                "{$complaint->name}<br>{$complaint->phone}<br>{$complaint->email}"
            )
            ->addColumn('project', fn($complaint) => 
                "{$complaint->site_project_name}<br>PO: {$complaint->po_no}"
            )
            ->addColumn('location', fn($complaint) => $complaint->site_location ?? 'N/A')
            ->addColumn('organization', fn($complaint) => $complaint->organization ?? 'N/A')
            ->addColumn('designation', fn($complaint) => $complaint->designation ?? 'N/A')
            ->addColumn('attachment', function ($complaint) {
                if ($complaint->attachment) {
                    $url = asset('storage/complaints/' . $complaint->attachment);
                    return "<a href='{$url}' target='_blank'>View File</a>";
                }
                return 'N/A';
            })
            ->addColumn('issue', fn($complaint) => str($complaint->issue_faced)->limit(50))
            ->addColumn('service_visit', function ($complaint) {
                if (empty($complaint->serviceReport)) {
                    return 'Not Conducted';
                }
                else{
                    return 'Conducted';
                }
            })
            ->addColumn('status', function ($complaint) {
                if ($complaint->report && $complaint->report->resolution_done == 1) {
                    return 'Resolved';
                }
                return 'Not Resolved';
            })
            ->addColumn('serviceEngineer', function ($complaint) {
                if (empty($complaint->status) || !$complaint->serviceEngineer) {
                    return '-';
                }
                    return "Name: {$complaint->serviceEngineer->name}<br>" .
                    "Phone: {$complaint->serviceEngineer->phone}<br>" .
                    "Email: {$complaint->serviceEngineer->email}";
            })
            // ->addColumn('action', function($complaint) {
            //         $actions = '<div class="dropdown">
            //             <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton1"
            //                 data-bs-toggle="dropdown" aria-expanded="false">
            //                 <i class="fa fa-ellipsis-v"></i>
            //             </button>
            //             <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
            //                 <li>
            //                     <a class="dropdown-item" href="'.route('customer_service.show', $complaint->id).'">
            //                         View
            //                     </a>
            //                 </li>';

            //         if (in_array(Auth::user()->role, ['coordinator', 'admin'])) {
            //             $actions .= '<li>
            //                 <a class="dropdown-item" id="allotServiceEngineerBtn" href="#" data-bs-toggle="modal"
            //                     data-bs-target="#allotEngineerModal" data-complaint-id="'.$complaint->id.'">
            //                     Allot Service Engineer
            //                 </a>
            //             </li>';
            //         }

            //         $actions .= '</ul></div>';

            //         return $actions;
            //     })
            ->addColumn('timer', fn() => '<button class="btn btn-primary btn-sm ">No Timer</button>')
            ->rawColumns(['customer', 'project', 'attachment', 'action', 'timer','serviceEngineer'])
            ->make(true);
    }

    
    public function allotServiceEngineer(Request $request)  {
        // Validate input
        $data = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'complaint_id' => 'required|string|max:20',
        ]);

        $complaintId = $request->input('complaint_id');
        $complaint = CustomerComplaint::findOrFail($complaintId);
        
        $data['status'] = '1';       

        $engineer = ServiceEngineer::create($data);

        $complaint->update(['status' => 'Assigned Service Engineer']);

        if ($this->mailToServiceEngineer($complaint->id)) {
            Log::info('Mail sent to Service Engineer successfully');
        } else {
            Log::error('Mail not sent to Service Engineer');
        }

        // Redirect back with success message
        return redirect()->back()->with('success', 'Service engineer successfully allotted.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('lead.index')->with('success', 'Lead deleted successfully.');
    }


    // MAILS Begin from here
    //servicecompleted not implemented yet
    // public function serviceCompletedMail($id)
    // {
    //     try {
    //         $complaint = CustomerComplaint::findOrFail($id);
    //         $report = ServiceReport::where('complaint_id', '=', $id);
                   

    //         $coordinator = User::where('role','=','coordinator')->first();  
    //         // if($coordinator->role != 'coordintor'){
    //         //     return redirect()->back()->with('error', 'Authorization error, User Role is not Co-ordinator');
    //         // }
    //         $user = User::where('role' ,'=','coordinator')->where('team', '=', 'service')->first();
    //         // dd($user);
            
    //         $serviceCoordinatorEmail = $user->email;
    //         // $serviceCoordinatorEmail = "abhijeetgaur777@gmail.com";
    //         $serviceCoordinatorName = $user->name;
    //         $coordinatorName = $coordinator->name;
    //         // $coordinatorEmail = $coordinator->email;
            

    //         $clientName = $complaint->name;
    //         $organization= $complaint->organization;
    //         $siteName = $complaint->site_project_name;
    //         $contactNo = $complaint->phone;
    //         $issueFaced = $complaint->issue_faced;

    //         $data = [
    //             'clientName' => $clientName,
    //             'organization' => $organization,
    //             'siteName' => $siteName,
    //             'contactNo' => $contactNo,
    //             'issueFaced' => $issueFaced,
    //             'attachment' => $complaint->attachment,
    //         ];

    //         // Log::info('data: ' . json_encode($data));
    //         // Log::info('To: ' . $serviceCoordinatorEmail . ' From: ' . $coordinatorEmail);
    //         $mailer = MailHelper::configureMailer($coordinator->email, $coordinator->app_password, $coordinator->name);
    //         $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
    //         // Mail::mailer($mailer)->to($serviceCoordinatorEmail)
    //         //     ->send(new FollowupAssigned($data));

    //         Mail::mailer($mailer)->to($serviceCoordinatorEmail)
    //             ->send(new ComplaintServiceCoordinatorMail($data));  
            
    //         Log::info('Created Service Mail sent to Service Co-ordinator successfully');
            
    //         return response()->json(['success' => true]);
    //     } catch (\Throwable $th) {
    //         Log::error('Error Sending mail: ' . $th);
    //         return redirect()->back()->with('error', $th->getMessage());
    //     }
    // }

    public function serviceResolvedMail($data)
    {
        try {
            $id = $data['complaint_id'];
            $complaint = CustomerComplaint::findOrFail($id);

            $report = ServiceReport::where('complaint_id' , '=', $id)->first();
            // dd($report);
            
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
            $resolution_remark = $data['remarks'];
            $phone = $coordinator->mobile;
            $base_url =  "https://tms.volksenergie.in";
            $feedback_form_url = $base_url . "/customer-service/feedback/" . $id;

            $data = [
                'clientName' => $clientName,
                'organization' => $organization,
                'siteName' => $siteName,
                'contactNo' => $contactNo,
                'issueFaced' => $issueFaced,
                'attachment' => $complaint->attachment,
                'resolution_remark' => $resolution_remark,
                'phone' => $phone,
                'feedback_form_url' => $feedback_form_url,
                'ticket_no' => $complaint->ticket_no,
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

    public function serviceNotResolvedMail($data)
    {
        try {
            $id = $data['complaint_id'];
            $complaint = CustomerComplaint::findOrFail($id)->first();
            $report = ServiceReport::where('complaint_id' , '=', $id);
            // dd($data);

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
            $resolution_remark = $data['remarks'];
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
                'ticket_no' => $complaint->ticket_no,
            ];

            // Log::info('data: ' . json_encode($data));
            // Log::info('To: ' . $serviceCoordinatorEmail . ' From: ' . $coordinatorEmail);
            $mailer = MailHelper::configureMailer($coordinator->email, $coordinator->app_password, $coordinator->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            // Mail::mailer($mailer)->to($serviceCoordinatorEmail)
            //     ->send(new FollowupAssigned($data));

            Mail::mailer($mailer)->to($serviceCoordinatorEmail)
                ->send(new ServiceNotResolvedMail($data));  
            
            Log::info('Created Service Mail sent to Service Co-ordinator successfully');
            
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('Error Sending mail: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }


}