@extends('layouts.app')
@section('page-title', 'TQ View')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="4">
                                        <h5 class="text-center">Tender Info</h5>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Tender No.</th>
                                    <td>{{ $tender->tender_no }}</td>
                                    <th>Tender Name</th>
                                    <td>{{ $tender->tender_name }}</td>
                                </tr>
                                <tr>
                                    <th>GAT Value</th>
                                    <td>{{ $tender->gst_values }}</td>
                                    <th>Tender Fees</th>
                                    <td>{{ $tender->tender_fees }}</td>
                                </tr>
                                <tr>
                                    <th>Due Date</th>
                                    <th>Due Time</th>
                                    <th>Team Member</th>
                                    <th>Remarks</th>
                                    <th>Item</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $tender->due_date }}</td>
                                    <td>{{ $tender->due_time }}</td>
                                    <td>{{ $tender->team_member }}</td>
                                    <td>{{ $tender->remarks }}</td>
                                    <td>{{ $tender->item }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="py-4">
                            <table>
                                <thead>
                                    <th colspan="4">
                                        <h5 class="text-center">TQ Received</h5>
                                    </th>
                                    <tr>
                                        <th>Date Time</th>
                                        <td>
                                            {{ $tq_received->tq_submission_date ? \Carbon\Carbon::parse($tq_received->tq_submission_date)->format('d-m-Y') : '00-00-000' }}
                                            /
                                            {{ $tq_received->tq_submission_time ? \Carbon\Carbon::parse($tq_received->tq_submission_time)->format('h:i A') : '00:00' }}
                                        </td>
                                        <th>TQ Document</th>
                                        <td>
                                            <a href="{{ asset('uploads/tq/' . $tq_received->tq_document) }}"
                                                data-fancybox="image">
                                                <img src="{{ asset('uploads/tq/' . $tq_received->tq_document) }}"
                                                    alt="image" style="height: 30px;width: 40px;">
                                            </a>
                                        </td>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr>
                                        <th colspan="2">TQ Type</th>
                                        <th colspan="2">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($tq_received && isset($tq_received->tq_type) && count($tq_received->tq_type) > 0)
                                        @foreach ($tq_received->tq_type as $key => $type)
                                            <tr>
                                                <td colspan="2">
                                                    {{ $tq_type->firstWhere('id', $type ?? '')->tq_type ?? '' }}
                                                </td>
                                                <td colspan="2">
                                                    {{ $tq_received->description[$key] ?? '' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">No data available.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <table>
                            <thead>
                                <th colspan="4">
                                    <h5 class="text-center">TQ Replied</h5>
                                </th>
                                <tr>
                                    <th>TQ Submission Date</th>
                                    <th>TQ Documents</th>
                                    <th>Proof Of Submission</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        @if ($tq_replied && $tq_replied->tq_submission_date)
                                            {{ \Carbon\Carbon::parse($tq_replied->tq_submission_date)->format('d-m-Y') }} /
                                            {{ \Carbon\Carbon::parse($tq_replied->tq_submission_time)->format('h:i A') }}
                                        @else
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($tq_replied) && !empty($tq_replied->tq_document))
                                            <a href="{{ asset('uploads/tq/' . $tq_replied->tq_document) }}"
                                                data-fancybox="image" data-caption="">
                                                <img src="{{ asset('uploads/tq/' . $tq_replied->tq_document) }}"
                                                    alt="image" style="height: 30px;width: 40px;">
                                            </a>
                                        @else
                                            <p>No document available</p>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($tq_replied) && !empty($tq_replied->proof_submission))
                                            <a href="{{ asset('uploads/tq/' . $tq_replied->proof_submission) }}"
                                                data-fancybox="image" data-caption="">
                                                <img src="{{ asset('uploads/tq/' . $tq_replied->proof_submission) }}"
                                                    alt="image" style="height: 30px;width: 40px;">
                                            </a>
                                        @else
                                            <p>No proof available</p>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table>
                            <thead>
                                <th colspan="4">
                                    <h5 class="text-center">TQ Missed</h5>
                                </th>
                                <tr>
                                    <th>Reason For Missing</th>
                                    <th>Wold You ensure not repeated</th>
                                    <th>TMS System</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{ $tq_missed ? $tq_missed->reason_missing ?? 'No reason provided' : 'No data available' }}
                                    </td>
                                    <td>
                                        {{ $tq_missed ? $tq_missed->would_repeated ?? 'No information available' : 'No data available' }}
                                    </td>
                                    <td>
                                        {{ $tq_missed ? $tq_missed->tms_system ?? 'No data available' : 'No data available' }}
                                    </td>
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
            border-collapse: collapse;
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        th {
            text-align: center;
            color: white;
            font-weight: bold;
        }
    </style>
@endpush
