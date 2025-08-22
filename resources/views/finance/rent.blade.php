@extends('layouts.app')
@section('page-title', 'Rent Agreements')
@section('content')
@php
use Carbon\Carbon;
@endphp
    <div class="container-fluid content-inner p-0">
        <section>
            <div class="row">
                <div class="col-md-12 m-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('rent_add') }}" class="btn btn-primary btn-sm">Add Rental Aggereement</a>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table dataTable" id="allUsers" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>First Party</th>
                                            <th>Second Party</th>
                                            <th>Rent Amount</th>
                                            <th>Security Deposit</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Rent Increment <br>at Expiry</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rentdata as $key => $rentdataItem)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $rentdataItem->first_party }}</td>
                                                <td>{{ $rentdataItem->second_party }}</td>
                                                <td>{{ format_inr($rentdataItem->rent_amount) }}</td>
                                                <td>{{ format_inr($rentdataItem->security_deposit) }}</td>
                                                <td>{{ date('d/m/Y', strtotime($rentdataItem->start_date)) }}</td>
                                                <td>{{ date('d/m/Y', strtotime($rentdataItem->end_date)) }}</td>
                                                <td>{{ $rentdataItem->rent_increment_at_expiry }}</td>
                                                <td>
                                                    <a href="/upload/finance/{{ $rentdataItem->image }}"
                                                        data-fancybox="proof-gallery" target="_blank">
                                                        <i class="fa fa-file-text fs-4" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    @php
                                                        $currentDate = \Carbon\Carbon::now();
                                                        $endDate = \Carbon\Carbon::parse($rentdataItem->end_date);
                                                    @endphp

                                                    @if ($currentDate->gt($endDate))
                                                        <span class="badge bg-danger">Expired</span>
                                                    @else
                                                        <span class="badge bg-success">Active</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a hidden href="{{ route('update.rent.status') }}"
                                                        class="btn btn-warning">U</a>
                                                    <a href="{{ asset('admin/rent_edit/' . Crypt::encrypt($rentdataItem->id)) }}"
                                                        class="btn btn-info btn-sm">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                                    </a>
                                                    <a onclick="return check_delete()"
                                                        href="{{ asset('admin/rent_delete/' . Crypt::encrypt($rentdataItem->id)) }}"
                                                        class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>


                                                    <form action="https://abs.hyperofficial.in/admin/user/delete/3"
                                                        method="POST" id="deleteForm3" style="display: none;">
                                                        <input type="hidden" name="_token"
                                                            value="RKmnlheYhxA6XXGcKXYnMjAzOW1dzl9NWr2M1Fs4"
                                                            autocomplete="off"> <input type="hidden" name="_method"
                                                            value="POST"> <input type="hidden" name="id"
                                                            value="3">
                                                    </form>
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
        </section>
    </div>
@endsection
