@extends('layouts.app')
@section('page-title', 'Kick Off Meeting Details ')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table-bordered">
                            <tr>
                                <th>LOA/GEM/LOI/Draft WO</th>
                                <td>
                                    @if ($basic->image)
                                        <a href="{{ asset("upload/basicdetails/{$basic->image}") }}" target="_blank">
                                            View Document
                                        </a>
                                    @else
                                        No Document Found
                                    @endif
                                </td>
                            </tr>
                        </table>
                        <table class="table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="5" class="text-center text-uppercase">Client Details</th>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($woData['departments']) && count($woData['departments']))
                                    @for ($i = 0; $i < count($woData['departments']); $i++)
                                        <tr>
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
                        @if ($basic->wo_acceptance_yes?->wo_yes == 1)
                            <table class="table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <td>
                                            {{ Carbon\Carbon::parse($basic->date)->format('d-m-Y') }}
                                        </td>
                                        <th>PO/WO No.</th>
                                        <th>
                                            {{ $basic->number }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Page No.</th>
                                        <th>Clause No.</th>
                                        <th>Present Text</th>
                                        <th>Corrected Text</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($changes['page']) && count($changes['page']))
                                        @for ($i = 0; $i < count($changes['page']); $i++)
                                            <tr>
                                                <td>{{ $changes['page'][$i] ?? '' }}</td>
                                                <td>{{ $changes['clause'][$i] ?? '' }}</td>
                                                <td>{{ $changes['current'][$i] ?? '' }}</td>
                                                <td>{{ $changes['correct'][$i] ?? '' }}</td>
                                            </tr>
                                        @endfor
                                    @endif
                                </tbody>
                            </table>
                        @endif
                        <table class="table-bordered">
                            <tr>
                                <th>Budget (Pre GST)</th>
                                <td>{{ format_inr($basic->wo_details?->budget) }}</td>
                                <th>Max LD%</th>
                                <td>{{ $basic->wo_details?->max_ld }}%</td>
                                <th>BG Format</th>
                                <td>
                                    @if ($basic->wo_details?->file_applicable)
                                        <a href="{{ asset("upload/applicable/{$basic->wo_details->file_applicable}") }}"
                                            target="_blank">
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
                                    @if ($basic->wo_details?->file_agreement)
                                        <a href="{{ asset("upload/applicable/{$basic->wo_details->file_agreement}") }}"
                                            target="_blank">
                                            View Document
                                        </a>
                                    @else
                                        No Document Found
                                    @endif
                                </td>
                                <th>LOA/GEM/LOI/Draft WO</th>
                                <td>
                                    @if ($basic->lo_gem_img)
                                        <a href="{{ asset("upload/basicdetails/{$basic->lo_gem_img}") }}" target="_blank">
                                            View Document
                                        </a>
                                    @else
                                        No Document Found
                                    @endif
                                </td>
                                <th>Upload FOA/SAP PO/Detailed WO</th>
                                <td>
                                    @if ($basic->foa_sap_image)
                                        <a href="{{ asset("upload/basicdetails/{$basic->foa_sap_image}") }}"
                                            target="_blank">
                                            View Document
                                        </a>
                                    @else
                                        No Document Found
                                    @endif
                                </td>
                            </tr>
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
