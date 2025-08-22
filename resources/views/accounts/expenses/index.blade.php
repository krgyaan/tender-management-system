@extends('layouts.app')
@section('page-title', 'Fixed Expenses Dashboard')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('fixed-expenses.create') }}" class="btn btn-primary btn-sm">
                        + Add Expense
                    </a>
                </div>
                <div class="card">
                    @include('partials.messages')
                    <div class="card-body">
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">Expenses Pending</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">Expenses Paid</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="expensesTable">
                                            <thead>
                                                <tr>
                                                    <th>Party Name</th>
                                                    <th>Amount</th>
                                                    <th>Payment Method</th>
                                                    <th>Type</th>
                                                    <th>Due Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($expenses) > 0)
                                                    @foreach ($expenses as $expense)
                                                        <tr>
                                                            <td>{{ $expense->party_name }}</td>
                                                            <td>
                                                                @if ($expense->amount_type === 'Fixed')
                                                                    ₹{{ number_format($expense->amount, 2) }}
                                                                @else
                                                                    {{ $expense->status === 'Paid' ? '₹' . number_format($expense->amount, 2) : 'Variable' }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $expense->payment_method }}</td>
                                                            <td>{{ $expense->amount_type }}</td>
                                                            <td>{{ $expense->due_date->format('d-m-Y') }}</td>
                                                            <td>
                                                                <a href="{{ route('fixed-expenses.show', $expense->id) }}"
                                                                    class="btn btn-sm btn-secondary">View</a>
                                                                <a href="{{ route('fixed-expenses.status.index', $expense->id) }}"
                                                                    class="btn btn-sm btn-info">Status</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="completedExpensesTable">
                                            <thead>
                                                <tr>
                                                    <th>Party Name</th>
                                                    <th>Amount</th>
                                                    <th>Payment Method</th>
                                                    <th>Type</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($completedExpenses) > 0)
                                                    @foreach ($completedExpenses as $expense)
                                                        <tr>
                                                            <td>{{ $expense->party_name }}</td>
                                                            <td>
                                                                @if ($expense->amount_type === 'Fixed')
                                                                    ₹{{ number_format($expense->amount, 2) }}
                                                                @else
                                                                    {{ $expense->status === 'Paid' ? '₹' . number_format($expense->amount, 2) : 'Variable' }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $expense->payment_method }}</td>
                                                            <td>{{ $expense->amount_type }}</td>
                                                            <td>{{ $expense->due_date->format('d-m-Y') }}</td>
                                                            <td>{{ $expense->status }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
