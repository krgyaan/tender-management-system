@extends('layouts.app')
@section('page-title', 'WO Dashboard')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between">
                    <a class="btn btn-primary btn-sm" href="{{ route('basicdetailadd') }}">
                        Add Basic Details Direct
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="table-responsive">
                            <table class="table dataTable" id="wo-table">
                                <thead>
                                    <tr>
                                        <th>WO Date</th>
                                        <th>Project Name</th>
                                        <th>WO Number</th>
                                        <th>WO Value<br>(Pre-GST)</th>
                                        <th>LD Start <br>Date</th>
                                        <th>Max. LD Date</th>
                                        <th>PBG <br>Applicable</th>
                                        <th>Contract <br>Agreement</th>
                                        <th>Status</th>
                                        <th>Timer</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tenders as $tender)
                                        @php
                                            $basic = $tender->basic_details;
                                            $wo = $basic?->wo_details;
                                            $woacc = $basic?->wo_acceptance_yes;
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($basic?->date)->format('d-m-Y') }}</td>
                                            <td>{{ $tender->tender_name }}</td>
                                            <td>{{ $basic?->number }}</td>
                                            <td>{{ $basic?->par_gst }}</td>
                                            <td>{{ \Carbon\Carbon::parse($wo?->ldstartdate)->format('d-m-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($wo?->maxlddate)->format('d-m-Y') }}</td>
                                            <td>{{ $wo ? ($wo->pbg_applicable_status == 1 ? 'Yes' : 'No') : '' }}</td>
                                            <td>{{ $wo ? ($wo->contract_agreement_status == 1 ? 'Yes' : 'No') : '' }}</td>
                                            <td>
                                                {{--
                                                    PO Awaited (Default, when the tender result is marked WON)
                                                    Basic Details filled (When the Basic Details form is filled)
                                                    WO Details filled (When the WO Details form is filled)
                                                    WO Accepted (When WO Amendment Needed: No)
                                                    WO Amendment Requested (When WO Amendment Needed: Yes)
                                                    WO uploaded (When WO upload is filled)
                                                --}}
                                                @if (!$basic)
                                                    PO Awaited
                                                @elseif (!$wo)
                                                    Basic Details filled
                                                @elseif (!$woacc)
                                                    WO Details filled
                                                @elseif ($woacc?->accepted_initiate == 'no')
                                                    WO Amendment Requested
                                                @elseif ($woacc?->accepted_initiate == 'yes')
                                                    WO Accepted
                                                @else
                                                    WO uploaded
                                                @endif
                                            </td>
                                            <td></td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="{{ route('basicdetailadd', ['id' => $tender->id]) }}"
                                                        class="btn btn-primary btn-xs">
                                                        Basic Details
                                                    </a>
                                                    @if ($basic)
                                                        <a href="{{ route('wodetailadd', ['id' => $tender->id]) }}"
                                                            class="btn btn-info btn-xs">
                                                            WO Details
                                                        </a>
                                                        @if ($wo)
                                                            <a href="{{ route('woacceptanceform', ['id' => $basic->id]) }}"
                                                                class="btn btn-success btn-xs">
                                                                WO Acceptance
                                                            </a>
                                                        @endif
                                                        @if ($woacc?->accepted_initiate == 'yes')
                                                            <a href="{{ route('woupdate', ['id' => $basic->id]) }}"
                                                                class="btn btn-warning btn-xs">
                                                                WO Update
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('woviewbuttenfoa', ['id' => $basic->id]) }}"
                                                            class="btn btn-outline-info btn-xs">
                                                            View
                                                        </a>
                                                    @endif
                                                </div>
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
@endsection
