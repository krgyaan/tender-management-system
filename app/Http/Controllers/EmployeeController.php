<?php

namespace App\Http\Controllers;

use App\Models\TenderInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role == 'admin') {
            $tender_info = TenderInfo::all();
            $follow_ups = DB::table('follow_ups')->where('assign_initiate', 'Followup Initiated')->get();
            // return $data;

        } else {
            $tender_info = TenderInfo::where('team_member', $user->id)->get();
            $follow_ups = DB::table('follow_ups')->where('assign_initiate', 'Followup Initiated')->where('assigned_to', $user->id)->get();
        }
        $tenderInfo = TenderInfo::where('deleteStatus', '0')->get();
        return view('employeeDashboard', compact('tenderInfo', 'tender_info', 'follow_ups'));
    }
}
