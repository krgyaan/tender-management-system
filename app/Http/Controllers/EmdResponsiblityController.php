<?php

namespace App\Http\Controllers;

use App\Models\EmdResponsiblity;
use App\Models\User;
use Illuminate\Http\Request;

class EmdResponsiblityController extends Controller
{
    public function index(Request $request)
    {
        $responsibilities = EmdResponsiblity::with('responsible')->get();
        $users = User::where('role', 'like', 'account%')->get();
        $instrumentTypes = [
            '1' => 'Demand Draft',
            '2' => 'FDR',
            '3' => 'Cheque',
            '4' => 'BG',
            '5' => 'Bank Transfer',
            '6' => 'Pay on Portal',
        ];
        $editMode = false;
        $responsibility = null;
        if ($request->has('edit')) {
            $editMode = true;
            $responsibility = EmdResponsiblity::findOrFail($request->query('edit'));
        }
        return view('master.emd-responsibility-index', compact('responsibilities', 'users', 'instrumentTypes', 'editMode', 'responsibility'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'instrument_type' => 'required|unique:emd_responsiblities,responsible_for',
            'user_id' => 'required|exists:users,id',
        ]);
        EmdResponsiblity::create([
            'responsible_for' => $request->instrument_type,
            'user_id' => $request->user_id,
        ]);
        return redirect()->route('emd-responsibility.index')->with('success', 'Responsibility assigned successfully.');
    }

    public function update(Request $request, $id)
    {
        $responsibility = EmdResponsiblity::findOrFail($id);
        $request->validate([
            'instrument_type' => 'required|unique:emd_responsiblities,responsible_for,' . $id,
            'user_id' => 'required|exists:users,id',
        ]);
        $responsibility->update([
            'responsible_for' => $request->instrument_type,
            'user_id' => $request->user_id,
        ]);
        return redirect()->route('emd-responsibility.index')->with('success', 'Responsibility updated successfully.');
    }

    public function destroy($id)
    {
        $responsibility = EmdResponsiblity::findOrFail($id);
        $responsibility->delete();
        return redirect()->route('emd-responsibility.index')->with('success', 'Responsibility deleted successfully.');
    }
}
