<?php

namespace App\Http\Controllers;

// use App\Models\GstR1;
use Illuminate\Http\Request;
use App\Models\Accounts\GstR1;
use Illuminate\Support\Facades\Storage;

class GstR1Controller extends Controller
{
    public function index()
    {
        $gstR1s = GstR1::latest()->paginate(10);
        return view('accounts.gstr1.index', compact('gstR1s'));
    }

    public function create()
    {
        return view('accounts.gstr1.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gst_r1_sheet' => 'required|file|mimes:xls,xlsx,pdf|max:5120',
            'tally_data_link' => 'required|url',
            'confirmation' => 'required|accepted',
            'return_file' => 'nullable|file|mimes:xls,xlsx,pdf|max:5120',
        ]);

        // Store GST R1 Sheet
        $gstR1SheetPath = $request->file('gst_r1_sheet')->store('gst-r1-sheets', 'public');

        $returnFilePath = null;
        if ($request->hasFile('return_file')) {
            $returnFilePath = $request->file('return_file')->store('gst-r1-returns', 'public');
        }

        GstR1::create([
            'gst_r1_sheet_path' => $gstR1SheetPath,
            'tally_data_link' => $validated['tally_data_link'],
            'confirmation' => true, // Since it's required and accepted
            'return_file_path' => $returnFilePath,
            'filed_date' => null, // Will be set when actually filed
        ]);

        return redirect()->route('gstr1.index')
            ->with('success', 'GST R1 entry created successfully!');
    }

    public function show($id)
    {
        $gstR1 = GstR1::findOrFail($id);
        return view('accounts.gstr1.show', compact('gstR1'));
    }

    public function edit($id)
    {
        $gstR1 = GstR1::findOrFail($id);
        return view('accounts.gstr1.edit', compact('gstR1'));
    }

    public function update(Request $request, $id)
    {
        $gstR1 = GstR1::findOrFail($id);
         $validated = $request->validate([
            'tally_data_link' => 'required|url',
            'confirmation' => 'sometimes|accepted',
            'return_file' => 'nullable|file|mimes:xls,xlsx,pdf|max:5120',
            'filed_date' => 'nullable|date',
        ]);

        $updateData = [
            'tally_data_link' => $validated['tally_data_link'],
            'confirmation' => $request->has('confirmation'),
        ];

        // Update return file if provided
        if ($request->hasFile('return_file')) {
            if ($gstR1->return_file_path) {
                Storage::disk('public')->delete($gstR1->return_file_path);
            }
            $updateData['return_file_path'] = $request->file('return_file')->store('gst-r1-returns', 'public');
        }

        // Update filed date if provided
        if ($request->filled('filed_date')) {
            $updateData['filed_date'] = $request->input('filed_date');
        }

        $gstR1->update($updateData);

        return redirect()->route('accounts.gstr1.index')
            ->with('success', 'GST R1 entry updated successfully!');
    }

    public function destroy($id)
    {
        // Find the record
        $gstr1 = GstR1::findOrFail($id);

        // Delete the GST R1 sheet file if it exists
        if ($gstr1->gst_r1_sheet_path && Storage::disk('public')->exists($gstr1->gst_r1_sheet_path)) {
            Storage::disk('public')->delete($gstr1->gst_r1_sheet_path);
        }

        // Delete the return file if it exists
        if ($gstr1->return_file_path && Storage::disk('public')->exists($gstr1->return_file_path)) {
            Storage::disk('public')->delete($gstr1->return_file_path);
        }

        // Delete the database record
        $gstr1->delete();

        // Redirect back with success message
        return redirect()->route('accounts.gstr1.index')
            ->with('success', 'GST R1 entry deleted successfully.');
    }
}
