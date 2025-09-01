<?php

namespace App\Http\Controllers\CustomerService;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Helpers\MailHelper;
use App\Services\TimerService;
use App\Models\CustomerComplaint;
use App\Models\ServiceEngineer;
use App\Models\ServiceConferenceCall;
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

class ConferenceCallController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function index()
    {
        return view('service.customer.conference_call.index');
    }


    public function create($complaintId)
    {
        $complaint = CustomerComplaint::findOrFail($complaintId);
        return view('service.customer.conference_call.create', compact('complaint'));
    }
    
    public function store(Request $request)
    {

        $complaintId = $request['complaint_id'];
        // dd($complaintId);
        // Validate fields
        $validated = $request->validate([
            'issue_description'   => 'required|string',
            'materials_required'  => 'nullable|string',
            'actions_planned'     => 'nullable|string',
            'attachments.*'       => 'nullable|file|max:10240', // Max 10 MB per file
            'voice_recording'     => 'nullable|file|mimes:mp3,wav,m4a,aac,ogg|max: 10240', // 10 MB max
        ]);

        // Find the complaint
        $complaint = CustomerComplaint::findOrFail($complaintId);

        // Store voice recording if uploaded
        $voicePath = null;
        if ($request->hasFile('voice_recording')) {
            $voicePath = $request->file('voice_recording')->store('conference_calls/voice_recordings', 'public');
        }

        // Store attachments if uploaded
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = $file->store('conference_calls/attachments', 'public');
            }
        }

        // Save the report (adjust model/table/fields as needed)
        $report = new ServiceConferenceCall();
        $report->complaint_id         = $complaint->id;
        $report->issue_description    = $validated['issue_description'];
        $report->materials_required   = $validated['materials_required'] ?? null;
        $report->actions_planned      = $validated['actions_planned'] ?? null;
        $report->voice_recording_path = $voicePath;
        $report->attachments          = json_encode($attachmentPaths); // Store as JSON array
        $report->created_by           = auth()->id(); // Optional: link to user
        $report->save();

        
        if ($this->conferenceCallMail($report->id, $complaint->id)) {
            Log::info('Mail sent to Service Engineer successfully');
        } else {
            Log::error('Mail not sent to Service Engineer');
            return redirect()->route('customer_service.conference_call.index')->with('error', 'Conferennce call details registered successfully but mail not sent to Service Engineer');
        }

        $complaint->update(['status' => '2']);


        return redirect()
            ->route('customer_service.conference_call.index')
            ->with('success', 'Conference Call details submitted successfully!');
    }

    public function show($id)
    {
        
        // Find the conference call record
        // dd($id);
        $conferenceCall = ServiceConferenceCall::where('complaint_id', '=', $id)->with('complaint')->first();
        // dd($conferenceCall);
        // Pass it to the view
        return view('service.customer.conference_call.show', compact('conferenceCall'));
    }


    public function getCustomerComplaintsData(Request $request)
    {
        $query = CustomerComplaint::query()->with('callDetails')->whereIn('status', ['1', '2', '3'])->orderBy('created_at', 'desc');

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
            ->addColumn('status', function ($complaint) {
                return view('partials.service_status', compact('complaint'))->render();
            })
            ->addColumn('action', function($complaint) {
               return view('partials.customer_service_call', compact('complaint'))->render();
            })
            ->addColumn('timer', fn() => '<button class="btn btn-primary btn-sm ">No Timer</button>')
            ->rawColumns(['customer', 'project', 'attachment', 'action', 'timer'])
            ->make(true);
    }


    public function destroy($id)
    {
        $lead->delete();
        return redirect()->route('lead.index')->with('success', 'Lead deleted successfully.');
    }


    // MAILS Begin from here
    
    public function conferenceCallMail($id, $complaintId)
    {
        try {
            $report = ServiceConferenceCall::findOrFail($id);
            $complaint = CustomerComplaint::findOrFail($complaintId);
            $serviceCoordinator =  User::where('role' ,'=','coordinator')->where('team', '=', 'service')->first();
            // dd($serviceCoordinator->email);
            //$serviceEngineer = "abhijeetgaur777@gmail.com"; //using hardcoded Email for now otherwise we will use customerEmail given below
            $serviceEngineer = ServiceEngineer::where('complaint_id', $complaintId)->where('status','=','1')->firstOrFail();
            // dd($serviceEngineer->email);
            // dd($serviceEngineer);
        

            $issue_description = $report->issue_description;
            $actions_planned= $report->actions_planned;
            $materials = $report->materials;
            $phone = $serviceCoordinator->mobile;
            $base_url =  "https://tms.volksenergie.in";
            $service_visit_form_url = $base_url . "/customer-service/service-visit/public/" . $complaintId;
            // dd($service_visit_form_url);

            $data = [
                'issue_description'    => $issue_description,
                'actions_planned'      => $actions_planned,
                'materials'            => $materials,
                'phone'                => $phone,
                'ticket_no'            => $complaint->ticket_no,
                'service_visit_form_url' => $service_visit_form_url,
                'attachments'          => json_decode($report->attachments, true) ?? [],
                'voice_recording'      => $report->voice_recording_path,
            ];

            $mailer = MailHelper::configureMailer($serviceCoordinator->email, $serviceCoordinator->app_password, $serviceCoordinator->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';

            Mail::mailer($mailer)->to($serviceEngineer)
                ->send(new ConferenceCallMail($data));  
            
            Log::info('Sent email to service engineer successfully');
            
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error('Error Sending mail to Service Engineer: ' . $th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

}