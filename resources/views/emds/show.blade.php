@extends('layouts.app')
@php
    $instrumentType = [
        '1' => 'Demand Draft',
        '2' => 'FDR',
        '3' => 'Cheque',
        '4' => 'BG',
        '5' => 'Bank Transfer',
        '6' => 'Pay on Portal',
    ];
    $pop = [
        1 => 'Accounts Form',
        2 => 'Initiate Followup',
        3 => 'Returned via Bank Transfer',
        4 => 'Settled with Project Account',
    ];
    $chq = [
        1 => 'Accounts Form',
        2 => 'Initiate Followup',
        3 => 'Stop the cheque from the bank',
        4 => 'Paid via Bank Transfer',
        5 => 'Deposited in Bank',
        6 => 'Cancelled/Torn',
    ];
    $dd = [
        1 => 'DD Created',
        2 => 'Followup Initiated',
        3 => 'Returned via courier',
        4 => 'Returned via Bank Transfer',
        5 => 'Settled with Project Account',
        6 => 'DD Cancellation request sent to branch',
        7 => 'DD cancelled at Branch',
    ];
    $banks = [
        'SBI' => 'State Bank of India',
        'HDFC_0026' => 'HDFC Bank',
        'ICICI' => 'ICICI Bank',
        'YESBANK_2011' => 'Yes Bank 2011',
        'YESBANK_0771' => 'Yes Bank 0771',
        'PNB_6011' => 'Punjab National Bank',
    ];
@endphp
@push('styles')
    <style>
        td {
            padding: 10px;
        }

        th {
            padding: 10px;
            font-size: 14px;
        }
    </style>
@endpush
@section('page-title', 'Emd Information')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center pb-2">EMD Details for <u>{{ $emd->project_name }}</u></h4>
                </div>
                <div class="card-body m-auto">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table-bordered" style="width: 100%;">
                                <tr>
                                    <th>Tender No</th>
                                    <td>{{ $emd->tender_id != 0 ? $emd->tender->tender_no : 'OTHER THAN TMS' }}</td>
                                    <th>Tender Name</th>
                                    <td>{{ $emd->project_name }}</td>
                                </tr>
                                <tr>
                                    <th>Instrument Type</th>
                                    <td>{{ $instrumentType[$emd->instrument_type] }}</td>
                                    <th>Requested By</th>
                                    <td>{{ $emd->requested_by }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @switch($emd->instrument_type)
                        @case('1')
                            <div class="row pt-4" id="dd">
                                <div class="table-responsive">
                                    <caption class="text-center">
                                        <h4 class="text-center pb-3">Demand Draft Details</h4>
                                    </caption>
                                    <table class="table-bordered" style="width: 100%;">
                                        <tr>
                                            <th>DD In Favour</th>
                                            <td>{{ optional($emd->emdDemandDrafts->first())->dd_favour ?? '' }}</td>
                                            <th>DD Amount</th>
                                            <td>{{ optional($emd->emdDemandDrafts->first())->dd_amt ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>DD Payable At</th>
                                            <td>{{ optional($emd->emdDemandDrafts->first())->dd_payable ?? '' }}</td>
                                            <th>DD Needs in</th>
                                            <td>{{ optional($emd->emdDemandDrafts->first())->dd_needs ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>DD Purpose</th>
                                            <td>{{ optional($emd->emdDemandDrafts->first())->dd_purpose ?? '' }}</td>
                                            <th>Courier Address</th>
                                            <td>{!! nl2br(wordwrap($emd->emdDemandDrafts->first()->courier_add, 40, "\n")) !!}</td>
                                        </tr>
                                        <tr>
                                            <th>Courier Deadline</th>
                                            <td>{{ optional($emd->emdDemandDrafts->first())->courier_deadline ?? '' }}</td>
                                            <th>DD Created At</th>
                                            <td>{{ date('d-m-Y', strtotime($emd->created_at)) }}</td>
                                        </tr>
                                        @if (optional($emd->emdDemandDrafts->first())->action ?? '')
                                            @switch(optional($emd->emdDemandDrafts->first())->action ?? '')
                                                @case('1')
                                                    <tr>
                                                        <th colspan="2" class="text-center">DD Status</th>
                                                        <td colspan="2">
                                                            {{ $dd[$emd->emdDemandDrafts->first()->action] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>DD Date</th>
                                                        <td>{{ date('d-m-Y', strtotime(optional($emd->emdDemandDrafts->first())->dd_date ?? '')) }}
                                                        </td>
                                                        <th>DD No</th>
                                                        <td>{{ optional($emd->emdDemandDrafts->first())->dd_no ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Courier request No.</th>
                                                        <td>{{ optional($emd->emdDemandDrafts->first())->req_no ?? '' }}</td>
                                                        <th>Remarks</th>
                                                        <td>{{ optional($emd->emdDemandDrafts->first())->remarks ?? '' }}</td>
                                                    </tr>
                                                    </tr>
                                                @break

                                                @case('2')
                                                    <tr>
                                                        <th colspan="2" class="text-center">DD Status</th>
                                                        <td colspan="2">
                                                            {{ $dd[$emd->emdDemandDrafts->first()->action] }}</td>
                                                    </tr>
                                                @break

                                                @case('3')
                                                    <tr>
                                                        <th colspan="2" class="text-center">DD Status</th>
                                                        <td colspan="2">
                                                            {{ $dd[$emd->emdDemandDrafts->first()->action] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Transfer Date</th>
                                                        <td>{{ date('d-m-Y', strtotime(optional($emd->emdDemandDrafts->first())->transfer_date ?? '')) }}
                                                        </td>
                                                        <th>UTR Number</th>
                                                        <td>{{ optional($emd->emdDemandDrafts->first())->utr ?? '' }}</td>
                                                    </tr>
                                                @break

                                                @case('4')
                                                    <tr>
                                                        <th colspan="2" class="text-center">DD Status</th>
                                                        <td colspan="2">
                                                            {{ $dd[$emd->emdDemandDrafts->first()->action] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Date</th>
                                                        <td>{{ date('d-m-Y', strtotime(optional($emd->emdDemandDrafts->first())->date ?? '')) }}
                                                        </td>
                                                        <th>Amount credited</th>
                                                        <td>{{ optional($emd->emdDemandDrafts->first())->amount ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Bank reference No</th>
                                                        <td>{{ optional($emd->emdDemandDrafts->first())->reference_no ?? '' }}</td>
                                                    </tr>
                                                @break

                                                @case('5')
                                                    <tr>
                                                        <th colspan="2" class="text-center">DD Status</th>
                                                        <td colspan="2">
                                                            {{ $dd[$emd->emdDemandDrafts->first()->action] }}</td>
                                                    </tr>
                                                @break

                                                @case('6')
                                                    <tr>
                                                        <th colspan="2" class="text-center">DD Status</th>
                                                        <td colspan="2">
                                                            {{ $dd[$emd->emdDemandDrafts->first()->action] }}</td>
                                                    </tr>
                                                @break

                                                @case('7')
                                                    <tr>
                                                        <th colspan="2" class="text-center">DD Status</th>
                                                        <td colspan="2">
                                                            {{ $dd[$emd->emdDemandDrafts->first()->action] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Docket No.</th>
                                                        <td>{{ optional($emd->emdDemandDrafts->first())->docket_no ?? '' }}</td>
                                                        <th>Upload Docket Slip</th>
                                                        <td>{{ optional($emd->emdDemandDrafts->first())->docket_slip ?? '' }}</td>
                                                    </tr>
                                                @break

                                                @default
                                                    <tr></tr>
                                            @endswitch
                                        @endif
                                    </table>
                                </div>
                            </div>
                        @break

                        @case('2')
                            <div class="row pt-4" id="fdr">
                                FDR
                            </div>
                        @break

                        @case('3')
                            <div class="row pt-4" id="cheque">
                                <div class="table-responsive">
                                    <caption class="text-center">
                                        <h4 class="text-center pb-3">Cheque Details</h4>
                                    </caption>
                                    <table class="table-bordered" style="width: 100%;">
                                        <tr>
                                            <th>Cheque in Favour of:</th>
                                            <td>{{ optional($emd->emdCheques->first())->cheque_favour ?? '' }}</td>
                                            <th>Cheque Amount</th>
                                            <td>{{ optional($emd->emdCheques->first())->cheque_amt ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cheque date</th>
                                            <td>{{ date('d-m-Y', strtotime(optional($emd->emdCheques->first())->cheque_date ?? '')) }}
                                            </td>
                                            <th>DD Needs in</th>
                                            <td>{{ optional($emd->emdCheques->first())->cheque_needs ?? '' }} Hrs</td>
                                        </tr>
                                        <tr>
                                            <th>Purpose of the Cheque:</th>
                                            <td>{{ optional($emd->emdCheques->first())->cheque_reason ?? '' }}</td>
                                            <th>Amount to be debited from</th>
                                            <td>{{ $emd->emdCheques }}</td>
                                        </tr>
                                        @if ($emd->emdCheques->first()->action)
                                            @switch(optional($emd->emdCheques->first())->action ?? '')
                                                @case('1')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Cheque Status</th>
                                                        <td colspan="2">{{ $chq[$emd->emdCheques->first()->action] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Cheque request:</th>
                                                        <td>{{ optional($emd->emdCheques->first())->status ?? '' }}</td>
                                                        <th>Reason for Rejection:</th>
                                                        <td>{{ optional($emd->emdCheques->first())->reason ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Cheque No.:</th>
                                                        <td>{{ optional($emd->emdCheques->first())->cheq_no ?? '' }}</td>
                                                        <th>Due date (if payable):</th>
                                                        <td>{{ optional($emd->emdCheques->first())->duedate ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Receiving of the cheque handed over:</th>
                                                        <td><a href="{{ asset('uploads/accounts/' . optional($emd->emdCheques->first())->handover ?? '') }}">View Document</a></td>
                                                        <th>Soft copy of Cheque (both sides):</th>
                                                        <td>
                                                            @if($emd->emdCheques->first())
                                                                @php $cheques = explode(',', $emd->emdCheques->first()->cheq_img); @endphp
                                                                @foreach($cheques as $cheque)
                                                                    <a href="{{ asset('uploads/accounts/' . $cheque) }}">View Cheque</a>
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Positive pay confirmation copy:</th>
                                                        <td>{{ optional($emd->emdCheques->first())->confirmation ?? '' }}</td>
                                                        <th>Remarks (if any):</th>
                                                        <td>{{ optional($emd->emdCheques->first())->remarks ?? '' }}</td>
                                                    </tr>
                                                @break

                                                @case('2')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Cheque Status</th>
                                                        <td colspan="2">{{ $chq[$emd->emdCheques->first()->action] }}</td>
                                                    </tr>
                                                @break

                                                @case('3')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Cheque Status</th>
                                                        <td colspan="2">{{ $chq[$emd->emdCheques->first()->action] }}</td>
                                                    </tr>
                                                @break

                                                @case('4')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Cheque Status</th>
                                                        <td colspan="2">{{ $chq[$emd->emdCheques->first()->action] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Transfer Date:</th>
                                                        <td>{{ optional($emd->emdCheques->first())->transfer_date ?? '' }}</td>
                                                        <th>UTR Amount:</th>
                                                        <td>{{ optional($emd->emdCheques->first())->amount ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>UTR Number</th>
                                                        <td>{{ optional($emd->emdCheques->first())->utr ?? '' }}</td>
                                                    </tr>
                                                @break

                                                @case('5')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Cheque Status</th>
                                                        <td colspan="2">{{ $chq[$emd->emdCheques->first()->action] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Transfer Date:</th>
                                                        <td>{{ optional($emd->emdCheques->first())->bt_transfer_date ?? '' }}</td>
                                                        <th>Bank Reference No:</th>
                                                        <td>{{ optional($emd->emdCheques->first())->reference ?? '' }}</td>
                                                    </tr>
                                                @break

                                                @case('6')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Cheque Status</th>
                                                        <td colspan="2">{{ $chq[$emd->emdCheques->first()->action] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Photo/confirmation from Beneficiary:</th>
                                                        <td>{{ optional($emd->emdCheques->first())->cancelled_img ?? '' }}</td>
                                                    </tr>
                                                @break

                                                @default
                                                    <tr></tr>
                                            @endswitch
                                        @endif
                                    </table>
                                </div>
                            </div>
                        @break

                        @case('4')
                            <div class="row pt-4" id="bg">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <caption class="text-center">
                                            <h4 class="text-center pb-3">Bank Guarantee Details</h4>
                                        </caption>
                                        <table class="table-bordered" style="width: 100%;">
                                            <tr>
                                                <th>Purpose of BG</th>
                                                <td>{{ ucfirst(optional($emd->emdBgs->first())->bg_purpose ?? 'N/A') }}</td>
                                                <th>Prefilled forms (unsigned)</th>
                                                <td>
                                                    @foreach (json_decode(optional($emd->emdBgs->first())->generated_pdfs ?? '[]') as $pdfPath)
                                                        <a href="{{ asset('uploads/bgpdfs/' . $pdfPath) }}" target="_blank">
                                                            {{ basename($pdfPath) }}
                                                        </a><br>
                                                    @endforeach

                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Prefilled forms (signed)</th>
                                                <td>

                                                </td>
                                                <th>BG format by TE</th>
                                                <td>
                                                    @if (optional($emd->emdBgs->first())->bg_format_te)
                                                        <a href="{{ asset('uploads/emds/' . optional($emd->emdBgs->first())->bg_format_te) }}"
                                                            target="_blank">View</a>
                                                    @else
                                                        Not Uploaded
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Rejection reasons</th>
                                                <td>{{ optional($emd->emdBgs->first())->reason_req ?? 'N/A' }}</td>
                                                <th>BG format by Accounts</th>
                                                <td>
                                                    @if (optional($emd->emdBgs->first())->bg_format_imran)
                                                        <a href="{{ asset('uploads/accounts/' . optional($emd->emdBgs->first())->bg_format_imran) }}"
                                                            target="_blank">View</a>
                                                    @else
                                                        Not Uploaded
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>PO/Request Letter/Tender Name</th>
                                                <td>
                                                    @if (optional($emd->emdBgs->first())->bg_po)
                                                        <a href="{{ asset('uploads/emds/' . optional($emd->emdBgs->first())->bg_po) }}"
                                                            target="_blank">View</a>
                                                    @else
                                                        Not Uploaded
                                                    @endif
                                                </td>
                                                <th>Client emails</th>
                                                <td>
                                                    {{ optional($emd->emdBgs->first())->bg_client_user ?? 'N/A' }}<br>
                                                    {{ optional($emd->emdBgs->first())->bg_client_fin ?? 'N/A' }}<br>
                                                    {{ optional($emd->emdBgs->first())->bg_client_cp ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Client Bank Acc. Name</th>
                                                <td>{{ optional($emd->emdBgs->first())->bg_bank_name ?? 'N/A' }}</td>
                                                <th>Client Acc No.</th>
                                                <td>{{ optional($emd->emdBgs->first())->bg_bank_acc ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>IFSC</th>
                                                <td>{{ optional($emd->emdBgs->first())->bg_bank_ifsc ?? 'N/A' }}</td>
                                                <th>Soft copy of BG</th>
                                                <td>
                                                    @if (optional($emd->emdBgs->first())->courier)
                                                        <a href="{{ asset('uploads/courier_docs/' . optional($emd->emdBgs->first())->courier->courier_docs) }}"
                                                            target="_blank">View</a>
                                                    @else
                                                        Not Uploaded
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>BG Courier Address</th>
                                                <td>{!! nl2br(wordwrap(optional($emd->emdBgs->first())->bg_courier_addr, 40, "\n")) !!}</td>
                                                <th>Courier Request No.</th>
                                                <td>{{ optional($emd->emdBgs->first())->courier_no ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Courier Docket No.</th>
                                                <td>
                                                    {{ optional($emd->emdBgs->first())->docket_no ?? 'N/A' }}
                                                </td>
                                                <th>Courier Docket Slip</th>
                                                <td>
                                                    @if (optional($emd->emdBgs->first())->docket_slip)
                                                        <a href="{{ asset('uploads/accounts/' . optional($emd->emdBgs->first())->docket_slip) }}"
                                                            target="_blank">View</a>
                                                    @else
                                                        Not Uploaded
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>SFMS</th>
                                                <td>
                                                    @if (optional($emd->emdBgs->first())->sfms_conf)
                                                        <a href="{{ asset('uploads/accounts/' . optional($emd->emdBgs->first())->sfms_conf) }}"
                                                            target="_blank">View</a>
                                                    @else
                                                        Not Uploaded
                                                    @endif
                                                </td>
                                                <th>FDR Copy</th>
                                                <td>
                                                    @if (optional($emd->emdBgs->first())->fdr_copy)
                                                        <a href="{{ asset('uploads/emds/' . optional($emd->emdBgs->first())->fdr_copy) }}"
                                                            target="_blank">View</a>
                                                    @else
                                                        Not Uploaded
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>BG Charges</th>
                                                <td>{{ optional($emd->emdBgs->first())->bg_charge_deducted ?? 'N/A' }}</td>
                                                <th>SFMS Charges</th>
                                                <td>{{ optional($emd->emdBgs->first())->sfms_charge_deducted ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Stamp Paper Charges</th>
                                                <td>{{ format_inr(optional($emd->emdBgs->first())->stamp_charge_deducted ?? 0) }}</td>
                                                <th>Returned via Courier Docket No.</th>
                                                <td>{{ optional($emd->emdBgs->first())->docket_no ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Returned via Courier Docket Slip</th>
                                                <td>
                                                    @if (optional($emd->emdBgs->first())->docket_slip)
                                                        <a href="{{ asset('uploads/accounts/' . optional($emd->emdBgs->first())->docket_slip) }}"
                                                            target="_blank">View</a>
                                                    @else
                                                        Not Uploaded
                                                    @endif
                                                </td>
                                                <th>BG cancellation confirmation</th>
                                                <td>{{ '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>FDR Cancellation Date</th>
                                                <td>{{ optional($emd->emdBgs->first())->bg_fdr_cancel_date ? date('d-m-Y', strtotime(optional($emd->emdBgs->first())->bg_fdr_cancel_date)) : 'N/A' }}
                                                </td>
                                                <th>Cancelled FDR Amount</th>
                                                <td>{{ format_inr(optional($emd->emdBgs->first())->bg_fdr_cancel_amount ?? 0) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Bank reference No.</th>
                                                <td>{{ optional($emd->emdBgs->first())->bg_fdr_cancel_ref_no ?? 'N/A' }}</td>
                                                <th>Followup objective reached proof</th>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>Followup objective reached proof image</th>
                                                <td></td>
                                                <th></th>
                                                <td></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @break

                        @case('5')
                            <div class="row pt-4" id="bank-transfer">
                                <div class="table-responsive">
                                    <caption class="text-center">
                                        <h4 class="text-center pb-3">Bank Transfer Details</h4>
                                    </caption>
                                    <table class="table-bordered" style="width: 100%;">
                                        <tr>
                                            <th>Account Number</th>
                                            <td>{{ optional($emd->emdBankTransfers->first())->bt_acc ?? '' }}</td>
                                            <th>IFSC</th>
                                            <td>{{ optional($emd->emdBankTransfers->first())->bt_ifsc ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Requested On</th>
                                            <td>{{ date('d-m-Y h:i A', strtotime($emd->created_at)) }}</td>
                                            <th>Account Holder Name</th>
                                            <td>{{ optional($emd->emdBankTransfers->first())->bt_acc_name ?? '' }}</td>
                                        </tr>
                                        @if (optional($emd->emdBankTransfers->first())->action ?? '')
                                            @switch(optional($emd->emdBankTransfers->first())->action ?? '')
                                                @case('1')
                                                    <tr>
                                                        <th class="text-center">Bank Transfer Status</th>
                                                        <td colspan="3">
                                                            {{ $pop[$emd->emdBankTransfers->first()->action] }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Bank Transfer request:</th>
                                                        <td>{{ optional($emd->emdBankTransfers->first())->status ?? '' }}</td>
                                                        <th>Reason for Rejection:</th>
                                                        <td>{{ optional($emd->emdBankTransfers->first())->reason ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>UTR for the transaction:</th>
                                                        <td>{{ optional($emd->emdBankTransfers->first())->utr ?? '' }}</td>
                                                        <th>Remarks (if any):</th>
                                                        <td>{{ optional($emd->emdBankTransfers->first())->remarks ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>UTR Message:</th>
                                                        <td>{{ optional($emd->emdBankTransfers->first())->utr_mgs ?? '' }}</td>
                                                    </tr>
                                                @break

                                                @case('2')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Bank Transfer Status</th>
                                                        <td colspan="2">
                                                            {{ $pop[$emd->emdBankTransfers->first()->action] }}
                                                        </td>
                                                    </tr>
                                                @break

                                                @case('3')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Bank Transfer Status</th>
                                                        <td colspan="2">
                                                            {{ $pop[$emd->emdBankTransfers->first()->action] }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Transfer Date:</th>
                                                        <td>{{ optional($emd->emdBankTransfers->first())->transfer_date ?? '' }}</td>
                                                        <th>UTR Number:</th>
                                                        <td>{{ optional($emd->emdBankTransfers->first())->utr_num ?? '' }}</td>
                                                    </tr>
                                                @break

                                                @case('4')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Bank Transfer Status</th>
                                                        <td colspan="2">
                                                            {{ $pop[$emd->emdBankTransfers->first()->action] }}
                                                        </td>
                                                    </tr>
                                                @break

                                                @default
                                                    <tr></tr>
                                            @endswitch
                                        @endif
                                    </table>
                                </div>
                            </div>
                        @break

                        @case('6')
                            <div class="row pt-4" id="pop">
                                <div class="table-responsive">
                                    <caption class="text-center">
                                        <h4 class="text-center pb-3">Pay on Portal Details</h4>
                                    </caption>
                                    <table class="table-bordered" style="width: 100%;">
                                        <tr>
                                            <th>Purpose</th>
                                            <td>{{ optional($emd->emdPayOnPortals->first())->purpose ?? '' }}</td>
                                            <th>Name of Portal/Website</th>
                                            <td>{{ optional($emd->emdPayOnPortals->first())->portal ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Net Banking Available</th>
                                            <td class="text-capitalize">
                                                {{ optional($emd->emdPayOnPortals->first())->is_netbanking ?? '' }}</td>
                                            <th>Yes Bank Debit Card Allowed</th>
                                            <td class="text-capitalize">
                                                {{ optional($emd->emdPayOnPortals->first())->is_debit ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Amount</th>
                                            <td>{{ number_format(optional($emd->emdPayOnPortals->first())->amount ?? '') }}</td>
                                        </tr>
                                        @if (optional($emd->emdPayOnPortals->first())->action ?? '')
                                            @switch(optional($emd->emdPayOnPortals->first())->action ?? '')
                                                @case('1')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Pay on Portal request:</th>
                                                        <td colspan="2">{{ $pop[$emd->emdPayOnPortals->first()->action] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Pay on Portal request:</th>
                                                        <td>{{ optional($emd->emdPayOnPortals->first())->status ?? '' }}</td>
                                                        <th>Reason for Rejection:</th>
                                                        <td>{{ optional($emd->emdPayOnPortals->first())->reason ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>UTR for the transaction:</th>
                                                        <td>{{ optional($emd->emdPayOnPortals->first())->utr ?? '' }}</td>
                                                        <th>Remarks (if any):</th>
                                                        <td>{{ optional($emd->emdPayOnPortals->first())->remarks ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>UTR Message:</th>
                                                        <td>{{ optional($emd->emdPayOnPortals->first())->utr_mgs ?? '' }}</td>
                                                    </tr>
                                                @break

                                                @case('2')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Pay on Portal Status</th>
                                                        <td colspan="2">{{ $pop[$emd->emdPayOnPortals->first()->action] }}</td>
                                                    </tr>
                                                @break

                                                @case('3')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Pay on Portal Status</th>
                                                        <td colspan="2">{{ $pop[$emd->emdPayOnPortals->first()->action] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Transfer Date:</th>
                                                        <td>{{ optional($emd->emdPayOnPortals->first())->transfer_date ?? '' }}</td>
                                                        <th>UTR Number:</th>
                                                        <td>{{ optional($emd->emdPayOnPortals->first())->utr_num ?? '' }}</td>
                                                    </tr>
                                                @break

                                                @case('4')
                                                    <tr>
                                                        <th colspan="2" class="text-center">Pay on Portal Status</th>
                                                        <td colspan="2">{{ $pop[$emd->emdPayOnPortals->first()->action] }}</td>
                                                    </tr>
                                                @break

                                                @default
                                                    <tr></tr>
                                            @endswitch
                                        @endif
                                    </table>
                                </div>
                            </div>
                        @break

                        @default
                            <div class="row"></div>
                    @endswitch
                </div>
            </div>
        </div>
    </div>
@endsection
