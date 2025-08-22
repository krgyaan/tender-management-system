@extends('layouts.app')
@section('page-title', 'Work Order Details ')
@section('content')
    @php
        $tender = $basic->tenderName->info;
        $rfq = $basic->tenderName->rfqs;
        $wo = $basic->wo_details;
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table-bordered">
                            <tr>
                                <th>FOA/SAP PO/Detailed WO</th>
                                <td>
                                    @if ($basic->foa_sap_image)
                                        <a href="{{ asset("upload/basicdetails/{$basic->foa_sap_image}") }}" target="_blank">
                                            View Document
                                        </a>
                                    @else
                                        No Document Found
                                    @endif
                                </td>
                            </tr>
                        </table>
                        <table class="table-bordered">
                            <tbody>
                                <tr>
                                    <th colspan="6" class="text-center text-uppercase">Project Summary Sheet</th>
                                </tr>
                                <tr>
                                    <th>Project Name</th>
                                    <td>{{ $basic->tenderName->tender_name }}</td>
                                    <th>WO No. / GEM Contract No.</th>
                                    <td>{{ $basic->number }}</td>
                                    <th>WO Date</th>
                                    <td>{{ Carbon\Carbon::parse($basic->date)->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-center text-uppercase">Client Details</th>
                                </tr>
                                @if (isset($woData['departments']) && count($woData['departments']))
                                    <tr>
                                        <th>#</th>
                                        <th>Department</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                    </tr>
                                    @for ($i = 0; $i < count($woData['departments']); $i++)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $woData['departments'][$i] ?? '' }}</td>
                                            <td>{{ $woData['name'][$i] ?? '' }}</td>
                                            <td>{{ $woData['designation'][$i] ?? '' }}</td>
                                            <td>{{ $woData['phone'][$i] ?? '' }}</td>
                                            <td>{{ $woData['email'][$i] ?? '' }}</td>
                                        </tr>
                                    @endfor
                                @endif
                            </tbody>
                        </table>
                        <table class="table-bordered">
                            <tbody>
                                <tr>
                                    <th colspan="6" class="text-center text-uppercase">WO Details</th>
                                </tr>
                                <tr>
                                    <th>WO Value (Pre GST)</th>
                                    <td>{{ $basic?->par_gst }}</td>
                                    <th>WO Value (GST Amount)</th>
                                    <td>{{ format_inr($basic?->par_amt) }}</td>
                                    <th>Budget</th>
                                    <td>{{ format_inr($wo?->budget) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-center text-uppercase">Completion Period</th>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td>{{ $tender?->supply + $tender?->installation }}</td>
                                    <th>Supply</th>
                                    <td>{{ $tender?->supply }}</td>
                                    <th>I&C</th>
                                    <td>{{ $tender?->installation }}</td>
                                </tr>
                                <tr>
                                    <th>Max LD%</th>
                                    <td>{{ $wo?->max_ld }}%</td>
                                    <th>LD Start Date</th>
                                    <td>{{ \Carbon\Carbon::parse($wo?->ldstartdate)->format('d-m-Y') }}</td>
                                    <th>Max LD Date</th>
                                    <td>{{ \Carbon\Carbon::parse($wo?->maxlddate)->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Terms (supply)</th>
                                    <td>{{ $tender?->pt_supply }} %</td>
                                    <th>Payment Terms (I&C)</th>
                                    <td>{{ $tender?->pt_ic }} %</td>
                                    <th>Costing Sheet</th>
                                    <td>
                                        <a href="{{ $basic->tenderName?->sheet?->driveid }}" target="_blank">Open Costing Sheet</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table-bordered">
                            <tbody>
                                <tr>
                                    <th colspan="5" class="text-center text-uppercase">WO Documents</th>
                                </tr>
                                <tr>
                                    <th>Original LOA/Gem PO/LOI/Draft PO</th>
                                    <td>
                                        @if ($basic?->image)
                                            <a href="{{ asset("upload/basicdetails/{$basic->image}") }}" target="_blank">
                                                View Document
                                            </a>
                                        @else
                                            No Document Found
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Contract Agreement Format</th>
                                    <td>
                                        @if ($wo?->file_agreement)
                                            <a href="{{ asset("upload/applicable/{$wo->file_agreement}") }}"
                                                target="_blank">
                                                View Document
                                            </a>
                                        @else
                                            Not Applicable
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Filled BG Format</th>
                                    <td>
                                        @if ($wo?->file_applicable)
                                            <a href="{{ asset("upload/applicable/{$wo->file_applicable}") }}"
                                                target="_blank">
                                                View Document
                                            </a>
                                        @else
                                            No Document Found
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table-bordered">
                            <tbody>
                                <tr>
                                    <th colspan="5" class="text-center text-uppercase">Tender Documents</th>
                                </tr>
                                <tr>
                                    <th>Scope of Work</th>
                                    <td>
                                        @if($rfq?->scopes)
                                            @foreach($rfq->scopes as $file)
                                                <a href="{{ asset('uploads/rfqdocs/' . $file->file_path) }}" target="_blank">
                                                    {{ $loop->iteration}} - {{ $file->file_path }}
                                                </a><br>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tech Specs</th>
                                    <td>
                                        @if($rfq?->technicals)
                                            @foreach($rfq->technicals as $file)
                                                <a href="{{ asset('uploads/rfqdocs/' . $file->file_path) }}" target="_blank">
                                                    {{ $loop->iteration}} - {{ $file->file_path }}
                                                </a><br>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Detailed BOQ</th>
                                    <td>
                                        @if($rfq?->boqs)
                                            @foreach($rfq->boqs as $file)
                                                <a href="{{ asset('uploads/rfqdocs/' . $file->file_path) }}" target="_blank">
                                                    {{ $loop->iteration}} - {{ $file->file_path }}
                                                </a><br>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>MAF Format</th>
                                    <td>
                                        @if($rfq?->scopes)
                                            @foreach($rfq->scopes as $file)
                                                <a href="{{ asset('uploads/rfqdocs/' . $file->file_path) }}" target="_blank">
                                                    {{ $loop->iteration}} - {{ $file->file_path }}
                                                </a><br>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>MAF from Vendor</th>
                                    <td>
                                        @if($rfq?->mafs)
                                            @foreach($rfq->mafs as $file)
                                                <a href="{{ asset('uploads/rfqdocs/' . $file->file_path) }}" target="_blank">
                                                    {{ $loop->iteration}} - {{ $file->file_path }}
                                                </a><br>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>MII Format</th>
                                    <td>
                                        @if($rfq?->miis)
                                            @foreach($rfq->miis as $file)
                                                <a href="{{ asset('uploads/rfqdocs/' . $file->file_path) }}" target="_blank">
                                                    {{ $loop->iteration}} - {{ $file->file_path }}
                                                </a><br>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>MII from Vendor</th>
                                    <td>
                                        @if($rfq?->scopes)
                                            @foreach($rfq->scopes as $file)
                                                <a href="{{ asset('uploads/rfqdocs/' . $file->file_path) }}" target="_blank">
                                                    {{ $loop->iteration}} - {{ $file->file_path }}
                                                </a><br>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>List of Documents from OEM</th>
                                    <td>{{ $rfq?->docs_list }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .table-bordered th,
        .table-bordered td {
            padding: 8px;
        }

        .table-bordered th {
            font-weight: bold;
            font-size: 14px;
        }
    </style>
@endpush
