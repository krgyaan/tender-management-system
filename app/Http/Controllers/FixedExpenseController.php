<?php

namespace App\Http\Controllers;

use App\Models\FixedExpense;
use Illuminate\Http\Request;

class FixedExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('accounts.expenses.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('accounts.expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(FixedExpense $fixedExpense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FixedExpense $fixedExpense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FixedExpense $fixedExpense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FixedExpense $fixedExpense)
    {
        //
    }
}
