<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;

class AccountsPerformanceController extends Controller
{
    public function performance(Request $request)
    {
        $users = User::where('role', 'team-leader')->orWhere('role', 'operation-leader')->orWhere('role', 'account-leader')->get();
        $result = true;
        if ($request->method() == 'GET') {
            $result = false;
            return view('performance.accounts', compact('users', 'result'));
        }

        if ($request->method() == 'GET') {
            $result = false;
            return view('performance.accounts', compact('users', 'result'));
        }
    }
}
