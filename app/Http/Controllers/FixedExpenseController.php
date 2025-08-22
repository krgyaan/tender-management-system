<?php

namespace App\Http\Controllers;

use App\Models\Gst3B\FixedExpense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class FixedExpenseController extends Controller
{

    public function status(FixedExpense $fixedExpense)
    {
        return view('accounts.expenses.status', compact('fixedExpense'));
    }

    public function updateStatus(Request $request, FixedExpense $fixedExpense)
    {
        $validated = $request->validate([
            'amount_type' => 'required|in:Fixed,Variable',
            'amount' => 'required_if:amount_type,Fixed|nullable|numeric|min:0',
            'status' => 'required|string|max:50',
            'utr_message' => 'nullable|string',
            'payment_datetime' => 'nullable|date',
        ]);

        $fixedExpense->update($validated);

        return redirect()->route('fixed-expenses.show', $fixedExpense)->with('success', 'Fixed Expense status updated successfully.');
    }

    public function index()
    {
        $expenses = FixedExpense::all()->sortByDesc('due_date')->where('status', '!=', 'Paid');
        $completedExpenses = FixedExpense::all()->sortByDesc('due_date')->where('status', 'Paid');
        return view('accounts.expenses.index', compact('expenses', 'completedExpenses'));
    }

    public function create()
    {
        return view('accounts.expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'party_name' => 'required|string|max:255',
            'amount_type' => 'required|in:Fixed,Variable',
            'amount' => 'required_if:amount_type,Fixed|nullable|numeric|min:0',
            'payment_method' => 'required|in:Auto Debit,Bank Transfer',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'ifsc' => 'nullable|string|max:255',
            'due_date' => 'required|date|after_or_equal:today',
            'frequency' => 'required|string|max:50',
        ]);

        FixedExpense::create($validated);

        return redirect()->route('fixed-expenses.index')->with('success', 'Fixed Expense created successfully.');
    }

    public function show(FixedExpense $fixedExpense)
    {
        return view('accounts.expenses.show', compact('fixedExpense'));
    }

    public function edit(FixedExpense $fixedExpense)
    {
        return view('accounts.expenses.edit', compact('fixedExpense'));
    }

    public function update(Request $request, FixedExpense $fixedExpense)
    {
        $validated = $request->validate([
            'party_name' => 'required|string|max:255',
            'amount_type' => 'required|in:Fixed,Variable',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Auto Debit,Bank Transfer',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'ifsc' => 'nullable|string|max:255',
            'due_date' => 'required|date',
            'frequency' => 'required|string|max:50',
            'status' => 'nullable|string|max:50',
            'utr_message' => 'nullable|string',
            'payment_datetime' => 'nullable|date',
        ]);

        $fixedExpense->update($validated);

        return redirect()->route('fixed-expenses.index')->with('success', 'Fixed Expense updated successfully.');
    }



    public function destroy(FixedExpense $fixedExpense)
    {
        $fixedExpense->delete();

        return redirect()->route('fixed-expenses.index')->with('success', 'Fixed Expense deleted successfully.');
    }
}
