@extends('layouts.app')
@section('page-title', 'Fixed Expense Details')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <p>
                            <strong>Party Name:</strong> {{ $fixedExpense->party_name }}
                        </p>
                        <p>
                            <strong>Amount Type:</strong> {{ $fixedExpense->amount_type }}
                        </p>
                        @if ($fixedExpense->amount_type == 'Fixed')
                            <p>
                                <strong>Amount:</strong> {{ $fixedExpense->amount }}
                            </p>
                        @endif
                        <p>
                            <strong>Payment Method:</strong> {{ $fixedExpense->payment_method }}
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <p>
                            <strong>Account Name:</strong> {{ $fixedExpense->account_name ?? '-' }}
                        </p>
                        <p>
                            <strong>Account Number:</strong> {{ $fixedExpense->account_number ?? '-' }}
                        </p>
                        <p>
                            <strong>IFSC:</strong> {{ $fixedExpense->ifsc ?? '-' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('fixed-expenses.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                <a href="{{ route('fixed-expenses.edit', $fixedExpense->id) }}" class="btn btn-primary btn-sm">Edit</a>
            </div>
        </div>
    </section>
@endsection
