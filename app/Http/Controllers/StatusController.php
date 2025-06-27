<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::all();
        return view('master.status', compact('statuses'));
    }

    public function create()
    {
        return view('statuses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:statuses|max:255',
            'tender_category' => 'required|max:255',
        ]);

        Status::create($request->all());

        return redirect()->route('statuses.index')->with('success', 'Status created successfully.');
    }

    public function show(Status $status)
    {
        return view('statuses.show', compact('status'));
    }

    public function edit(Status $status)
    {
        return view('statuses.edit', compact('status'));
    }

    public function update(Request $request, Status $status)
    {
        $request->validate([
            'name' => 'required|unique:statuses,name,' . $status->id . '|max:255',
            'tender_category' => 'required|max:255',
        ]);

        $status->update($request->all());

        return redirect()->route('statuses.index')->with('success', 'Status updated successfully.');
    }

    public function destroy(Status $status)
    {
        $status->status = $status->status == 1 ? 0 : 1;
        $status->save();

        return redirect()->route('statuses.index')->with('success', 'Status toggled successfully.');
    }
}
