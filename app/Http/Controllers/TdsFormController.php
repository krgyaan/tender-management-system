<?php

namespace App\Http\Controllers;

use App\Models\TdsForm;
use Illuminate\Http\Request;

class TdsFormController extends Controller
{
    public function index()
    {
        return view('accounts.tds.index');
    }

    public function create()
    {
        return view('accounts.tds.create');
    }

    public function store(Request $request)
    {
        //
    }


    public function show(TdsForm $tdsForm)
    {
        return view('accounts.tds.show');
    }


    public function edit(TdsForm $tdsForm)
    {
        return view('accounts.tds.edit');
    }


    public function update(Request $request, TdsForm $tdsForm)
    {
        //
    }


    public function destroy(TdsForm $tdsForm)
    {
        //
    }
}
