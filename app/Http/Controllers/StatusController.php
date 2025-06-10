<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Display a listing of the statuses.
     */
    public function index()
    {
        $statuses = Status::all();
        return view('master.status', compact('statuses'));
    }

    /**
     * Show the form for creating a new status.
     */
    public function create()
    {
        return view('statuses.create');
    }

    /**
     * Store a newly created status in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:statuses|max:255',
        ]);

        Status::create($request->all());

        return redirect()->route('statuses.index')->with('success', 'Status created successfully.');
    }

    /**
     * Display the specified status.
     */
    public function show(Status $status)
    {
        return view('statuses.show', compact('status'));
    }

    /**
     * Show the form for editing the specified status.
     */
    public function edit(Status $status)
    {
        return view('statuses.edit', compact('status'));
    }

    /**
     * Update the specified status in storage.
     */
    public function update(Request $request, Status $status)
    {
        $request->validate([
            'name' => 'required|unique:statuses,name,' . $status->id . '|max:255',
        ]);

        $status->update($request->all());

        return redirect()->route('statuses.index')->with('success', 'Status updated successfully.');
    }

    /**
     * Remove the specified status from storage.
     */
    public function destroy(Status $status)
    {
        $status->delete();

        return redirect()->route('statuses.index')->with('success', 'Status deleted successfully.');
    }
}
