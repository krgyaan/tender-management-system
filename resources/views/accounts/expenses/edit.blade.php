@extends('layouts.app')
@section('page-title', 'Edit Fixed Expense')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
                @include('partials.messages')

                <form action="{{ route('fixed-expenses.update', $fixedExpense->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="party_name" class="form-label">Party Name</label>
                            <input type="text" name="party_name" id="party_name" class="form-control"
                                value="{{ old('party_name', $fixedExpense->party_name) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="amount_type" class="form-label">Amount Type</label>
                            <select name="amount_type" id="amount_type" class="form-select" required>
                                <option value="">Select</option>
                                <option value="Fixed"
                                    {{ old('amount_type', $fixedExpense->amount_type) == 'Fixed' ? 'selected' : '' }}>Fixed
                                </option>
                                <option value="Variable"
                                    {{ old('amount_type', $fixedExpense->amount_type) == 'Variable' ? 'selected' : '' }}>
                                    Variable
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                                value="{{ old('amount', $fixedExpense->amount) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <input type="text" name="payment_method" id="payment_method" class="form-control"
                                value="{{ old('payment_method', $fixedExpense->payment_method) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="account_name" class="form-label">Account Name (optional)</label>
                            <input type="text" name="account_name" id="account_name" class="form-control"
                                value="{{ old('account_name', $fixedExpense->account_name) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="account_number" class="form-label">Account Number (optional)</label>
                            <input type="text" name="account_number" id="account_number" class="form-control"
                                value="{{ old('account_number', $fixedExpense->account_number) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="ifsc" class="form-label">IFSC (optional)</label>
                            <input type="text" name="ifsc" id="ifsc" class="form-control"
                                value="{{ old('ifsc', $fixedExpense->ifsc) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Type</label>
                            <input type="text" name="type" id="type" class="form-control"
                                value="{{ old('type', $fixedExpense->type) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control"
                                value="{{ old('due_date', $fixedExpense->due_date->format('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="frequency" class="form-label">Frequency</label>
                            <input type="text" name="frequency" id="frequency" class="form-control"
                                value="{{ old('frequency', $fixedExpense->frequency) }}" required>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary">Update Expense</button>
                            <a href="{{ route('fixed-expenses.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
