@extends('layouts.app')
@section('page-title', ' Employees Imprest (Account)')
@section('content')
    <div class="page-wrapper">
        @include('partials.messages')
        <div class="page-content">
            <div class="row ">
                <div class="col-md-3 p-4 rounded shadow border">
                    <h6>Amount Received</h6>
                    <p>{{ format_inr($amountrecevied) }}</p>
                </div>
                <div class="col-md-3 p-4 rounded shadow border">
                    <h6>Amount Spent</h6>
                    <p>{{ format_inr($employeeamount) }}</p>
                </div>
                <div class="col-md-3 p-4 rounded shadow border">
                    <h6>Amount Approved</h6>
                    <p>{{ format_inr($amountapproved) }} </p>
                </div>
                <div class="col-md-3 p-4 rounded shadow border">
                    <h6>Amount Left</h6>
                    <p>{{ format_inr($amountspent) }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mx-auto mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('employeeimprest_add') }}" class="btn btn-primary btn-sm">Add Imprest</a>
                        <a href="{{ route('imprest-voucher') }}" class="btn btn-sm btn-success">All Imprest Voucher</a>
                        <a href="{{ route('payment-history') }}" class="btn btn-secondary btn-sm">Full Payment History</a>
                    </div>
                    <div class="card">
                        <div class="card-header px-4 py-3">
                            <h5 class="mb-0">All Employee Imprest Details</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Employee Name</th>
                                                    <th>Amount Received</th>
                                                    <th>Amount Spent</th>
                                                    <th>Amount Approved</th>
                                                    <th>Amount Left</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($employeeimprest as $employee)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ asset('/admin/employeeimprest_dashboard') }}/{{ $employee->name_id }}"
                                                                @class(['fs-6', 'font-bold' => true])>
                                                                {{ $employee->user->name }}
                                                                <i class="fa fa-external-link-alt text-primary"></i>
                                                            </a>
                                                        </td>
                                                        <td>{{ format_inr($employee->getAmtReceived($employee->name_id)) }}</td>
                                                        <td>{{ format_inr($employee->getAmtSpent($employee->name_id)) }}</td>
                                                        <td>{{ format_inr($employee->getAmtApproved($employee->name_id)) }}</td>
                                                        <td>{{ format_inr($employee->getAmtLeft($employee->name_id)) }}</td>
                                                        <td>
                                                            <a href="{{ asset('/admin/employeeimprest_dashboard') }}/{{ $employee->name_id }}"
                                                                class="btn btn-info btn-sm">
                                                                Dashboard
                                                            </a>
                                                            <a href="{{ route('payment-history', $employee->name_id) }}"
                                                                class="btn btn-secondary btn-sm">
                                                                Payment History
                                                            </a>
                                                            <a href="{{ route('imprest-voucher', $employee->name_id) }}"
                                                                class="btn btn-sm btn-outline-success">
                                                                Imprest Voucher
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
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
    </div>
@endsection
