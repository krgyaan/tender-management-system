@extends('layouts.app')
@section('page-title', 'Fixed Expenses Checklist')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('fixed-expenses.create') }}" class="btn btn-sm btn-primary">Fixed Expense Entry</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Party Name</th>
                                        <th>Amount</th>
                                        <th>Payment Mode</th>
                                        <th>Type</th>
                                        <th>Due Date</th>
                                        <th>View</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
