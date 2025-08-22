<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Gst3B\Gst3B;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Gst3BController extends Controller
{
    public function index()
    {
        $gst3bs = Gst3B::latest()->paginate(10);
        return view('accounts.gst3b.index', compact('gst3bs'));
    }

    public function create()
    {
        return view('accounts.gst3b.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tally_data_link' => 'required|url',
            'gst_2a_file' => 'required|file|mimes:xls,xlsx,pdf',
            'gst_tds_file' => 'required|file|mimes:xls,xlsx,pdf',
            'gst_tds_accepted' => 'sometimes|boolean',
            'gst_tds_amount' => 'nullable|numeric',
            'gst_paid' => 'sometimes|boolean',
            'amount' => 'required|numeric',
        ]);

        $gst2aPath = $request->file('gst_2a_file')->store('gst3b');
        $gstTdsPath = $request->file('gst_tds_file')->store('gst3b');

        Gst3B::create([
            'tally_data_link' => $validated['tally_data_link'],
            'gst_2a_file_path' => $gst2aPath,
            'gst_tds_file_path' => $gstTdsPath,
            'gst_tds_accepted' => $request->has('gst_tds_accepted'),
            'gst_tds_amount' => $validated['gst_tds_amount'] ?? 0,
            'gst_paid' => $request->has('gst_paid'),
            'amount' => $validated['amount'],
            'status' => 'pending',
        ]);

        return redirect()->route('accounts.gst3b.index')
            ->with('success', 'GST 3B form created successfully.');
    }

    public function show($id)
    {
        $gst3b = Gst3B::findOrFail($id);
        return view('accounts.gst3b.show', compact('gst3b'));
    }

    public function edit($id)
    {
        $gst3b = Gst3B::findOrFail($id);
        return view('accounts.gst3b.edit', compact('gst3b'));
    }

    public function update(Request $request, $id)
    {
        $gst3b = Gst3B::findOrFail($id);

        $validated = $request->validate([
            'tally_data_link' => 'required|url',
            'gst_2a_file' => 'nullable|file|mimes:xls,xlsx,pdf',
            'gst_tds_accepted' => 'sometimes|boolean',
            'gst_tds_amount' => 'nullable|numeric',
            'gst_paid' => 'sometimes|boolean',
            'amount' => 'required|numeric',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $data = [
            'tally_data_link' => $validated['tally_data_link'],
            'gst_tds_accepted' => $request->has('gst_tds_accepted'),
            'gst_tds_amount' => $validated['gst_tds_amount'] ?? 0,
            'gst_paid' => $request->has('gst_paid'),
            'amount' => $validated['amount'],
            'status' => $validated['status'],
        ];

        if ($request->hasFile('gst_2a_file')) {
            if ($gst3b->gst_2a_file_path) {
                Storage::delete($gst3b->gst_2a_file_path);
            }
            $data['gst_2a_file_path'] = $request->file('gst_2a_file')->store('gst3b');
        }

        $gst3b->update($data);

        return redirect()->route('accounts.gst3b.index')
            ->with('success', 'GST 3B form updated successfully.');
    }

    public function uploadPaymentChallan(Request $request, $id)
    {
        $request->validate([
            'payment_challan' => 'required|file|mimes:pdf,jpg,png',
        ]);

        $gst3b = Gst3B::findOrFail($id);

        if ($gst3b->payment_challan_path) {
            Storage::delete($gst3b->payment_challan_path);
        }

        $path = $request->file('payment_challan')->store('gst3b');

        $gst3b->update([
            'payment_challan_path' => $path,
            'gst_paid' => true,
        ]);

        return redirect()->route('accounts.gst3b.index')
            ->with('success', 'Payment challan uploaded successfully.');
    }

    public function approve($id)
    {
        $gst3b = Gst3B::findOrFail($id);
        $gst3b->update(['status' => 'approved']);

        return redirect()->route('accounts.gst3b.index')
            ->with('success', 'GST 3B form approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $gst3b = Gst3B::findOrFail($id);
        $gst3b->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('accounts.gst3b.index')
            ->with('success', 'GST 3B form rejected successfully.');
    }
}
