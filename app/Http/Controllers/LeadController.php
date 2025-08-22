<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Lead;
use App\Models\State;
use App\Models\LeadType;
use App\Models\LeadContact;
use App\Imports\LeadsImport;
use App\Models\LeadIndustry;
use Illuminate\Http\Request;
use App\Models\LeadCallFollowup;
use App\Models\LeadMailFollowup;
use Yajra\DataTables\DataTables;
use App\Models\LeadVisitFollowup;
use App\Models\LeadLetterFollowup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LeadWhatsappFollowup;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    public function index()
    {
        return view('crm.lead.index');
    }

    public function getLeadsData(Request $request)
    {
        $query = Lead::query();
        $query = $query->orderBy('created_at', 'desc');

        // Global search (search box)
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('industry', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('team', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addColumn('company', fn($lead) => $lead->company_name ?? 'N/A')
            ->addColumn('contact', fn($lead) => "{$lead->name}<br>{$lead->phone}<br>{$lead->email}")
            ->addColumn('industry', fn($lead) => $lead->industry ?? 'N/A')
            ->addColumn('state', fn($lead) => "{$lead->state}<br> ({$lead->country})")
            ->addColumn('type', fn($lead) => $lead->type ?? 'N/A')
            ->addColumn('team', fn($lead) => $lead->team ?? 'N/A')
            ->addColumn('action', fn($lead) => view('partials.lead-action', compact('lead'))->render())
            ->rawColumns(['contact', 'action', 'state'])
            ->make(true);
    }


    public function create()
    {
        $states = State::where('status', true)->orderBy('name')->get();
        $types = LeadType::where('status', true)->orderBy('name')->get();
        $industries = LeadIndustry::where('status', true)->orderBy('name')->get();
        return view('crm.lead.create', compact('states', 'types', 'industries'));
    }

    public function store(Request $request)
    {
        Log::info('LeadController: store() method started.');
        try {
            $validated = $request->validate([
                'company_name' => 'required|string',
                'name' => 'required|string',
                'designation' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|email',
                'address' => 'required|string',
                'country' => 'required|string',
                'state' => 'required|string', // Will be overwritten if needed
                'state_text' => 'required_unless:country,India|string',
                'type' => 'required|string',
                'industry' => 'required|string',
                'team' => 'required|string',
                'points_discussed' => 'nullable|string',
                've_responsibility' => 'nullable|string',
            ]);
    
            // Override 'state' if country is not India
            if ($validated['country'] !== 'India') {
                $validated['state'] = $validated['state_text'];
            }
    
            // Remove state_text since it's not in DB
            unset($validated['state_text']);
    
            Lead::create($validated);
    
            Log::info('LeadController: Lead created successfully.', ['lead' => $validated]);
    
            return redirect()->route('lead.index')->with('success', 'Lead submitted successfully!');
        } catch (\Exception $e) {
            Log::error('LeadController: store() exception: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(Lead $lead)
    {
        $lead->load(['mailFollowups', 'callFollowups', 'visitFollowups', 'letterFollowups', 'whatsappFollowups']);
        return view('crm.lead.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $states = State::where('status', true)->orderBy('name')->get();
        $types = LeadType::where('status', true)->orderBy('name')->get();
        $industries = LeadIndustry::where('status', true)->orderBy('name')->get();

        return view('crm.lead.edit', compact('lead', 'states', 'types', 'industries'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'company_name' => 'required|string',
            'name' => 'required|string',
            'designation' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'country' => 'required|string',
            'state' => 'required|string',
            'type' => 'required|string',
            'industry' => 'required|string',
            'team' => 'required|string',
            'points_discussed' => 'nullable|string',
            've_responsibility' => 'nullable|string',
        ]);

        $lead->update($validated);

        return redirect()->route('lead.index')->with('success', 'Lead updated successfully.');
    }

    public function initiateFollowup(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $user = Auth::user();
        return view('crm.lead.initiate-followup', compact('lead', 'user'));
    }

    public function storeMail(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'mailText' => 'required|string',
            'mailAttachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'frequency' => 'required|in:1,2,3,4,5,6',
            'stop_reason' => 'required_if:frequency,6|nullable|in:1,2,3,4',
            'proof_text' => 'required_if:stop_reason,2|nullable|string',
            'proof_img' => 'required_if:stop_reason,2|nullable|image|mimes:jpg,png|max:1024',
            'stop_rem' => 'required_if:stop_reason,4|nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Store the mail followup
            $mailFollowup = new LeadMailFollowup();
            $mailFollowup->lead_id = $lead->id;
            $mailFollowup->user_id = auth()->id();
            $mailFollowup->mail_body = $validated['mailText'];
            $mailFollowup->frequency = $validated['frequency'];

            if ($request->hasFile('mailAttachment')) {
                $path = $request->file('mailAttachment')->store('lead_mail_attachments');
                $mailFollowup->attachment_path = $path;
            }

            if ($validated['frequency'] == '6') {
                $mailFollowup->stop_reason = $validated['stop_reason'];

                if ($validated['stop_reason'] == '2') {
                    $mailFollowup->proof_text = $validated['proof_text'];
                    if ($request->hasFile('proof_img')) {
                        $proofPath = $request->file('proof_img')->store('lead_stop_proofs');
                        $mailFollowup->proof_image = $proofPath;
                    }
                } elseif ($validated['stop_reason'] == '4') {
                    $mailFollowup->remarks = $validated['stop_rem'];
                }
            }
            $mailFollowup->sequence_number = $lead->mail_followup_count + 1;
            $mailFollowup->save();

            // Update lead counters
            $lead->increment('mail_followup_count');
            $lead->last_mail_sent_at = now();
            $lead->save();

            // Update lead status if frequency is "Stop"
            if ($validated['frequency'] == '6') {
                $lead->status = 'stopped';
                $lead->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Mail follow-up recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record mail follow-up: ' . $e->getMessage());
        }
    }

    public function storeCall(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'callPoints' => 'required|string',
            'callResponsibility' => 'required|string',
            'callNextDate' => 'required|date|after_or_equal:today',
            'mail.name.*' => 'sometimes|required|string',
            'mail.designation.*' => 'sometimes|required|string',
            'mail.phone.*' => 'sometimes|required|numeric',
            'mail.email.*' => 'sometimes|required|email',
        ]);

        try {
            DB::beginTransaction();

            // Store the call followup
            $callFollowup = new LeadCallFollowup();
            $callFollowup->lead_id = $lead->id;
            $callFollowup->user_id = auth()->id();
            $callFollowup->points_discussed = $validated['callPoints'];
            $callFollowup->responsibility = $validated['callResponsibility'];
            $callFollowup->next_followup_date = $validated['callNextDate'];
            $callFollowup->sequence_number = $lead->call_followup_count + 1;
            $callFollowup->save();

            // Store contact details if any
            if (!empty($validated['mail']['name'])) {
                foreach ($validated['mail']['name'] as $index => $name) {
                    $contact = new LeadContact();
                    $contact->lead_id = $lead->id;
                    $contact->name = $name;
                    $contact->designation = $validated['mail']['designation'][$index] ?? null;
                    $contact->phone = $validated['mail']['phone'][$index] ?? null;
                    $contact->email = $validated['mail']['email'][$index] ?? null;
                    $contact->source = 'call_followup';
                    $contact->save();
                }
            }

            // Update lead status
            $lead->increment('call_followup_count');
            $lead->last_call_at = now();
            $lead->save();

            DB::commit();

            return redirect()->back()->with('success', 'Call follow-up recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record call follow-up: ' . $e->getMessage());
        }
    }

    public function storeVisit(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'visitPoints' => 'required|string',
            'visitResponsibility' => 'required|string',
            'visitNextDate' => 'required|date|after_or_equal:today',
            'cold.name.*' => 'sometimes|required|string',
            'cold.designation.*' => 'sometimes|required|string',
            'cold.phone.*' => 'sometimes|required|numeric',
            'cold.email.*' => 'sometimes|required|email',
        ]);

        try {
            DB::beginTransaction();

            // Store the visit followup
            $visitFollowup = new LeadVisitFollowup();
            $visitFollowup->lead_id = $lead->id;
            $visitFollowup->user_id = auth()->id();
            $visitFollowup->points_discussed = $validated['visitPoints'];
            $visitFollowup->responsibility = $validated['visitResponsibility'];
            $visitFollowup->next_followup_date = $validated['visitNextDate'];
            $visitFollowup->sequence_number = $lead->visit_followup_count + 1;
            $visitFollowup->save();

            // Store contact details if any
            if (!empty($validated['cold']['name'])) {
                foreach ($validated['cold']['name'] as $index => $name) {
                    $contact = new LeadContact();
                    $contact->lead_id = $lead->id;
                    $contact->name = $name;
                    $contact->designation = $validated['cold']['designation'][$index] ?? null;
                    $contact->phone = $validated['cold']['phone'][$index] ?? null;
                    $contact->email = $validated['cold']['email'][$index] ?? null;
                    $contact->source = 'visit_followup';
                    $contact->save();
                }
            }

            $lead->increment('visit_followup_count');
            $lead->last_visit_at = now();
            $lead->save();

            DB::commit();

            return redirect()->back()->with('success', 'Visit follow-up recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record visit follow-up: ' . $e->getMessage());
        }
    }

    public function storeLetter(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'letterCourierNo' => 'required|string|max:50',
        ]);

        try {
            $letterFollowup = new LeadLetterFollowup();
            $letterFollowup->lead_id = $lead->id;
            $letterFollowup->user_id = auth()->id();
            $letterFollowup->courier_number = $validated['letterCourierNo'];
            $letterFollowup->sequence_number = $lead->letter_sent_count + 1;
            $letterFollowup->save();

            // Update lead status
            $lead->increment('letter_sent_count');
            $lead->last_letter_sent_at = now();
            $lead->save();

            return redirect()->back()->with('success', 'Letter follow-up recorded successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to record letter follow-up: ' . $e->getMessage());
        }
    }

    public function storeWhatsapp(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'whatsappText' => 'required|string',
        ]);

        try {
            $whatsappFollowup = new LeadWhatsappFollowup();
            $whatsappFollowup->lead_id = $lead->id;
            $whatsappFollowup->user_id = auth()->id();
            $whatsappFollowup->message = $validated['whatsappText'];
            $whatsappFollowup->sequence_number = $lead->whatsapp_followup_count + 1;
            $whatsappFollowup->save();

            // Update lead status
            $lead->increment('whatsapp_followup_count');
            $lead->last_whatsapp_sent_at = now();
            $lead->save();

            return redirect()->back()->with('success', 'WhatsApp follow-up recorded successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to record WhatsApp follow-up: ' . $e->getMessage());
        }
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('lead.index')->with('success', 'Lead deleted successfully.');
    }

    public function sample()
    {
        return response()->download(public_path('assets/lead_sample.xlsx'));
    }
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new LeadsImport, $request->file('import_file'));
            return redirect()->route('lead.index')->with('success', 'Leads imported successfully!');
        } catch (\Exception $e) {
            return redirect()->route('lead.index')->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function enquiryReceived(Request $request, Lead $lead)
    {
        if ($request->isMethod('GET')) {
            $items = Item::where('status', '1')->get();
            $locations = Location::where('status', 1)->get();
            $organisations = Organization::where('status', '1')->get();
            return view('crm.lead.received-enquiry', compact('lead', 'items', 'locations', 'organisations'));
        }

        if ($request->isMethod('POST')) {
        }
    }

    public function allocateToTE(Request $request, Lead $lead)
    {
        if ($request->isMethod('GET')) {
            $technicalExecutives = User::where('role', 'tender-executive')->get();
            return view('crm.lead.allocate-to-te', compact('lead', 'technicalExecutives'));
        }
    }
}
