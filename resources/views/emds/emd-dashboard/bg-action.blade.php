@extends('layouts.app')
@section('page-title', 'Bank Guarantee Actions')
@php
    $ferq = [
        '1' => 'Daily',
        '2' => 'Alternate Days',
        '3' => '2 times a day',
        '4' => 'Weekly (every Mon)',
        '5' => 'Twice a Week (every Mon & Thu)',
        '6' => 'Stop',
    ];
    $instrumentType = [
        '0' => 'NA',
        '1' => 'Demand Draft',
        '2' => 'FDR',
        '3' => 'Cheque',
        '4' => 'BG',
        '5' => 'Bank Transfer',
        '6' => 'Pay on Portal',
    ];

    $bgStatus = [
        1 => 'Accounts Form (BG) 1 - Request to Bank',
        2 => 'Accounts Form (BG) 2 - After BG Creation',
        3 => 'Accounts Form (BG) 3 - Capture FDR Details',
        4 => 'Initiate Followup',
        5 => 'Request Extension',
        6 => 'Returned via courier',
        7 => 'Request Cancellation',
        8 => 'BG Cancellation Confirmation',
        9 => 'FDR Cancellation Confirmation',
    ];
    $couriers = App\Models\CourierDashboard::all();
@endphp
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <form action="{{ route('bg-action', $bg->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="action" class="form-label">Choose What to do:</label>
                                        <select class="form-control" id="bgaction" name="action">
                                            <option value="">Select Action</option>
                                            @foreach ($bgStatus as $key => $act)
                                                <option value="{{ $key }}"
                                                    {{ $bg->action == $key ? 'selected' : '' }}>
                                                    {{ $act }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: {{ $bg->action == '1' ? 'flex' : 'none' }}" id="bgaccount1">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="bg_req">BG request</label>
                                    <select name="bg_req" class="form-control" id="bg_req">
                                        <option value="">-- Select --</option>
                                        <option {{ $bg->bg_req == 'Accepted' ? 'selected' : '' }} value="Accepted">Accepted
                                        </option>
                                        <option {{ $bg->bg_req == 'Rejected' ? 'selected' : '' }} value="Rejected">Rejected
                                        </option>
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('bg_req') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="reason_req">Reason for Rejection/Changes required</label>
                                    <textarea name="reason_req" class="form-control" id="reason_req">{{ $bg->reason_req }}</textarea>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('reason_req') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="approve_bg">
                                        Approved BG Format
                                    </label>
                                    <select name="approve_bg" class="form-control" id="approve_bg">
                                        <option value="">-- Select --</option>
                                        <option {{ $bg->approve_bg == '1' ? 'selected' : '' }} value="1">Accept the BG
                                            given by TE</option>
                                        <option {{ $bg->approve_bg == '2' ? 'selected' : '' }} value="2">Upload by
                                            Imran</option>
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('approve_bg') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4" id="bg_format_imran"
                                    style="display: {{ $bg->approve_bg == '2' ? 'flex' : 'none' }}">
                                    <label class="form-label" for="bg_format_imran">Upload Documents</label>
                                    <input type="file" name="bg_format_imran" class="form-control" id="bg_format_imran"
                                        accept=".pdf,.jpeg,.jpg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('bg_format_imran') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="prefilled_signed_bg">Prefilled Bank Formats, Signed by
                                        CEO</label>
                                    <input type="file" name="prefilled_signed_bg[]" class="form-control" id="prefilled_signed_bg" multiple>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('prefilled_signed_bg') }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="row" style="display: {{ $bg->action == '2' ? 'flex' : 'none' }}"
                                id="bgaccount2">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="bg_no">BG no</label>
                                    <input type="text" name="bg_no" class="form-control" id="bg_no"
                                        value="{{ $bg->bg_no }}">
                                    </input>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('bg_no') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="bg_date">BG Creation Date</label>
                                    <input type="date" name="bg_date" class="form-control" id="bg_date"
                                        value="{{ $bg->bg_date }}">
                                    </input>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('bg_no') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="bg_validity">BG Validity</label>
                                    <input type="date" name="bg_validity" class="form-control" id="bg_validity"
                                        value="{{ $bg->bg_validity ?? '' }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('bg_validity') }}</span>
                                    </small>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="form-label" for="claim_expiry">BG Claim Period Expiry</label>
                                    <input type="date" name="claim_expiry" class="form-control" id="claim_expiry"
                                        value="{{ $bg->claim_expiry }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('claim_expiry') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="courier_no">
                                        Courier Request No.:
                                    </label>
                                    <select name="courier_no" class="form-control" id="courier_no">
                                        <option value="">Select Courier</option>
                                        @foreach ($couriers as $courier)
                                            <option value="{{ $courier->id }}"
                                                {{ $bg->courier_no == $courier->id ? 'selected' : '' }}>
                                                {{ $courier->id }} - {{ $courier->to_org }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('courier_no') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="bg2_remark">
                                        Remarks (if any)
                                    </label>
                                    <textarea name="bg2_remark" class="form-control" id="bg2_remark">{{ $bg->bg2_remark }}</textarea>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('bg2_remark') }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="row" style="display: {{ $bg->action == '3' ? 'flex' : 'none' }}"
                                id="bgaccount3">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="sfms_conf">SFMS Confirmation copy</label>
                                    <input type="file" name="sfms_conf" class="form-control" id="sfms_conf"
                                        accept=".pdf,.jpeg,.jpg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                    @if ($bg->sfms_conf)
                                        <a href="{{ asset('uploads/accounts/' . $bg->sfms_conf) }}"
                                            target="_blank">View</a>
                                    @endif
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('sfms_conf') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="fdr_per">FDR Percentage</label>
                                    <select name="fdr_per" class="form-control" id="fdr_per">
                                        <option value="">Select %age</option>
                                        <option value="10" {{ $bg->fdr_per == '10' ? 'selected' : '' }}>
                                            10%
                                        </option>
                                        <option value="15" {{ $bg->fdr_per == '15' ? 'selected' : '' }}>
                                            15%
                                        </option>
                                        <option value="100" {{ $bg->fdr_per == '100' ? 'selected' : '' }}>
                                            100%
                                        </option>
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('fdr_per') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="fdr_amt">FDR Amount</label>
                                    <input type="number" step="any" name="fdr_amt" class="form-control"
                                        id="fdr_amount" value="{{ $bg->fdr_amount ?? '' }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('fdr_amt') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="fdr_copy">FDR copy</label>
                                    <input type="file" name="fdr_copy" class="form-control" id="fdr_copy"
                                        accept=".pdf,.jpeg,.jpg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                    @if ($bg->fdr_copy)
                                        <a href="{{ asset('uploads/accounts/' . $bg->fdr_copy) }}"
                                            target="_blank">View</a>
                                    @endif
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('fdr_copy') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="fdr_no">
                                        FDR No
                                    </label>
                                    <input type="text" name="fdr_no" class="form-control" id="fdr_no" value="{{ $bg->fdr_no }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('fdr_no') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="fdr_validity">
                                        FDR Validity
                                    </label>
                                    <input type="date" name="fdr_validity" class="form-control" id="fdr_validity"
                                        value="{{ $bg->fdr_validity }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('fdr_validity') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="fdr_roi">
                                        FDR ROI%
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="fdr_roi" class="form-control" id="fdr_roi"
                                            step="0.01" value="{{ $bg->fdr_roi }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('fdr_roi') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="bg_charge_deducted">
                                        BG Charges deducted
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="bg_charge_deducted" class="form-control"
                                            id="bg_charge_deducted" step="0.01" min="0"
                                            value="{{ $bg->bg_charge_deducted }}">
                                    </div>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('bg_charge_deducted') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="sfms_charge_deducted">
                                        SFMS Charges deducted
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="sfms_charge_deducted" class="form-control"
                                            id="sfms_charge_deducted" step="0.01" min="0"
                                            value="{{ $bg->sfms_charge_deducted }}">
                                    </div>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('sfms_charge_deducted') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="stamp_charge_deducted">
                                        Stamp Paper Charges Deducted
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="stamp_charge_deducted" class="form-control"
                                            id="stamp_charge_deducted" step="0.01" min="0"
                                            value="{{ $bg->stamp_charge_deducted }}">
                                    </div>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('stamp_charge_deducted') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="other_charge_deducted">
                                        Other charges deducted (if any)
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="other_charge_deducted" class="form-control"
                                            id="other_charge_deducted" step="0.01" min="0"
                                            value="{{ $bg->other_charge_deducted }}">
                                    </div>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('other_charge_deducted') }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="row" style="display: {{ $bg->action == '4' ? 'flex' : 'none' }}"
                                id="bgfollowup">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="org_name" class="form-label">Organisation Name</label>
                                        <input type="text" name="org_name" class="form-control" id="org_name">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <label class="form-label">Contact details:</label>
                                            <a href="javascript:void(0)" class="addPopFollowup">Add Person</a>
                                        </div>
                                        <div class="row" id="popfollowups">
                                            <div class="col-md-4 form-group">
                                                <input type="text" name="fp[0][name]" class="form-control"
                                                    id="name" placeholder="Name">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <input type="number" name="fp[0][phone]" class="form-control"
                                                    id="phone" placeholder="Phone">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <input type="email" name="fp[0][email]" class="form-control"
                                                    id="email" placeholder="Email">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="start_date">Followup Start From:</label>
                                        <input type="date" name="start_date" class="form-control" id="start_date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="frequency">Followup Frequency:</label>
                                        <select name="frequency" id="frequency" class="form-control">
                                            <option value="">choose</option>
                                            @foreach ($ferq as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 stop" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label" for="stop_reason">Why Stop:</label>
                                        <select name="stop_reason" class="form-control" id="stop_reason">
                                            <option value="">choose</option>
                                            <option value="1">
                                                The person is getting angry/or has requested to stop
                                            </option>
                                            <option value="2">Followup Objective achieved</option>
                                            <option value="3">External Followup Initiated</option>
                                            <option value="4">Remarks</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 stop_proof" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label">Please give proof:</label>
                                        <textarea name="proof_text" class="form-control mb-2" id="proof_text"></textarea>
                                        <input type="file" name="proof_img" class="form-control mt-2" id="proof_img">
                                    </div>
                                </div>
                                <div class="col-md-4 stop_rem" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label">Write Remarks:</label>
                                        <textarea name="stop_rem" class="form-control" id="stop_rem"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: {{ $bg->action == '5' ? 'flex' : 'none' }}"
                                id="reqExt">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" id="isModReq">Is Modification Required?</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="isModReq"
                                                id="isModReq1" value="1"
                                                {{ $bg->new_stamp_charge_deducted ? 'checked' : '' }}>
                                            <label class="form-check-label" for="isModReq1">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="isModReq"
                                                id="isModReq2" value="0"
                                                {{ $bg->new_stamp_charge_deducted ? 'checked' : '' }}>
                                            <label class="form-check-label" for="isModReq2">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label" for="ext_letter">Request Letter/email from
                                            Client:</label>
                                        <input type="file" name="ext_letter" class="form-control" id="ext_letter"
                                            accept=".pdf,.jpeg,.jpg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                        @if ($bg->ext_letter)
                                            <a href="{{ asset('uploads/accounts/' . $bg->ext_letter) }}"
                                                target="_blank">View</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 table-responsive" id="modreq">
                                    <table class="table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Field</th>
                                                <th>Existing Values</th>
                                                <th class="{{ $bg->new_stamp_charge_deducted ? '' : 'new' }}">New Values
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>Stamp Paper Amount</th>
                                                <td>{{ $bg->stamp_charge_deducted ? format_inr($bg->stamp_charge_deducted) : '' }}
                                                </td>
                                                <td class="{{ $bg->new_stamp_charge_deducted ? '' : 'new' }}">
                                                    <input type="number" name="new_stamp_charge_deducted"
                                                        value="{{ $bg->new_stamp_charge_deducted ? $bg->new_stamp_charge_deducted : $bg->stamp_charge_deducted }}"
                                                        class="form-control" id="new_stamp_charge_deducted">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Beneficiary Name</th>
                                                <td>{{ $bg->bg_bank_name }}</td>
                                                <td class="{{ $bg->new_bg_bank_name ? '' : 'new' }}">
                                                    <input type="text" name="new_bg_bank_name" class="form-control"
                                                        id="new_bg_bank_name"
                                                        value="{{ $bg->new_bg_bank_name ? $bg->new_bg_bank_name : $bg->bg_bank_name }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Amount</th>
                                                <td>{{ $bg->bg_amt ? format_inr($bg->bg_amt) : '' }}</td>
                                                <td class="{{ $bg->new_bg_amt ? '' : 'new' }}">
                                                    <input type="number" name="new_bg_amt" class="form-control"
                                                        id="new_bg_amt"
                                                        value="{{ $bg->new_bg_amt ? $bg->new_bg_amt : $bg->bg_amt }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Expiry Date</th>
                                                <td>{{ $bg->bg_expiry ? date('d-m-Y', strtotime($bg->bg_expiry)) : '' }}
                                                </td>
                                                <td class="{{ $bg->new_bg_expiry ? '' : 'new' }}">
                                                    <input type="date" name="new_bg_expiry" class="form-control"
                                                        id="new_bg_expiry"
                                                        value="{{ $bg->new_bg_expiry ? $bg->new_bg_expiry : $bg->bg_expiry }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Claim Date</th>
                                                <td>{{ $bg->bg_claim ? date('d-m-Y', strtotime($bg->bg_claim)) : '' }}</td>
                                                <td class="{{ $bg->new_bg_claim ? '' : 'new' }}">
                                                    <input type="date" name="new_bg_claim" class="form-control"
                                                        id="new_bg_claim"
                                                        value="{{ $bg->new_bg_claim ? $bg->new_bg_claim : $bg->bg_claim }}">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row" style="display: {{ $bg->action == '6' ? 'flex' : 'none' }}"
                                id="bgcourier">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="docket_no">Docket No.:</label>
                                        <input type="text" name="docket_no" class="form-control" id="docket_no"
                                            value="{{ $bg->docket_no }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="docket_slip">Upload Docket Slip:</label>
                                        <input type="file" name="docket_slip" class="form-control" id="docket_slip"
                                            accept=".pdf,.jpeg,.jpg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                        @if ($bg->docket_slip)
                                            <a href="{{ asset('uploads/accounts/' . $bg->docket_slip) }}"
                                                target="_blank">View</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: {{ $bg->action == '7' ? 'flex' : 'none' }}"
                                id="reqcancel">
                                <div class="col-md-4">
                                    <small>{{ $bg->bg_bank }}</small>
                                    <div class="form-group">
                                        <label class="form-label" for="ext_letter">
                                            Upload a Signed, Stamped Covering Letter from Client
                                        </label>
                                        <input type="file" name="stamp_covering_letter" class="form-control" id="stamp_covering_letter"
                                            accept=".pdf,.jpeg,.jpg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                        @if ($bg->stamp_covering_letter)
                                            <a href="{{ asset('uploads/accounts/' . $bg->stamp_covering_letter) }}"
                                                target="_blank">View</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: {{ $bg->action == '8' ? 'flex' : 'none' }}"
                                id="cancelconfirm">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="cancell_confirm">
                                            Upload the Bank BG cancellation request receiving from the Bank.
                                        </label>
                                        <input type="file" name="cancell_confirm" class="form-control"
                                            id="cancell_confirm"
                                            accept=".pdf,.jpeg,.jpg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                        @if ($bg->cancell_confirm)
                                            <a href="{{ asset('uploads/accounts/' . $bg->cancell_confirm) }}"
                                                target="_blank">View</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: {{ $bg->action == '9' ? 'flex' : 'none' }}"
                                id="bgfdrcancel">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="date">Date:</label>
                                        <input type="date" name="bg_fdr_cancel_date" class="form-control"
                                            id="date" value="{{ $bg->bg_fdr_cancel_date }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="amount">Amount credited:</label>
                                        <input type="number" name="bg_fdr_cancel_amount" class="form-control"
                                            id="amount" value="{{ $bg->bg_fdr_cancel_amount }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="reference_no">Bank reference No:</label>
                                        <input type="text" name="bg_fdr_cancel_ref_no" class="form-control"
                                            id="reference_no" value="{{ $bg->bg_fdr_cancel_ref_no }}">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.new').hide();
            $('input[name="isModReq"]').on('change', function() {
                if (this.value == '1') {
                    $('.new').show();
                } else {
                    $('.new').hide();
                }
            })

            $('#approve_bg').on('change', function() {
                $('#bg_format_imran').toggle(this.value == '2');
            });

            let fp = 1;
            $(document).on('click', '.addPopFollowup', function(e) {
                let html = `
                <div class="col-md-4 form-group">
                    <input type="text" name="fp[${fp}][name]" class="form-control" id="name" placeholder="Name">
                </div>
                <div class="col-md-4 form-group">
                    <input type="number" name="fp[${fp}][phone]" class="form-control" id="phone" placeholder="Phone">
                </div>
                <div class="col-md-4 form-group">
                    <input type="email" name="fp[${fp}][email]" class="form-control" id="email" placeholder="Email">
                </div>
            `;
                $('#popfollowups').append(html);
                fp++;
            });
            // Utility function to handle option changes
            function handleOptionChange(actionSelector, config) {
                $(actionSelector).on('change', function() {
                    const value = $(this).val();
                    config.forEach(({
                        val,
                        selectorsToShow,
                        reqFld
                    }) => {
                        if (value == val) {
                            selectorsToShow.forEach(selector => {
                                $(selector).show();
                                reqFld.forEach(s => $(s).prop('required', true));
                            });
                        } else {
                            selectorsToShow.forEach(selector => {
                                $(selector).hide();
                                reqFld.forEach(s => $(s).prop('required', false));
                            });
                        }
                    });
                });
                $(actionSelector).prop('required', true);
            }

            // Configurations for each action dropdown
            handleOptionChange('#bgaction', [{
                    val: '1',
                    selectorsToShow: ['#bgaccount1'],
                    reqFld: []
                },
                {
                    val: '2',
                    selectorsToShow: ['#bgaccount2'],
                    reqFld: []
                },
                {
                    val: '3',
                    selectorsToShow: ['#bgaccount3'],
                    reqFld: ['#status']
                },
                {
                    val: '4',
                    selectorsToShow: ['#bgfollowup'],
                    reqFld: []
                },
                {
                    val: '5',
                    selectorsToShow: ['#reqExt'],
                    reqFld: []
                },
                {
                    val: '6',
                    selectorsToShow: ['#bgcourier'],
                    reqFld: []
                },
                {
                    val: '7',
                    selectorsToShow: ['#reqcancel'],
                    reqFld: []
                },
                {
                    val: '8',
                    selectorsToShow: ['#cancelconfirm'],
                    reqFld: []
                },
                {
                    val: '9',
                    selectorsToShow: ['#bgfdrcancel'],
                    reqFld: []
                },
            ]);


            // Show/hide '.stop' based on frequency value
            $("select[name='frequency']").on('change', function() {
                if ($(this).val() == '6') {
                    $('.stop').show();
                } else {
                    $('.stop').hide();
                }
            });

            $("select[name='stop_reason']").on('change', function() {
                if ($(this).val() == '2') {
                    $('.stop_proof').show();
                } else {
                    $('.stop_proof').hide();
                }
                if ($(this).val() == '4') {
                    $('.stop_rem').show();
                } else {
                    $('.stop_rem').hide();
                }
            });
        });
        
        document.querySelector("form").addEventListener("submit", function (e) {
            // Loop through all tab contents
            document.querySelectorAll(".tab-pane").forEach(function(tab) {
                // If the tab is hidden (not active), disable its inputs
                if (!tab.classList.contains("active")) {
                    tab.querySelectorAll("input, select, textarea").forEach(function(input) {
                        input.removeAttribute("name");
                    });
                }
            });
        });
    </script>
@endpush
@push('styles')
    <style>
        th,
        td {
            padding: 8px;
        }

        th {
            font-size: 14px
        }
    </style>
@endpush
