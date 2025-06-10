<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use Illuminate\Http\Request;

class IndustryController extends Controller
{
    public function index()
    {
        $industries = Industry::all();
        return view('master.industries', compact('industries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:industries|max:255',
        ]);

        Industry::create($request->all());
        return redirect()->route('industries.index')
            ->with('success', 'Industry created successfully.');
    }

    public function update(Request $request, Industry $industry)
    {
        $request->validate([
            'name' => "required|max:255|unique:industries,name,{$industry->id}",
        ]);

        $industry->update($request->all());
        return redirect()->route('industries.index')
            ->with('success', 'Industry updated successfully.');
    }

    public function destroy(Industry $industry)
    {
        $industry->delete();
        return redirect()->route('industries.index')
            ->with('success', 'Industry deleted successfully.');
    }
}
