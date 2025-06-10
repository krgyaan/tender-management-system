<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::orderBy('address')->get();
        return view('master.locations', compact('locations'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'address' => 'required|string|max:255',
                'acronym' => 'required|string|max:255',
            ]);

            Location::create([
                'address' => $request->address,
                'acronym' => $request->acronym
            ]);

            return redirect()->back()->with('success', 'Location added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        try {
            $request->validate([
                'address' => 'required|string|max:255',
                'acronym' => 'required|string|max:255',
            ]);

            $location->update([
                'address' => $request->address,
                'acronym' => $request->acronym
            ]);

            return redirect()->back()->with('success', 'Location updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        try {
            $location->delete();
            return redirect()->back()->with('success', 'Location deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
