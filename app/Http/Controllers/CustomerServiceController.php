<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Helpers\MailHelper;
use App\Services\TimerService;
use App\Models\CustomerComplaint;
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


    public function create()
    {
        return view('service.customer.create');
    }

    public function show($id)
    {
        $complaint = CustomerComplaint::findOrFail($id);

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

            if ($this->mailToServiceCoordinator($complaint->id)) {
                Log::info('Mail sent to Service Co-ordinator successfully');
            } else {
                Log::error('Mail not sent to Service Co-ordinator');
                return redirect()->route('customer_service.index')->with('error', 'Service Complaint created successfully but mail not sent to Service Co-ordinator');
            }

            if ($this->mailToCustomer($complaint->id)) {
                Log::info('Mail sent to Customer successfully');
            } else {
                Log::error('Mail not sent to Customer');
                return redirect()->route('customer_service.index')->with('error', 'Service Complaint created successfully but mail not sent to Customer');
            }

            if ($this->allotServiceEngineer($complaint->id)) {
                Log::info('Mail sent to Service Engineer successfully');
            } else {
                Log::error('Mail not sent to Service Engineer');
                return redirect()->route('customer_service.index')->with('error', 'Service Complaint created successfully but mail not sent to Service Engineer');
            }


            return redirect()->route('customer_service.index')
                            ->with('success', 'Customer complaint registered successfully.');
        }
        catch(\Throwable $th){
            Log::error('Error service complaint store: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
       
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
                    ->orWhere('issue_faced', 'like', "%{$search}%");
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
            ->addColumn('status', fn() => '-')
            ->addColumn('action', function($complaint) {
                return '<a href="'.route('customer_service.show', $complaint->id).'" class="btn btn-sm btn-secondary">View</a>';
            })
            ->addColumn('timer', fn() => '<button class="btn btn-primary btn-sm ">No Timer</button>')
            ->rawColumns(['customer', 'project', 'attachment', 'action', 'timer'])
            ->make(true);
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

            $coordinator = auth()->user();            
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
            $customerEmail = "abhijeetgaur777@gmail.com"; //using hardcoded Email for now otherwise we will use customerEmail given below
            // $customerEmail = $complaint->email; 
            $customerName = $complaint->name;
            // dd($user);
            
            $complaint->name;
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


    public function allotServiceEngineer($id)
    {
        try {
            $complaint = CustomerComplaint::findOrFail($id);
            $serviceCoordinator =  User::where('role' ,'=','coordinator')->where('team', '=', 'service')->first();
            //$serviceEngineer = "abhijeetgaur777@gmail.com"; //using hardcoded Email for now otherwise we will use customerEmail given below
            $serviceEngineer = optional(User::where('role', 'service-engineer')
                                ->where('team', 'service')
                                ->first())->email;
            
        
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
