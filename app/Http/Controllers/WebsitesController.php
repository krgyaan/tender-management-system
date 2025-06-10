<?php

namespace App\Http\Controllers;

use App\Models\Websites;
use Illuminate\Http\Request;

class WebsitesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $websites = Websites::orderBy('name')->get();
        return view('master.websites', compact('websites'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'url' => 'nullable|string',
            ]);

            Websites::create([
                'name' => $request->name,
                'url' => $request->url,
            ]);

            return redirect()->route('websites.index')->with('success', 'Website added successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('websites.index')->with('error', $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Websites $websites)
    {
        try {
            $request->validate([
                'name' => 'required',
                'url' => 'nullable|string',
            ]);

            $websites = Websites::find($request->id);

            $websites->update([
                'name' => $request->name,
                'url' => $request->url,
            ]);

            return redirect()->route('websites.index')->with('success', 'Website updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('websites.index')->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Websites $websites)
    {
        try {
            $websites = Websites::find($request->id);
            $websites->delete();
            return redirect()->route('websites.index')->with('success', 'Website deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('websites.index')->with('error', $th->getMessage());
        }
    }
}
