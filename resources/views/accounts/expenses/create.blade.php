@extends('layouts.app')
@section('page-title', 'Add Fixed Expense')
@section('content')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <form method="POST" action="{{ route('fixed-expenses.store') }}">
                            @csrf
                            <!-- First Row - 3 Fields -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="party_name" class="form-label">Party Name</label>
                                        <input type="text" class="form-control @error('party_name') is-invalid @enderror"
                                            name="party_name" id="party_name" value="{{ old('party_name') }}" required>
                                        @error('party_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="amount_type" class="form-label">Amount Type</label>
                                        <select class="form-select @error('amount_type') is-invalid @enderror"
                                            name="amount_type" id="amount_type" required>
                                            <option value="">Select Amount Type</option>
                                            <option value="Fixed" {{ old('amount_type') == 'Fixed' ? 'selected' : '' }}>
                                                Fixed</option>
                                            <option value="Variable"
                                                {{ old('amount_type') == 'Variable' ? 'selected' : '' }}>
                                                Variable</option>
                                        </select>
                                        @error('amount_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="amount" class="form-label">Amount</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('amount') is-invalid @enderror" name="amount"
                                            id="amount" value="{{ old('amount') }}"
                                            {{ old('amount_type', 'Fixed') == 'Variable' ? 'disabled' : 'required' }}>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Second Row - 3 Fields -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="payment_method" class="form-label">Payment Method</label>
                                        <select class="form-select @error('payment_method') is-invalid @enderror"
                                            name="payment_method" id="payment_method" required>
                                            <option value="">Select Payment Method</option>
                                            <option value="Auto Debit"
                                                {{ old('payment_method') == 'Auto Debit' ? 'selected' : '' }}>Auto Debit
                                            </option>
                                            <option value="Bank Transfer"
                                                {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank
                                                Transfer</option>
                                        </select>
                                        @error('payment_method')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="due_date" class="form-label">Due Date</label>
                                        <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                            name="due_date" id="due_date" value="{{ old('due_date') }}" required>
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="frequency" class="form-label">Payment Frequency</label>
                                        <select class="form-select @error('frequency') is-invalid @enderror"
                                            name="frequency" id="frequency" required>
                                            <option value="">Select Frequency</option>
                                            <option value="Monthly" {{ old('frequency') == 'Monthly' ? 'selected' : '' }}>
                                                Monthly</option>
                                            <option value="Quarterly"
                                                {{ old('frequency') == 'Quarterly' ? 'selected' : '' }}>
                                                Quarterly</option>
                                            <option value="Half Yearly"
                                                {{ old('frequency') == 'Half Yearly' ? 'selected' : '' }}>Half Yearly
                                            </option>
                                            <option value="Annual" {{ old('frequency') == 'Annual' ? 'selected' : '' }}>
                                                Annual</option>
                                        </select>
                                        @error('frequency')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Account Details - 3 Fields in One Line -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Account Details</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="account_name" class="form-label">Account Name</label>
                                                <input type="text"
                                                    class="form-control @error('account_name') is-invalid @enderror"
                                                    name="account_name" id="account_name" placeholder="Account Name"
                                                    value="{{ old('account_name') }}" required>
                                                @error('account_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="account_number" class="form-label">Account Number</label>
                                                <input type="text"
                                                    class="form-control @error('account_number') is-invalid @enderror"
                                                    name="account_number" id="account_number" placeholder="Account Number"
                                                    value="{{ old('account_number') }}" required>
                                                @error('account_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="ifsc" class="form-label">IFSC Code</label>
                                                <input type="text"
                                                    class="form-control @error('ifsc') is-invalid @enderror"
                                                    name="ifsc" id="ifsc" placeholder="IFSC Code"
                                                    value="{{ old('ifsc') }}" required>
                                                @error('ifsc')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Buttons -->
                            <div class="row">
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Save Expense</button>
                                    <a href="{{ route('fixed-expenses.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountType = document.getElementById('amount_type');
            const amountField = document.getElementById('amount');

            amountType.addEventListener('change', function() {
                if (this.value === 'Variable') {
                    amountField.disabled = true;
                    amountField.removeAttribute('required');
                    amountField.value = '';
                } else {
                    amountField.disabled = false;
                    amountField.setAttribute('required', 'required');
                }
            });
        });
    </script>
@endsection
