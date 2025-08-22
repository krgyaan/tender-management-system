<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{

    public function index()
    {
        return view('accounts.checklist.index');
    }

    public function create()
    {
        return view('accounts.checklist.create');
    }

    public function store(Request $request)
    {
        // TODO: Add validation and DB save logic
        return redirect()->back()->with('success', 'Expense saved!');
    }
}
