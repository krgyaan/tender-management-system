<?php

namespace App\Http\Controllers;

use App\Models\TenderInfo;
use Illuminate\Http\Request;

class AccountantController extends Controller
{
    public function index()
    {
        $tenderInfo = TenderInfo::where('deleteStatus', '0')->get();
        return view('accountDashboard', compact('tenderInfo'));
    }
}
