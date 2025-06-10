@extends('layouts.app')
@section('page-title', 'Tender Info')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center pb-2">EMD Details for <u>{{ $emd->project_name }}</u></h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table-bordered w-75">
                            <tbody>
                                <tr>
                                    <th class="fw-bold">Project Name</th>
                                    <td>{{ $emd->project_name }}</td>
                                    <th class="fw-bold">Instrument Type</th>
                                    <td>
                                        @php $ins = ['Demand Draft', 'FDR', 'Cheque', 'BG', 'Bank Transfer', 'Pay on Portal'][$emd->instrument_type - 1] @endphp
                                        {{ $ins }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="fw-bold">Organisation</th>
                                    <td>{{ $emd->requested_by }}</td>
                                    <th class="fw-bold">{{ $ins }} in Favour of</th>
                                    <td>
                                        @php
                                            if ($emd->instrument_type == 1) {
                                                echo $emd->emdDemandDrafts->first()->dd_favour;
                                            } elseif ($emd->instrument_type == 2) {
                                                echo $emd->emdFdrs->first()->fdr_favour;
                                            } elseif ($emd->instrument_type == 3) {
                                                echo $emd->emdCheques->first()->cheque_favour;
                                            } elseif ($emd->instrument_type == 4) {
                                                echo $emd->emdBgs->bg_favour;
                                            }
                                        @endphp
                                    </td>
                                </tr>
                                @if ($emd->instrument_type == 1)
                                    <tr>
                                        <th class="fw-bold">DD Amount</th>
                                        <td>{{ $emd->emdDemandDrafts->first()->dd_amt }}</td>

                                        <th class="fw-bold">DD Payable At</th>
                                        <td>{{ $emd->emdDemandDrafts->first()->dd_payable }}</td>
                                    </tr>
                                    <tr>
                                        <th class="fw-bold">DD Needs in</th>
                                        <td>{{ $emd->emdDemandDrafts->first()->dd_needs }} Hours</td>
                                    </tr>
                                @elseif ($emd->instrument_type == 2)
                                    <tr>
                                        <th class="fw-bold">Purpose of FDR</th>
                                        <td>{{ $emd->emdFdrs->first()->fdr_purpose }}</td>
                                        <th class="fw-bold">FDR Amount</th>
                                        <td>{{ $emd->emdFdrs->first()->fdr_amt }}</td>
                                    </tr>
                                    <tr>
                                        <th class="fw-bold">FDR Expiry</th>
                                        <td>{{ $emd->emdFdrs->first()->fdr_expiry }}</td>
                                        <th class="fw-bold">FDR Needs in</th>
                                        <td>{{ $emd->emdFdrs->first()->fdr_needs }} Hours</td>
                                    </tr>
                                    <tr>
                                        <th class="fw-bold">Party Bank Details <br>(In case of Joint FDR)</th>
                                        <td>
                                            <p>Bank Account Name: <u>{{ $emd->emdFdrs->first()->fdr_bank_name }}</u></p>
                                            <p>Bank Account: <u>{{ $emd->emdFdrs->first()->fdr_bank_acc }}</u></p>
                                            <p>Bank IFSC: <u>{{ $emd->emdFdrs->first()->fdr_bank_ifsc }}</u></p>
                                        </td>
                                    </tr>
                                @elseif ($emd->instrument_type == 3)
                                    <tr>
                                        <th class="fw-bold">Cheque Amount</th>
                                        <td>{{ $emd->emdCheques->first()->cheque_amt }}</td>
                                        <th class="fw-bold">Cheque Date</th>
                                        <td>{{ date('d M, Y', strtotime($emd->emdCheques->first()->cheque_date)) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="fw-bold">Cheque needed in</th>
                                        <td>{{ $emd->emdCheques->first()->cheque_needs }} Hours</td>
                                        <th class="fw-bold">Reason</th>
                                        <td>
                                            @php
                                                $reason = $emd->emdCheques->first()->cheque_reason;
                                                if ($reason == 'security') {
                                                    echo 'Security (Not to be filled by counter party)';
                                                } else {
                                                    echo 'Payable (To be paid on date of payment)';
                                                }
                                            @endphp
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="fw-bold">Cheque Give from Account</th>
                                        <td>
                                            @php
                                                $banks = [
                                                    '1' => 'State Bank of India',
                                                    '2' => 'HDFC Bank',
                                                    '3' => 'ICICI Bank',
                                                    '4' => 'Yes Bank 2011',
                                                    '5' => 'Yes Bank 0771',
                                                    '6' => 'Punjab National Bank',
                                                ];
                                            @endphp
                                            @if ($emd->emdCheques->first()->cheque_bank)
                                                {{ $banks[$emd->emdCheques->first()->cheque_bank] }}
                                            @endif
                                        </td>
                                    </tr>
                                @elseif ($emd->instrument_type == 4)
                                    <tr>
                                        <th class="fw-bold">BG In Favour of</th>
                                        <td>{{ $emd->emdBgs->bg_favour }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th class="fw-bold">{{ $ins }} Status</th>
                                    <td>
                                        @php
                                            if ($emd->instrument_type == 1) {
                                                $st = $emd->emdDemandDrafts->first()->dd_status;
                                                $status =
                                                    $st == 'accepted'
                                                        ? '<span class="badge bg-success">Accepted</span>'
                                                        : '<span class="badge bg-danger">Rejected</span>';
                                                echo $status;
                                            } elseif ($emd->instrument_type == 2) {
                                                $st = $emd->emdFdrs->first()->fdr_status;
                                                $status =
                                                    $st == 'accepted'
                                                        ? '<span class="badge bg-success">Accepted</span>'
                                                        : '<span class="badge bg-danger">Rejected</span>';
                                                echo $status;
                                            } elseif ($emd->instrument_type == 3) {
                                                $st = $emd->emdCheques->first()->cheque_status;
                                                $status =
                                                    $st == 'accepted'
                                                        ? '<span class="badge bg-success">Accepted</span>'
                                                        : '<span class="badge bg-danger">Rejected</span>';
                                                echo $status;
                                            } elseif ($emd->instrument_type == 4) {
                                                $st = $emd->emdBgs->bg_status->first()->bg_status;
                                                $status =
                                                    $st == 'accepted'
                                                        ? '<span class="badge bg-success">Accepted</span>'
                                                        : '<span class="badge bg-danger">Rejected</span>';
                                                echo $status;
                                            }
                                        @endphp
                                    </td>
                                    <th class="fw-bold">Rejection Remark</th>
                                    <td>
                                        @php
                                            if ($emd->instrument_type == 1) {
                                                echo $emd->emdDemandDrafts->first()->dd_rejection;
                                            } elseif ($emd->instrument_type == 2) {
                                                echo $emd->emdFdrs->first()->fdr_rejection;
                                            } elseif ($emd->instrument_type == 3) {
                                                echo $emd->emdCheques->first()->cheque_rejection;
                                            } elseif ($emd->instrument_type == 4) {
                                                echo $emd->emdBgs->first()->bg_rejection;
                                            }
                                        @endphp
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="pt-4">
                            <a href="{{ route('emds.index') }}" class="btn btn-outline-light">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        tr,
        th,
        td {
            padding: 8px;
        }
    </style>
@endpush
