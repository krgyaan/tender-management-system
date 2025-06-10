<?php

namespace App\Http\Controllers;

use App\Models\FollowupFor;
use Illuminate\Http\Request;

class FollowupForController extends Controller
{
    public function index()
    {
        $fors = FollowupFor::all();
        return view('master.followupfors', compact('fors'));
    }

    public function create() {}

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            FollowupFor::create([
                'name' => $request->name,
            ]);

            return redirect()->back()->with('success', 'Follow purpose added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(FollowupFor $followupFor) {}
    public function edit(FollowupFor $followupFor) {}

    public function update(Request $request, FollowupFor $followupFor)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $followupFor->update([
                'name' => $request->name
            ]);

            return redirect()->back()->with('success', 'Follow purpose updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroy(FollowupFor $followupFor)
    {
        try {
            $followupFor->delete();
            return redirect()->back()->with('success', 'Follow purpose deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
