@extends('layouts.app')
@section('page-title', 'Edit Fixed Expenses Checklist')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST" class="needs-validation" novalidate
                            onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                            @csrf
                            <div class="row">
                                <!-- Party Name -->
                                <div class="form-group col-md-6">
                                    <label for="party_name" class="form-label">Party Name</label>
                                    <input type="text" name="party_name" id="party_name" class="form-control" required
                                        value="{{ old('party_name') }}">
                                </div>

                                <!-- Amount Type -->
                                <div class="form-group col-md-3">
                                    <label for="amount_type" class="form-label">Amount Type</label>
                                    <select name="amount_type" id="amount_type" class="form-control" required>
                                        <option value="">Choose</option>
                                        <option value="Fixed" {{ old('amount_type') == 'Fixed' ? 'selected' : '' }}>Fixed
                                        </option>
                                        <option value="Variable" {{ old('amount_type') == 'Variable' ? 'selected' : '' }}>
                                            Variable</option>
                                    </select>
                                </div>

                                <!-- Amount -->
                                <div class="form-group col-md-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" step="any" name="amount" id="amount" class="form-control"
                                        required value="{{ old('amount') }}">
                                </div>
                            </div>

                            <div class="row">
                                <!-- Payment Method -->
                                <div class="form-group col-md-6">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-control" required>
                                        <option value="">Choose</option>
                                        <option value="Auto Debit"
                                            {{ old('payment_method') == 'Auto Debit' ? 'selected' : '' }}>Auto Debit
                                        </option>
                                        <option value="Bank Transfer"
                                            {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Account Details -->
                                <div class="form-group col-md-4">
                                    <label for="account_name" class="form-label">Account Name</label>
                                    <input type="text" name="account_name" id="account_name" class="form-control"
                                        value="{{ old('account_name') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" name="account_number" id="account_number" class="form-control"
                                        value="{{ old('account_number') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="ifsc" class="form-label">IFSC Code</label>
                                    <input type="text" name="ifsc" id="ifsc" class="form-control"
                                        value="{{ old('ifsc') }}">
                                </div>
                            </div>

                            <div class="row">
                                <!-- Type -->
                                <div class="form-group col-md-4">
                                    <label for="type" class="form-label">Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="">Choose</option>
                                        @foreach (['Electricity', 'Rent', 'Salary', 'Communication', 'Software', 'Admin'] as $type)
                                            <option value="{{ $type }}"
                                                {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Due Date -->
                                <div class="form-group col-md-4">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control" required
                                        value="{{ old('due_date') }}">
                                </div>

                                <!-- Payment Frequency -->
                                <div class="form-group col-md-4">
                                    <label for="frequency" class="form-label">Payment Frequency</label>
                                    <select name="frequency" id="frequency" class="form-control" required>
                                        <option value="">Choose</option>
                                        @foreach (['Monthly', 'Quarterly', 'Half Yearly', 'Annual'] as $freq)
                                            <option value="{{ $freq }}"
                                                {{ old('frequency') == $freq ? 'selected' : '' }}>{{ $freq }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group text-end mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
