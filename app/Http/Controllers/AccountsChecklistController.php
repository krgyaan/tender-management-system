<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Gst3B\Checklist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AccountsChecklistController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;
        $checklists = (in_array(Auth::user()->role, ['admin', 'coordinator'])) ?
            Checklist::with(['responsibleUser', 'accountableUser'])->get() :
            Checklist::with(['responsibleUser', 'accountableUser'])
            ->where('responsibility', $userId)
            ->orWhere('accountability', $userId)
            ->get();

        return view('accounts.checklist.index', compact('checklists', 'userId'));
    }

    public function create()
    {
        $teamMember
        return view('accounts.checklist.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
        ]);

        Checklist::create($request->all());

        return redirect()->route('checklists.index')->with('success', 'Checklist created successfully.');
    }

    public function show(Checklist $checklist)
    {
        // Eager load all necessary relationships to prevent N+1 queries
        $checklist->load([
            'responsibleUser',
            'accountableUser',
            'responsibilityRemarkBy',
            'accountabilityRemarkBy'
        ]);

        // Ensure dates are properly cast
        if (is_string($checklist->responsibility_remark_date)) {
            $checklist->responsibility_remark_date = Carbon::parse($checklist->responsibility_remark_date);
        }

        if (is_string($checklist->accountability_remark_date)) {
            $checklist->accountability_remark_date = Carbon::parse($checklist->accountability_remark_date);
        }

        return view('accounts.checklist.show', compact('checklist'));
    }

    public function edit(Checklist $checklist)
    {
        $users = User::all();
        return view('accounts.checklist.edit', compact('checklist', 'users'));
    }

    public function update(Request $request, Checklist $checklist)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
        ]);

        $checklist->update($request->all());

        return redirect()->route('checklists.show', $checklist->id)->with('success', 'Checklist updated successfully.');
    }

    public function destroy(Checklist $checklist)
    {
        $checklist->delete();

        return redirect()->route('checklists.index')->with('success', 'Checklist deleted successfully.');
    }

    public function completeResponsibility(Checklist $checklist)
    {
        $checklist->responsibility_completed = true;
        $checklist->responsibility_completed_by = Auth::id();
        $checklist->responsibility_completed_at = now();
        $checklist->save();

        return redirect()->route('checklists.show', $checklist->id)->with('success', 'Responsibility marked complete.');
    }

    public function completeAccountability(Checklist $checklist)
    {
        $checklist->accountability_completed = true;
        $checklist->accountability_completed_by = Auth::id();
        $checklist->accountability_completed_at = now();
        $checklist->save();

        return redirect()->route('checklists.show', $checklist->id)->with('success', 'Accountability marked complete.');
    }

    public function storeResponsibilityRemark(Request $request, $id)
    {
        $request->validate([
            'responsibility_remark' => 'required|string|max:1000',
        ]);

        $checklist = Checklist::findOrFail($id);

        $checklist->update([
            'responsibility_remark' => $request->input('responsibility_remark'),
            'responsibility_remark_by' => Auth::id(),
            'responsibility_remark_date' => now(), // This will be automatically cast to a date
        ]);

        return redirect()->route('checklists.show', $id)
            ->with('success', 'Responsibility remark saved successfully.');
    }
    public function storeAccountabilityRemark(Request $request, $id)
    {
        $request->validate([
            'accountability_remark' => 'required|string|max:1000',
        ]);

        $checklist = Checklist::findOrFail($id);

        $checklist->update([
            'accountability_remark' => $request->input('accountability_remark'),
            'accountability_remark_by' => Auth::id(),
            'accountability_remark_date' => now(),
        ]);

        return redirect()->route('checklists.show', $id)
            ->with('success', 'Accountability remark saved successfully.');
    }

    public function uploadResultFile(Request $request, $id)
    {
        $request->validate([
            'result_file' => 'required|file|max:10240',
        ]);

        $checklist = Checklist::findOrFail($id);

        if ($request->hasFile('result_file')) {
            $path = $request->file('result_file')->store('checklist_result_files');
            $checklist->final_result_path = $path;
            $checklist->save();

            return redirect()->route('checklists.show', $id)->with('success', 'Result file uploaded successfully.');
        }

        return back()->withErrors(['result_file' => 'Result file upload failed']);
    }

    public function download(Checklist $checklist)
    {
        if (!$checklist->file_path || !Storage::exists($checklist->file_path)) {
            return redirect()->back()->withErrors('File not found.');
        }

        return Storage::download($checklist->file_path);
    }
}
