<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $states = Location::$indianStatesAndUTs;
        $regions = Location::$regions;
        $locations = Location::orderBy('address')->get();
        return view('master.locations', compact('locations', 'states', 'regions'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'address' => 'required|string|max:255',
                'acronym' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'region' => 'required|string|max:255',
            ]);

            Location::create([
                'address' => $request->address,
                'acronym' => $request->acronym,
                'state' => $request->state,
                'region' => $request->region
            ]);

            return redirect()->back()->with('success', 'Location added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Location $location)
    {
        try {
            $request->validate([
                'address' => 'required|string|max:255',
                'acronym' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'region' => 'required|string|max:255',
            ]);

            $location->update([
                'address' => $request->address,
                'acronym' => $request->acronym,
                'state' => $request->state,
                'region' => $request->region
            ]);

            return redirect()->back()->with('success', 'Location updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Location $location)
    {
        try {
            $newStatus = $location->status == '1' ? '0' : '1';
            $location->update(['status' => $newStatus]);
            return redirect()->back()->with('success', 'Location status toggled successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
