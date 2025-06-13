<?php

namespace App\Http\Controllers;

use App\Models\GstR1;
use Illuminate\Http\Request;

class GstR1Controller extends Controller
{
    public function index()
    {
        return view('accounts.gstr1.index');
    }

    public function create()
    {
        return view('accounts.gstr1.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show(GstR1 $gstR1)
    {
        //
    }

    public function edit(GstR1 $gstR1)
    {
        //
    }

    public function update(Request $request, GstR1 $gstR1)
    {
        //
    }

    public function destroy(GstR1 $gstR1)
    {
        //
    }
}
