@extends('layouts.app')
@section('page-title', 'Courier Details')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center pb-2">Courier Details</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table-bordered">
                            <tbody>
                                <tr>
                                    <th class="fw-bold ">Organization Name</th>
                                    <td>{{ $courier->to_org }}</td>
                                    <th class="fw-bold ">Name</th>
                                    <td>{{ $courier->to_name }}</td>
                                    <th class="fw-bold ">Address</th>
                                    <td>
                                        @if (strlen($courier->to_addr) > 50)
                                            {!! nl2br(wordwrap($courier->to_addr, 50, '<br>')) !!}
                                        @else
                                            {{ $courier->to_addr }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="fw-bold ">Pin Code</th>
                                    <td>{{ $courier->to_pin }}</td>
                                    <th class="fw-bold ">Mobile Number</th>
                                    <td colspan="3">{{ $courier->to_mobile }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-bold ">Soft copy of the docs</th>
                                    <td>
                                        @if (is_array(json_decode($courier->courier_docs)) || is_object(json_decode($courier->courier_docs)))
                                            @foreach (json_decode($courier->courier_docs) as $doc)
                                                <a href="{{ asset('uploads/courier_docs/' . $doc) }}" target="_blank">
                                                    View Document - {{ $loop->iteration }}
                                                </a><br>
                                            @endforeach
                                        @elseif (is_string($courier->courier_docs))
                                            <a href="{{ asset('uploads/courier_docs/' . $courier->courier_docs) }}" target="_blank">
                                                View Document
                                            </a>
                                        @else
                                            <span class="text-danger">No Documents Available</span>
                                        @endif
                                    </td>
                                    <th class="fw-bold ">Docket Slip</th>
                                    <td>
                                        @if ($courier->docket_slip)
                                            <a href="{{ asset('uploads/courier_docs/' . $courier->docket_slip) }}"
                                                target="_blank">
                                                View Docket Slip
                                            </a>
                                        @else
                                            <span class="text-danger">Not Available</span>
                                        @endif
                                    </td>
                                    <th class="fw-bold ">Delivery POD</th>
                                    <td>
                                        @if ($courier->delivery_pod)
                                            <a href="{{ asset('uploads/courier_docs/' . $courier->delivery_pod) }}"
                                                target="_blank">
                                                View Delivery POD
                                            </a>
                                        @else
                                            <span class="text-danger">Not Available</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="fw-bold ">Delivery With Expeceted time</th>
                                    <td colspan="6">{{ $courier->delivery_time }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        table.table-bordered th,
        table.table-bordered td {
            padding: 8px;
        }
    </style>
@endpush
