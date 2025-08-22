<?php

namespace App\Http\Controllers;

use App\Models\OrgIndustry;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        try {
            $organizations = Organization::all();
            $industries = OrgIndustry::where('status', '1')->get();

            if ($organizations->isEmpty()) {
                return redirect()->route('organizations.index')
                    ->with('error', 'No organizations found.');
            }

            return view('master.organization', compact('organizations', 'industries'));
        } catch (\Exception $e) {
            return redirect()->route('organizations.index')
                ->with('error', 'Failed to retrieve organizations: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:organizations|max:255',
                'full_form' => 'required',
                'industry' => 'required',
            ]);

            Organization::create($request->all());

            return redirect()->route('organizations.index')
                ->with('success', 'Organization created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('organizations.index')
                ->with('error', 'Failed to create organization: ' . $e->getMessage());
        }
    }

    public function show(Organization $organization)
    {
        return view('organizations.show', compact('organization'));
    }

    public function edit(Organization $organization)
    {
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        if (!$organization) {
            return redirect()->route('organizations.index')
                ->with('error', 'Organization not found.');
        }

        $request->validate([
            'name' => "required|max:255|unique:organizations,name,{$organization->id}",
            'full_form' => 'required',
            'industry' => 'required',
        ]);

        try {
            $organization->update($request->all());
        } catch (\Exception $e) {
            return redirect()->route('organizations.index')
                ->with('error', 'Failed to update organization: ' . $e->getMessage());
        }

        return redirect()->route('organizations.index')
            ->with('success', 'Organization updated successfully.');
    }

    public function delete($id)
    {
        try {
            $organization = Organization::find($id);

            if (!$organization) {
                return redirect()->route('organizations.index')
                    ->with('error', 'Organization not found.');
            }

            $organization->status = '0';
            $organization->save();

            return redirect()->route('organizations.index')
                ->with('success', 'Organization deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('organizations.index')
                ->with('error', 'An error occurred while deleting the organization.');
        }
    }
    
    public function addIndustry(Request $request)
    {
        if ($request->isMethod('get')) {
            $industries = OrgIndustry::orderBy('status')->get();
            return view('master.org-industries', compact('industries'));
        }

        if ($request->isMethod('post')) {
            try {
                $request->validate([
                    'name' => 'required|unique:org_industries,name',
                    'description' => 'nullable'
                ]);

                OrgIndustry::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'status' => '1'
                ]);

                return redirect()->back()->with('success', 'Industry added successfully.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to add industry: ' . $e->getMessage());
            }
        }
    }

    public function editIndustry($id)
    {
        try {
            $editIndustry = OrgIndustry::findOrFail($id);
            $industries = OrgIndustry::orderBy('status')->get();

            return view('master.org-industries', compact('editIndustry', 'industries'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Industry not found.');
        }
    }

    public function updateIndustry(Request $request, $id)
    {
        try {
            $industry = OrgIndustry::findOrFail($id);

            $request->validate([
                'name' => 'required|unique:org_industries,name,' . $id,
                'description' => 'nullable'
            ]);

            $industry->update([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return redirect()->route('org-industries.add')->with('success', 'Industry updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update industry: ' . $e->getMessage());
        }
    }

    public function deleteIndustry($id)
    {
        try {
            $industry = OrgIndustry::findOrFail($id);

            if (!$industry) {
                return redirect()->back()->with('error', 'Industry not found.');
            }

            $industry->status = '0';
            $industry->save();

            return redirect()->back()->with('success', 'Industry inactivated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting the industry.');
        }
    }
}
