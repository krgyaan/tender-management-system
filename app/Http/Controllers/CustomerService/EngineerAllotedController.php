<?php

namespace App\Http\Controllers\CustomerService;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Helpers\MailHelper;
use App\Services\TimerService;
use App\Models\CustomerComplaint;
use App\Models\ServiceEngineer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\CustomerService\ComplaintServiceCoordinatorMail;
use App\Mail\CustomerService\CustomerMail;
use App\Mail\CustomerService\ConferenceCallMail;
use App\Mail\CustomerService\ServiceEngineerMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class CustomerServiceController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function index()
    {
        return view('service.customer.index');
    }

    public function conferenceCallIndex()
    {
        return view('service.customer.conference_call_index');
    }


    public function create()
    {
        return view('service.customer.create');
    }

    public function createPublic()
    {
        $complaintCookie = Cookie::get('complaint');

        // Decode JSON into an array if it exists
        $complaintFromCookie = $complaintCookie ? json_decode($complaintCookie, true) : null;

        return view('service.customer.create-public', compact('complaintFromCookie', 'complaintCookie'));
    }

    public function show($id)
    {
        $complaint = CustomerComplaint::with('serviceEngineer')->findOrFail($id);


        return view('service.customer.show', compact('complaint'));
    }

    public function store(Request $request)
    {
        try{
            $user = auth()->user();
            // dd($user);
            if($user->role != 'coordinator' && $user->role !='admin'){
                 return redirect()->back()->with('error', 'Unauthorized User. Logged in user is not co-ordinator or Admin');
            }
            $request->validate([
                'name'              => 'required|string|max:255',
                'phone'             => 'required|string|max:20',
                'email'             => 'required|email|max:255',
                'site_project_name' => 'required|string|max:255',
                'po_no'             => 'required|string|max:100',
                'site_location'     => 'required|string|max:255',
                'attachment'        => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
                'issue_faced'       => 'nullable|string',
            ]);

            $data = $request->all();

            // Handle file upload
            if ($request->hasFile('attachment')) {

                $fileName = time() . '_' . $request->file('attachment')->getClientOriginalName();
                $request->file('attachment')->storeAs('complaints', $fileName, 'public');
                $data['attachment'] = $fileName;
            }

            $complaint = CustomerComplaint::create($data);

            // if ($this->mailToServiceCoordinator($complaint->id)) {
            //     Log::info('Mail sent to Service Co-ordinator successfully');
            // } else {
            //     Log::error('Mail not sent to Service Co-ordinator');
            //     return redirect()->route('customer_service.index')->with('error', 'Service Complaint created successfully but mail not sent to Service Co-ordinator');
            // }

            // if ($this->mailToCustomer($complaint->id)) {
            //     Log::info('Mail sent to Customer successfully');
            // } else {
            //     Log::error('Mail not sent to Customer');
            //     return redirect()->route('customer_service.index')->with('error', 'Service Complaint created successfully but mail not sent to Customer');
            // }

            // if ($this->allotServiceEngineer($complaint->id)) {
            //     Log::info('Mail sent to Service Engineer successfully');
            // } else {
            //     Log::error('Mail not sent to Service Engineer');
            //     return redirect()->route('customer_service.index')->with('error', 'Service Complaint created successfully but mail not sent to Service Engineer');
            // }


            return redirect()->route('customer_service.index')
                            ->with('success', 'Customer complaint registered successfully.');
        }
        catch(\Throwable $th){
            Log::error('Error service complaint store: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
       
    }

    
    public function storePublic(Request $request)
    {
        try{
            $user = auth()->user();
            // dd($user);
            // if($user->role != 'coordinator' && $user->role !='admin'){
            //      return redirect()->back()->with('error', 'Unauthorized User. Logged in user is not co-ordinator or Admin');
            // }
            $request->validate([
                'name'              => 'required|string|max:255',
                'phone'             => 'required|string|max:20',
                'email'             => 'required|email|max:255',
                'site_project_name' => 'required|string|max:255',
                'po_no'             => 'required|string|max:100',
                'site_location'     => 'required|string|max:255',
                'attachment'        => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
                'issue_faced'       => 'nullable|string',
            ]);

            $data = $request->all();

            // Handle file upload
            if ($request->hasFile('attachment')) {

                $fileName = time() . '_' . $request->file('attachment')->getClientOriginalName();
                $request->file('attachment')->storeAs('complaints', $fileName, 'public');
                $data['attachment'] = $fileName;
            }

            $complaint = CustomerComplaint::create($data);
            // dd($complaint);
            if ($this->mailToServiceCoordinator($complaint->id)) {
                Log::info('Mail sent to Service Co-ordinator successfully');
            } else {
                Log::error('Mail not sent to Service Co-ordinator');
            }

            if ($this->mailToCustomer($complaint->id)) {
                Log::info('Mail sent to Customer successfully');
            } else {
                Log::error('Mail not sent to Customer');
            }

            if ($this->allotServiceEngineer($complaint->id)) {
                Log::info('Mail sent to Service Engineer successfully');
            } else {
                Log::error('Mail not sent to Service Engineer');
            }

                    // Build a compact payload (exclude large/needless fields)

        $payload = [
            'name'              => $complaint->name,
            'email'             => $complaint->email,
            'phone'             => $complaint->phone,
            'site_project_name' => $complaint->site_project_name,
            'po_no'             => $complaint->po_no,
            'site_location'     => $complaint->site_location,
            'issue_faced'       => $complaint->issue_faced,
        ];

        $minutes = 60 * 72; // 3 days

        $cookie = cookie(
            name: 'complaint',
            value: json_encode($payload, JSON_UNESCAPED_UNICODE),
            minutes: $minutes,
            path: '/',                                   // make it available site-wide
            domain: config('session.domain'),            // null, or ".volksenergie.in" for cross-subdomain
            secure: app()->environment('production'),    // true on HTTPS in prod
            httpOnly: true,                              // JS can't read it (safer)
            raw: false,
            sameSite: config('session.same_site', 'Lax') // "Lax" works for POST->redirect flows
        );

        // Attach to the final response after redirects:
        Cookie::queue($cookie);
            // dd($cookie);
            
            return redirect()->route('register_complaint.success')
                            ->with('success', 'Customer complaint registered successfully.');
        }
        catch(\Throwable $th){
            Log::error('Error service complaint store: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
       
    }

    public function success(){
        return view('service.customer.success');
    }


    public function getCustomerComplaintsData(Request $request)
    {
        $query = CustomerComplaint::query()->orderBy('created_at', 'desc');

        // Global search (search box)
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
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
            ->addColumn('status', fn($complaint) => empty($complaint->status) ? '-' : str($complaint->status))
            ->addColumn('action', function($complaint) {
               return view('partials.customer-service-actions', compact('complaint'))->render();
            })
            ->addColumn('timer', fn() => '<button class="btn btn-primary btn-sm ">No Timer</button>')
            ->rawColumns(['customer', 'project', 'attachment', 'action', 'timer'])
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

    public function mailToServiceCoordinator($id)
    {
        try {
            $complaint = CustomerComplaint::findOrFail($id);

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

            $data = [
                'clientName' => $clientName,
                'organization' => $organization,
                'siteName' => $siteName,
                'contactNo' => $contactNo,
                'issueFaced' => $issueFaced,
                'attachment' => $complaint->attachment,
            ];

            // Log::info('data: ' . json_encode($data));
            // Log::info('To: ' . $serviceCoordinatorEmail . ' From: ' . $coordinatorEmail);
            $mailer = MailHelper::configureMailer($coordinator->email, $coordinator->app_password, $coordinator->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            // Mail::mailer($mailer)->to($serviceCoordinatorEmail)
            //     ->send(new FollowupAssigned($data));

            Mail::mailer($mailer)->to($serviceCoordinatorEmail)
                ->send(new ComplaintServiceCoordinatorMail($data));  
            
            Log::info('Created Service Mail sent to Service Co-ordinator successfully');
            
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('Error Sending mail: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function mailToCustomer($id)
    {
        try {
            $complaint = CustomerComplaint::findOrFail($id);
            $serviceCoordinator =  User::where('role' ,'=','coordinator')->where('team', '=', 'service')->first();
            // $customerEmail = "abhijeetgaur777@gmail.com"; //using hardcoded Email for now otherwise we will use customerEmail given below
            $customerEmail = $complaint->email; 
            $customerName = $complaint->name;
            // dd($user);
            
            $clientName= $complaint->name;
            $siteName = $complaint->site_project_name;
            $issueFaced = $complaint->issue_faced;

            $data = [
                'clientName' => $clientName,
                'siteName' => $siteName,
                'issueFaced' => $issueFaced,
            ];

            $mailer = MailHelper::configureMailer($serviceCoordinator->email, $serviceCoordinator->app_password, $serviceCoordinator->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';

            Mail::mailer($mailer)->to($customerEmail)
                ->send(new CustomerMail($data));  
            
            Log::info('Sent email from service co-ordinator to customer successfully');
            
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('Error Sending mail: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function mailToServiceEngineer($id)
    {
        try {
            $complaint = CustomerComplaint::findOrFail($id);
            $serviceCoordinator =  User::where('role' ,'=','coordinator')->where('team', '=', 'service')->first();
            //$serviceEngineer = "abhijeetgaur777@gmail.com"; //using hardcoded Email for now otherwise we will use customerEmail given below
            $serviceEngineer = ServiceEngineer::where('complaint_id', $id)->where('status','=','1')->firstOrFail();
        

            $clientName = $complaint->name;
            $organization= $complaint->organization;
            $siteName = $complaint->site_project_name;
            $contactNo = $complaint->phone;
            $issueFaced = $complaint->issue_faced;

            $data = [
                'clientName' => $clientName,
                'organization' => $organization,
                'siteName' => $siteName,
                'contactNo' => $contactNo,
                'issueFaced' => $issueFaced,
                'attachment' => $complaint->attachment,
            ];

            $mailer = MailHelper::configureMailer($serviceCoordinator->email, $serviceCoordinator->app_password, $serviceCoordinator->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';

            Mail::mailer($mailer)->to($serviceEngineer)
                ->send(new ServiceEngineerMail($data));  
            
            Log::info('Sent email to service engineer successfully');
            
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('Error Sending mail to Service Engineer: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

}