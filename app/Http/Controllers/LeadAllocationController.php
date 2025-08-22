<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class LeadAllocationController extends Controller
{
    public function index()
    {
        $allocations = Lead::all();
        $technicalExecutives = User::where('role', 'tender-executive')->get();
        return view('crm.lead-allocation.index', compact('allocations'));
    }

    public function create(Lead $lead)
    {
        $technicalExecutives = User::where('role', 'tender-executive')->get();
        return view('crm.lead-allocation.create', compact('technicalExecutives', 'lead'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'te_id' => 'required|exists:users,id',
            'allocation_notes' => 'required|string',
        ]);

        $lead = Lead::find($request->lead_id);
        if (!$lead) {
            return redirect()->back()->withErrors(['error' => 'Lead not found.']);
        }

        try {
            $lead->update([
                'allocated_te' => $request->te_id,
            ]);

            return redirect()->route('lead.index')->with('success', 'Lead allocated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to allocate lead.']);
        }
    }
}
