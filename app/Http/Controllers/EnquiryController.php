<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Item;
use App\Models\Lead;
use App\Models\Location;
use App\Models\Organization;
use App\Models\PrivateCostingSheet;
use App\Models\SiteVisit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class EnquiryController extends Controller
{
    public function index()
    {
        $users = User::all()->where('role', '!=', 'admin')->where('status', 1);
        return view('crm.enquiry.index', compact('users'));
    }

    public function getEnquiriesData(Request $request, $type)
    {
        $query = Enquiry::with(['organisation', 'item', 'lead', 'siteVisits', 'costingSheets'])
            ->orderBy('created_at', 'desc');

        // Type-based filtering
        switch ($type) {
            case 'ac':
                $query->whereHas('lead', fn($q) => $q->where('team', 'AC'))
                    ->orWhere('team', 'AC');
                break;
            case 'dc':
                $query->whereHas('lead', fn($q) => $q->where('team', 'DC'))
                    ->orWhere('team', 'DC');
                break;
            case 'ib':
                $query->whereHas('lead', fn($q) => $q->where('team', 'IB'))
                    ->orWhere('team', 'IB');
                break;
        }

        return DataTables::of($query)
            ->addColumn('enquiry_no', fn($enquiry) => 'ENQ-' . str_pad($enquiry->id, 5, '0', STR_PAD_LEFT))
            ->addColumn('enquiry_name', fn($enquiry) => $enquiry->enq_name)
            ->addColumn('bd_lead', fn($enquiry) => $enquiry->lead ? $enquiry->lead->name : $enquiry->creator->name)
            ->addColumn('company_name', fn($enquiry) => $enquiry->lead ? $enquiry->lead->company_name : 'N/A')
            ->addColumn('organization_name', fn($enquiry) => $enquiry->organisation ? $enquiry->organisation->name : 'N/A')
            ->addColumn('item_name', fn($enquiry) => $enquiry->item ? $enquiry->item->name : 'N/A')
            ->addColumn('approx_value', fn($enquiry) => format_inr($enquiry->approx_value))
            ->addColumn(
                'site_visit',
                fn($enquiry) =>
                $enquiry->site_visit_required ?
                    ($enquiry->siteVisit ? '<span class="badge bg-success">Done</span>' : '<span class="badge bg-warning">Pending</span>') :
                    '<span class="badge bg-secondary">Not Required</span>'
            )
            ->addColumn(
                'status',
                fn($enquiry) =>
                $enquiry->costingSheet ?
                    ($enquiry->costingSheet->submitted_at ? '<span class="badge bg-primary">Submitted</span>' : '<span class="badge bg-info">Created</span>') : ($enquiry->siteVisit ? '<span class="badge bg-success">Visit Done</span>' : ($enquiry->site_visit_required ? '<span class="badge bg-warning">Visit Needed</span>' : '<span class="badge bg-secondary">New</span>'))
            )
            ->addColumn('timer', fn($enquiry) => view('partials.enquiry-timer', compact('enquiry'))->render())
            ->addColumn('action', fn($enquiry) => view('partials.enquiry-action', compact('enquiry'))->render())
            ->rawColumns(['site_visit', 'status', 'timer', 'action'])
            ->make(true);
    }

    public function create(Lead $lead = null)
    {
        return view('crm.enquiry.create', [
            'lead' => $lead,
            'organisations' => Organization::orderBy('name')->get(),
            'items' => Item::orderBy('name')->get(),
            'locations' => Location::orderBy('address')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organisation' => 'required|exists:organizations,id',
            'item' => 'required|exists:items,id',
            'location' => 'required|exists:locations,acronym',
            'approx_value' => 'required|numeric|min:0',
            'site_visit_required' => 'required|in:Y,N',
            'enquiry_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        try {
            $enquiry = new Enquiry();
            $enquiry->lead_id = $request->lead_id;
            $enquiry->team = Lead::find($request->lead_id)?->team ?? $request->team;
            $enquiry->enq_name = $request->enq_name;
            $enquiry->organisation_id = $validated['organisation'];
            $enquiry->item_id = $validated['item'];
            $enquiry->location_code = $validated['location'];
            $enquiry->approx_value = $validated['approx_value'];
            $enquiry->site_visit_required = $validated['site_visit_required'] === 'Y';
            $enquiry->created_by = auth()->id();

            if ($request->hasFile('enquiry_file')) {
                $file = $request->file('enquiry_file');
                $filename = time() . '_enquiry_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/enquiries'), $filename);
                $enquiry->document_path = $filename;
            }

            $enquiry->save();

            if ($request->lead_id) {
                $lead = Lead::find($request->lead_id);
                $lead->enquiry_received_at = now();
                $lead->save();
            }

            return redirect()->route('enquiries.index')
                ->with('success', 'Enquiry created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating enquiry: ' . $e->getMessage());
        }
    }

    public function edit(Enquiry $enquiry)
    {
        return view('enquiries.edit', [
            'enquiry' => $enquiry,
            'organisations' => Organization::orderBy('name')->get(),
            'items' => Item::orderBy('name')->get(),
            'locations' => Location::orderBy('address')->get()
        ]);
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $validated = $request->validate([
            'organisation' => 'required|exists:organisations,id',
            'item' => 'required|exists:items,id',
            'location' => 'required|exists:locations,acronym',
            'approx_value' => 'required|numeric|min:0',
            'site_visit_required' => 'required|in:Y,N',
            'enquiry_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        try {
            $enquiry->organisation_id = $validated['organisation'];
            $enquiry->item_id = $validated['item'];
            $enquiry->location_code = $validated['location'];
            $enquiry->approx_value = $validated['approx_value'];
            $enquiry->site_visit_required = $validated['site_visit_required'] === 'Y';
            $enquiry->updated_by = auth()->id();

            if ($request->hasFile('enquiry_file')) {
                // Delete old file if exists
                if ($enquiry->document_path) {
                    unlink(public_path('uploads/enquiries/' . $enquiry->document_path));
                }

                $file = $request->file('enquiry_file');
                $filename = time() . '_enquiry_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/enquiries'), $filename);
                $enquiry->document_path = $filename;
            }

            $enquiry->save();

            return redirect()->route('enquiries.show', $enquiry)
                ->with('success', 'Enquiry updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating enquiry: ' . $e->getMessage());
        }
    }


    public function show(Enquiry $enquiry)
    {
        return view('crm.enquiry.show', compact('enquiry'));
    }

    public function destroy(Enquiry $enquiry)
    {
        //
    }

    // Store site visit allocation
    public function allocateSiteVisit(Request $request)
    {
        Log::info('AllocateSiteVisit: Validating request', ['request' => $request->all()]);

        $validated = $request->validate([
            'enquiry_id' => 'required|exists:enquiries,id',
            'visit_date_time' => 'required|date',
            'assignee_id' => 'required|exists:users,id',
            'notes' => 'nullable|string'
        ]);

        Log::info('AllocateSiteVisit: Request validated', ['validated' => $validated]);

        try {
            $siteVisit = SiteVisit::create([
                'enquiry_id' => $validated['enquiry_id'],
                'assigned_to' => $validated['assignee_id'],
                'scheduled_at' => $validated['visit_date_time'],
                'additional_notes' => $validated['notes'],
                'status' => 'scheduled'
            ]);

            Log::info('AllocateSiteVisit: Site visit created', ['site_visit' => $siteVisit]);

            return back()->with('success', 'Site visit allocated successfully!');
        } catch (\Exception $e) {
            Log::error('AllocateSiteVisit: Error allocating site visit', ['error' => $e->getMessage()]);

            return back()->with('error', 'Error allocating site visit: ' . $e->getMessage());
        }
    }

    // Record site visit details
    public function recordSiteVisit(Request $request)
    {
        Log::info('RecordSiteVisit: Validating request', ['request' => $request->all()]);

        $validated = $request->validate([
            'site_visit_id' => 'required|exists:site_visits,id',
            'information' => 'required|string',
            'contacts' => 'required|array',
            'contacts.name.*' => 'required|string',
            'contacts.designation.*' => 'nullable|string',
            'contacts.phone.*' => 'nullable|string',
            'contacts.email.*' => 'nullable|email',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:10240'
        ]);

        Log::info('RecordSiteVisit: Request validated', ['validated' => $validated]);

        try {
            DB::beginTransaction();

            // Store documents
            $documentPaths = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $filename = time() . '_enquiry_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/site_docs'), $filename);
                    $documentPaths[] = $filename;
                }
            }

            Log::info('RecordSiteVisit: Files stored', ['documents' => $documentPaths]);

            // Update site visit
            $siteVisit = SiteVisit::find($validated['site_visit_id']);
            $siteVisit->update([
                'information' => $validated['information'],
                'conducted_at' => now(),
                'documents' => $documentPaths,
                'status' => 'completed'
            ]);

            Log::info('RecordSiteVisit: Site visit updated', ['site_visit' => $siteVisit]);

            // Store contacts
            foreach ($validated['contacts'] as $contact) {
                // Check for existing contact with same name and email
                $existingContact = $siteVisit->contacts()
                    ->where('name', $contact['name'])
                    ->where('email', $contact['email'] ?? null)
                    ->first();

                if (!$existingContact) {
                    $siteVisit->contacts()->create([
                        'name' => $contact['name'],
                        'designation' => $contact['designation'] ?? null,
                        'phone' => $contact['phone'] ?? null,
                        'email' => $contact['email'] ?? null
                    ]);
                }
            }

            Log::info('RecordSiteVisit: Contacts stored', ['contacts' => $validated['contacts']]);
            DB::commit();
            return back()->with('success', 'Site visit recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RecordSiteVisit: Error recording site visit', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error recording site visit: ' . $e->getMessage());
        }
    }
}
