<?php

namespace App\Http\Controllers;

use App\Models\Gst3B;
use Illuminate\Http\Request;

class Gst3BController extends Controller
{
    public function index()
    {
        return view('accounts.gst3b.index');
    }

    public function create()
    {
        return view('accounts.gst3b.create');
    }

    public function show()
    {
        return view('accounts.gst3b.show');
    }

    public function store(Request $request)
    {
        //
    }

    public function edit()
    {
        return view('accounts.gst3b.edit');
    }

    public function update(Request $request, Gst3B $gst3B)
    {
        //
    }

    public function destroy(Gst3B $gst3B)
    {
        //
    }
}
