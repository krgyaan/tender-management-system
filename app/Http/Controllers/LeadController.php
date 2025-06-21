<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\State;
use App\Models\LeadIndustry;
use App\Models\LeadType;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Display a listing of the leads.
     */
    public function index()
    {
        $leads = Lead::latest()->paginate(10);
        return view('crm.lead.index', compact('leads'));
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create()
    {
        $states = State::where('status', true)->orderBy('name')->get();
        $types = LeadType::where('status', true)->orderBy('name')->get();
        $industries = LeadIndustry::where('status', true)->orderBy('name')->get();
        return view('crm.lead.create', compact('states', 'types', 'industries'));
    }

    /**
     * Store a newly created lead in storage.
     */


    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string',
            'name' => 'required|string',
            'designation' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'state' => 'required|string',
            'type' => 'required|string',
            'industry' => 'required|string',
            'team' => 'required|string',
            'points_discussed' => 'nullable|string',
            've_responsibility' => 'nullable|string',
        ]);

        Lead::create($validated);

        return redirect()->route('lead.index')->with('success', 'Lead submitted successfully!');
    }
    /**
     * Display the specified lead.
     */
    public function show(Lead $lead)
    {
        return view('crm.lead.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified lead.
     */
    public function edit(Lead $lead)
    {
        $states = State::where('status', true)->orderBy('name')->get();
        $types = LeadType::where('status', true)->orderBy('name')->get();
        $industries = LeadIndustry::where('status', true)->orderBy('name')->get();

        return view('crm.lead.edit', compact('lead', 'states', 'types', 'industries'));
    }

    /**
     * Update the specified lead in storage.
     */
    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'company_name' => 'required|string',
            'name' => 'required|string',
            'designation' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'state' => 'required|string',
            'type' => 'required|string',
            'industry' => 'required|string',
            'team' => 'required|string',
            'points_discussed' => 'nullable|string',
            've_responsibility' => 'nullable|string',
        ]);


        $lead->update($validated);

        return redirect()->route('lead.show', $lead->id)->with('success', 'Lead updated successfully.');
    }

    /**
     * Remove the specified lead from storage.
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('lead.index')->with('success', 'Lead deleted successfully.');
    }
}
