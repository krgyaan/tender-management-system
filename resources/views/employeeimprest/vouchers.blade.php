@extends('layouts.app')
@section('page-title', 'Employee Imprest Vouchers')
@section('content')
    <div class="row">
        @include('partials.messages')
        <div class="col-md-12 mx-auto mt-4">
            <div class="d-flex justify-content-between align-items-center">
                @if (Auth::user()->role == 'admin' || Str::startsWith(Auth::user()->role, 'account'))
                    <a href="{{ route('employeeimprest_account') }}" class="btn btn-outline-danger btn-sm">back</a>
                @else
                    <a href="{{ route('employeeimprest') }}" class="btn btn-outline-danger btn-sm">back</a>
                @endif
            </div>
            <div class="card">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Voucher Period</th>
                                    <th>Voucher Amount</th>
                                    <th>Buttons</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($vouchers != null && $vouchers->count() > 0)
                                    @foreach ($vouchers as $voucher)
                                        <tr>
                                            <td>{{ $voucher->user->name }}</td>
                                            <td>
                                                {{ 'Week: ' . $voucher->week }} <br>
                                                {{ date('d M, Y', strtotime($voucher->start_date)) }} -
                                                {{ date('d M, Y', strtotime($voucher->end_date)) }}
                                            </td>
                                            <td>{{ $voucher->total_amount }}</td>
                                            <td class="d-flex gap-2 flex-wrap">
                                                <form action="{{ route('voucher-view') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="from" value="{{ $voucher->start_date }}">
                                                    <input type="hidden" name="to" value="{{ $voucher->end_date }}">
                                                    <input type="hidden" name="name_id" value="{{ $voucher->name_id }}">
                                                    <button type="submit" class="btn btn-light btn-sm">
                                                        View Voucher
                                                    </button>
                                                </form>
                                                <a href="{{ route('view-proof', Crypt::encrypt($voucher->all_invoice_proofs)) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    View Proof
                                                </a>
                                            </td>
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
@endsection
