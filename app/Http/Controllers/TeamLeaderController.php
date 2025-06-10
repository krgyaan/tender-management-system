<?php

namespace App\Http\Controllers;

use App\Models\TenderInfo;
use Illuminate\Http\Request;

class TeamLeaderController extends Controller
{
    public function index()
    {
        $tenderInfo = TenderInfo::where('deleteStatus', '0')->get();
        return view('tlDashboard', compact('tenderInfo'));
    }
}
