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

    $banks = [
        'SBI' => 'State Bank of India',
        'HDFC_0026' => 'HDFC Bank',
        'ICICI' => 'ICICI Bank',
        'YESBANK_2011' => 'Yes Bank 2011',
        'YESBANK_0771' => 'Yes Bank 0771',
        'BGLIMIT_0771' => 'BG Limit',
        'PNB_6011' => 'Punjab National Bank',
    ];

    $purpose = [
        'advance' => 'Advance Payment',
        'deposit' => 'Security Bond/ Deposit',
        'bid' => 'Bid Bond',
        'performance' => 'Performance',
        'financial' => 'Financial',
        'counter' => 'Counter Guarantee',
    ];
    $tid = $emd->tender_id;
    $ins = $emd->instrument_type;
@endphp
@section('page-title', 'Edit ' . $instrumentType[$ins] . ' EMD')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('emds.index') }}" class="btn btn-primary btn-sm">View All EMDs</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="new-user-info">
                            <form method="POST" action="{{ route('emds.update', $emd->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="tender_id">Tender Number:</label>
                                        <input type="hidden" name="tender_id" class="form-control" id="tender_id"
                                            value="{{ $emd->tender_id }}">
                                        <input type="text" name="tender" class="form-control"
                                            value="{{ $tender ? $tender->tender_no : $emd->tender_no }}" required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('tender_id') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="project_name">Project Name:</label>
                                        <input type="text" name="project_name" class="form-control" id="project_name"
                                            value="{{ $emd->project_name }}" required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('project_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="instrument_type">Instrument Type:</label>
                                        <select name="instrument_type" class="form-control" id="instrument_type" required>
                                            <option value="">-- Select --</option>
                                            @foreach ($instrumentType as $key => $value)
                                                <option {{ $emd->instrument_type == $key ? 'selected' : '' }} value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('instrument_type') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="requested_by">Requested By:</label>
                                        <input type="text" name="requested_by" class="form-control" id="requested_by"
                                            value="{{ $emd->requested_by }}" required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('requested_by') }}</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" id="conditional_form">
                                        <!-- Demand Draft Fields -->
                                        @if ($ins == '1')
                                            <div class="row" id="demand_draft">
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="dd_favour">DD in Favour of:</label>
                                                    <input type="text" name="dd_favour" id="dd_favour"
                                                        class="form-control" required value="{{ $allData->dd_favour }}">
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('dd_favour') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="dd_amt">DD Amount:</label>
                                                    <input type="number" name="dd_amt" id="dd_amt" min="0"
                                                        step="0.01" class="form-control" required
                                                        value="{{ $allData->dd_amt }}">
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('dd_amt') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="dd_payable">Payable At:</label>
                                                    <input type="text" name="dd_payable" id="dd_payable"
                                                        class="form-control" required value="{{ $allData->dd_payable }}">
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('dd_payable') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group {{ $tid == '00' ? 'd-none' : '' }}">
                                                    <label class="form-label" for="dd_needs">DD deliver by:</label>
                                                    <select name="dd_needs" id="dd_needs" class="form-control"
                                                        {{ $tid == '00' ? '' : 'required' }}>
                                                        <option value="">-- Choose --</option>
                                                        <option {{ $allData->dd_needs == 'due' ? 'selected' : '' }}
                                                            value="due">Tender Due Date</option>
                                                        <option {{ $allData->dd_needs == '24' ? 'selected' : '' }}
                                                            value="24">24 Hours</option>
                                                        <option {{ $allData->dd_needs == '36' ? 'selected' : '' }}
                                                            value="36">36 Hours</option>
                                                        <option {{ $allData->dd_needs == '48' ? 'selected' : '' }}
                                                            value="48">48 Hours</option>
                                                    </select>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('dd_needs') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="dd_purpose">Purpose of the DD:</label>
                                                    <select name="dd_purpose" id="dd_purpose" class="form-control"
                                                        required>
                                                        <option value="">-- Choose --</option>
                                                        <option {{ $allData->dd_purpose == 'EMD' ? 'selected' : '' }}
                                                            value="EMD">
                                                            EMD
                                                        </option>
                                                        <option
                                                            {{ $allData->dd_purpose == 'Tender Fees' ? 'selected' : '' }}
                                                            value="Tender Fees">
                                                            Tender Fees
                                                        </option>
                                                        <option
                                                            {{ $allData->dd_purpose == 'Security Deposit' ? 'selected' : '' }}
                                                            value="Security Deposit">
                                                            Security Deposit
                                                        </option>
                                                        <option
                                                            {{ $allData->dd_purpose == 'Other Payment' ? 'selected' : '' }}
                                                            value="Other Payment">
                                                            Other Payment
                                                        </option>
                                                        <option
                                                            {{ $allData->dd_purpose == 'Other Security' ? 'selected' : '' }}
                                                            value="Other Security">
                                                            Other Security
                                                        </option>
                                                    </select>
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('dd_purpose') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group {{ $tid == '00' ? 'd-none' : '' }}">
                                                    <label class="form-label" for="courier_add">Courier Address:</label>
                                                    <input type="text" name="courier_add" id="courier_add"
                                                        class="form-control" {{ $tid == '00' ? '' : 'required' }}
                                                        value="{{ $allData->courier_add }}">
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('courier_add') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group {{ $tid == '00' ? 'd-none' : '' }}">
                                                    <label class="form-label" for="courier_deadline">Time required for
                                                        courier to reach destination:</label>
                                                    <div class="input-group">
                                                        <input type="number" name="courier_deadline"
                                                            id="courier_deadline" class="form-control"
                                                            {{ $tid == '00' ? '' : 'required' }}
                                                            value="{{ $allData->courier_deadline }}"
                                                            onkeypress="return isNumberKey(event)" min="1"
                                                            step="1">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">Hours</span>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('courier_deadline') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group {{ $tid == '00' ? '' : 'd-none' }}">
                                                    <label class="form-label" for="dd_date">DD Date:</label>
                                                    <input type="date" name="dd_date" id="dd_date"
                                                        class="form-control" {{ $tid == '00' ? 'required' : '' }}
                                                        value="{{ $allData->dd_date }}">
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('dd_date') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group {{ $tid == '00' ? '' : 'd-none' }}">
                                                    <label class="form-label" for="remarks">Remarks (if any):</label>
                                                    <input type="text" name="remarks" id="remarks"
                                                        class="form-control" {{ $tid == '00' ? 'required' : '' }}
                                                        value="{{ $allData->remarks }}">
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('remarks') }}</span>
                                                    </small>
                                                </div>
                                            </div>
                                            <!-- FDR Fields -->
                                        @elseif($ins == '2')
                                            <div class="row" id="fdr">
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="fdr_purpose">Purpose of FDR:</label>
                                                    <select name="fdr_purpose" id="fdr_purpose" class="form-control"
                                                        required>
                                                        <option value="">-- Choose --</option>
                                                        <option {{ $allData->fdr_purpose == 'bg' ? 'selected' : '' }}
                                                            value="bg">Bank Guarantee</option>
                                                        <option value="security-deposit"
                                                            {{ $allData->fdr_purpose == 'security-deposit' ? 'selected' : '' }}>
                                                            Security Deposit
                                                        </option>
                                                        <option {{ $allData->fdr_purpose == 'others' ? 'selected' : '' }}
                                                            value="others">Others</option>
                                                    </select>
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('fdr_purpose') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="fdr_favour">FDR in Favour of:</label>
                                                    <input type="text" name="fdr_favour" id="fdr_favour"
                                                        class="form-control" value="{{ $allData->fdr_favour }}" required>
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('fdr_favour') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="fdr_amt">FDR Amount:</label>
                                                    <input type="number" name="fdr_amt" id="fdr_amt" min="0"
                                                        step="0.01" class="form-control"
                                                        value="{{ $allData->fdr_amt }}" required>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('fdr_amt') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="fdr_expiry">FDR Expiry Date:</label>
                                                    <input type="date" name="fdr_expiry" id="fdr_expiry"
                                                        class="form-control" value="{{ $allData->fdr_expiry }}" required>
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('fdr_expiry') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="fdr_needs">FDR Needed in:</label>
                                                    <select name="fdr_needs" id="fdr_needs" class="form-control"
                                                        required>
                                                        <option value="">Select</option>
                                                        <option value="24"
                                                            {{ $allData->fdr_needs == '24' ? 'selected' : '' }}>24 Hours
                                                        </option>
                                                        <option value="48"
                                                            {{ $allData->fdr_needs == '48' ? 'selected' : '' }}>48 Hours
                                                        </option>
                                                        <option value="72"
                                                            {{ $allData->fdr_needs == '72' ? 'selected' : '' }}>72 Hours
                                                        </option>
                                                        <option value="96"
                                                            {{ $allData->fdr_needs == '96' ? 'selected' : '' }}>96 Hours
                                                        </option>
                                                    </select>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('fdr_needs') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="fdr_bank_details">Party Bank Details
                                                        (In case of Joint FDR)</label>
                                                    <div class="input-group mb-1">
                                                        <input type="text" name="fdr_bank_name" id="fdr_bank_name"
                                                            class="form-control" placeholder="Bank Account Name"
                                                            value="{{ $allData->fdr_bank_name }}" required>
                                                    </div>
                                                    <div class="input-group mb-1">
                                                        <input type="number" name="fdr_bank_acc" id="fdr_bank_acc"
                                                            min="0" class="form-control"
                                                            placeholder="Account Number"
                                                            value="{{ $allData->fdr_bank_acc }}" required>
                                                    </div>
                                                    <div class="input-group mb-1">
                                                        <input type="text" name="fdr_bank_ifsc" id="fdr_bank_ifsc"
                                                            class="form-control" placeholder="IFSC"
                                                            value="{{ $allData->fdr_bank_ifsc }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Cheque Fields -->
                                        @elseif($ins == '3')
                                            <div class="row" id="cheque">
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="cheque_favour">Cheque in Favour
                                                        of</label>
                                                    <input type="text" name="cheque_favour" id="cheque_favour"
                                                        class="form-control" required
                                                        value="{{ $allData->cheque_favour }}">
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('cheque_favour') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="cheque_date">Cheque Date</label>
                                                    <input type="date" name="cheque_date" id="cheque_date"
                                                        class="form-control" value="{{ $allData->cheque_date }}">
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('cheque_date') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="cheque_amt">Cheque Amount:</label>
                                                    <input type="number" name="cheque_amt" id="cheque_amt"
                                                        min="0" step="0.01" class="form-control"
                                                        value="{{ $allData->cheque_amt }}">
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('cheque_amt') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group {{ $tid == '00' ? 'd-none' : '' }}">
                                                    <label class="form-label" for="cheque_needs">Cheque Needed
                                                        in</label>
                                                    <select name="cheque_needs" id="cheque_needs" class="form-control">
                                                        <option value="">Select</option>
                                                        <option {{ $allData->cheque_needs == '3' ? 'selected' : '' }}
                                                            value="3">3 Hours</option>
                                                        <option {{ $allData->cheque_needs == '6' ? 'selected' : '' }}
                                                            value="6">6 Hours</option>
                                                        <option {{ $allData->cheque_needs == '12' ? 'selected' : '' }}
                                                            value="12">12 Hours</option>
                                                        <option {{ $allData->cheque_needs == '24' ? 'selected' : '' }}
                                                            value="24">24 Hours</option>
                                                    </select>
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('cheque_needs') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for=" ">
                                                        Purpose of the Cheque</label>
                                                    <select name="cheque_reason" id="cheque_reason" class="form-control">
                                                        <option value="">Select</option>
                                                        <option
                                                            {{ $allData->cheque_reason == 'Payable' ? 'selected' : '' }}
                                                            value="Payable">Payable</option>
                                                        <option
                                                            {{ $allData->cheque_reason == 'Security' ? 'selected' : '' }}
                                                            value="Security">Security</option>
                                                        <option {{ $allData->cheque_reason == 'DD' ? 'selected' : '' }}
                                                            value="DD">DD</option>
                                                        <option {{ $allData->cheque_reason == 'FDR' ? 'selected' : '' }}
                                                            value="FDR">FDR</option>
                                                    </select>
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('cheque_reason') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="cheque_bank">
                                                        Account to be debited from
                                                    </label>
                                                    <select name="cheque_bank" id="cheque_bank" class="form-control"
                                                        required>
                                                        <option value="">Choose Bank Name</option>
                                                        @foreach ($banks as $key => $bank)
                                                            <option value="{{ $key }}"
                                                                {{ $allData->cheque_bank == $key ? 'selected' : '' }}>
                                                                {{ $bank }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- BG Fields -->
                                        @elseif($ins == '4')
                                            <div class="row" id="bg">
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_needs">BG needed in</label>
                                                    <select name="bg_needs" id="bg_needs" class="form-control" required>
                                                        <option value="">Select</option>
                                                        <option {{ $allData->bg_needs == '72' ? 'selected' : '' }}
                                                            value="72">72 Hours</option>
                                                        <option {{ $allData->bg_needs == '96' ? 'selected' : '' }}
                                                            value="96">96 Hours</option>
                                                        <option {{ $allData->bg_needs == '120' ? 'selected' : '' }}
                                                            value="120">120 Hours</option>
                                                    </select>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('bg_needs') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_purpose">Purpose of the BG</label>
                                                    <select name="bg_purpose" id="bg_purpose" class="form-control"
                                                        required>
                                                        <option value="">Select</option>
                                                        @foreach ($purpose as $key => $value)
                                                            <option {{ $allData->bg_purpose == $key ? 'selected' : '' }}
                                                                value="{{ $key }}">
                                                                {{ $value }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('bg_purpose') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_favour">BG in Favour of</label>
                                                    <input type="text" name="bg_favour" id="bg_favour"
                                                        class="form-control" value="{{ $allData->bg_favour }}" required>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('bg_favour') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_address">BG Address</label>
                                                    <textarea name="bg_address" id="bg_address" class="form-control" required>{{ $allData->bg_address }}</textarea>
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('bg_address') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_expiry">BG Expiry Date</label>
                                                    <input type="date" name="bg_expiry" id="bg_expiry"
                                                        class="form-control" value="{{ $allData->bg_expiry }}" required>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('bg_expiry') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_claim">BG Claim Period:</label>
                                                    <input type="date" name="bg_claim" id="bg_claim"
                                                        class="form-control" value="{{ $allData->bg_claim }}" required>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('bg_claim') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_amt">BG Amount:</label>
                                                    <input type="number" name="bg_amt" id="bg_amt" min="0"
                                                        step="0.01" class="form-control"
                                                        value="{{ $allData->bg_amt }}" required>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('bg_amt') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_stamp">BG Stamp Paper Value</label>
                                                    <input type="number" name="bg_stamp" id="bg_stamp" min="0"
                                                        step="0.01" class="form-control"
                                                        value="{{ $allData->bg_stamp }}" required>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('bg_stamp') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_format_te">Upload BG Format
                                                        TE</label>
                                                    <input type="file" name="bg_format_te" id="bg_format_te"
                                                        class="form-control" multiple required>
                                                    <small class="text-muted">
                                                        Upload max 5 files.
                                                        <span
                                                            class="text-danger">{{ $errors->first('bg_format_te') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_po">PO/Tender/Request letter
                                                        Upload</label>
                                                    <input type="file" name="bg_po" id="bg_po"
                                                        accept=".pdf,.doc,.docx,image/*" class="form-control"
                                                        value="{{ $allData->bg_po }}" required>
                                                    <small class="text-muted">
                                                        Upload only 1 file.
                                                        <span class="text-danger">{{ $errors->first('bg_po') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_client_email">Client
                                                        Emails</label>
                                                    <div class="input-group mb-1">
                                                        <input type="email" name="bg_client_user" id="bg_client_user"
                                                            class="form-control" placeholder="User Dept. Email"
                                                            value="{{ $allData->bg_client_user }}" required>
                                                    </div>
                                                    <div class="input-group mb-1">
                                                        <input type="email" name="bg_client_cp" id="bg_client_cp"
                                                            class="form-control" placeholder="C&P Dept. Email"
                                                            value="{{ $allData->bg_client_cp }}" required>
                                                    </div>
                                                    <div class="input-group mb-1">
                                                        <input type="email" name="bg_client_fin" id="bg_client_fin"
                                                            class="form-control" placeholder="Finance Dept. Email"
                                                            value="{{ $allData->bg_client_fin }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_bank_details">Client Bank
                                                        Details</label>
                                                    <div class="input-group mb-1">
                                                        <input type="text" name="bg_bank_name" id="bg_bank_name"
                                                            class="form-control" placeholder="Bank Account Name"
                                                            value="{{ $allData->bg_bank_name }}" required>
                                                    </div>
                                                    <div class="input-group mb-1">
                                                        <input type="number" name="bg_bank_acc" id="bg_bank_acc"
                                                            min="0" class="form-control"
                                                            placeholder="Account Number"
                                                            value="{{ $allData->bg_bank_acc }}">
                                                    </div>
                                                    <div class="input-group mb-1">
                                                        <input type="text" name="bg_bank_ifsc" id="bg_bank_ifsc"
                                                            class="form-control" placeholder="IFSC"
                                                            value="{{ $allData->bg_bank_ifsc }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_courier_addr">BG Courier
                                                        Address</label>
                                                    <input type="text" name="bg_courier_addr" id="bg_courier_addr"
                                                        class="form-control" value="{{ $allData->bg_courier_addr }}"
                                                        required>
                                                    <small class="text-muted">
                                                        Address where BG will be sent through courier.
                                                        <span
                                                            class="text-danger">{{ $errors->first('bg_courier_addr') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="courier_deadline">Courier Delivery
                                                        Time {{ $allData->courier_deadline }}</label>
                                                    <div class="input-group">
                                                        <select name="courier_deadline" id="courier_deadline" class="form-control" required>
                                                            <option value="">Select Days</option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}"
                                                                    {{ $allData->courier_deadline == $i ? 'selected' : '' }}>
                                                                    {{ $i }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                        <span class="input-group-text">days</span>
                                                    </div>
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('courier_deadline') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bg_bank">Bank</label>
                                                    <select name="bg_bank" id="bg_bank" class="form-control">
                                                        <option value="">Select Bank</option>
                                                        @foreach ($banks as $key => $bank)
                                                            <option value="{{ $key }}"
                                                                {{ $allData->bg_bank == $key ? 'selected' : '' }}>
                                                                {{ $bank }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('bg_bank') }}</span>
                                                    </small>
                                                </div>
                                            </div>
                                            <!-- Bank Transfer Fields -->
                                        @elseif($ins == '5')
                                            <div class="row" id="bank_transfer">
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="purpose">Purpose</label>
                                                    <input type="text" name="purpose" id="purpose"
                                                        class="form-control" value="{{ $allData->purpose }}">
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('purpose') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bt_acc_name">Account Name</label>
                                                    <input type="text" name="bt_acc_name" id="bt_acc_name"
                                                        class="form-control" value="{{ $allData->bt_acc_name }}">
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('bt_acc_name') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bt_acc">Account Number</label>
                                                    <input type="number" name="bt_acc" id="bt_acc"
                                                        class="form-control" value="{{ $allData->bt_acc }}">
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('bt_acc') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bt_ifsc">IFSC</label>
                                                    <input type="text" name="bt_ifsc" id="bt_ifsc"
                                                        class="form-control" value="{{ $allData->bt_ifsc }}">
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('bt_ifsc') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bt_amount">Amount</label>
                                                    <input type="number" step="any" name="bt_amount" id="bt_amount"
                                                        class="form-control" value="{{ $allData->bt_amount }}">
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('bt_amount') }}</span>
                                                    </small>
                                                </div>
                                            </div>
                                            <!-- Pay on Portal Fields -->
                                        @elseif($ins == '6')
                                            <div class="row" id="pay_on_portal">
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="bt_acc">Purpose</label>
                                                    <select name="purpose" id="purpose" class="form-control">
                                                        <option value="">-- Choose --</option>
                                                        <option {{ $allData->purpose == 'EMD' ? 'selected' : '' }}
                                                            value="EMD">EMD</option>
                                                        <option {{ $allData->purpose == 'Tender Fees' ? 'selected' : '' }}
                                                            value="Tender Fees">Tender Fees</option>
                                                        <option {{ $allData->purpose == 'Others' ? 'selected' : '' }}
                                                            value="Others">Others</option>
                                                    </select>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('purpose') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="portal">Name of
                                                        Portal/Website</label>
                                                    <input type="text" name="portal" id="portal"
                                                        class="form-control" value="{{ $allData->portal }}">
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('portal') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="is_netbanking">Net Banking
                                                        Available</label>
                                                    <select name="is_netbanking" id="is_netbanking" class="form-control">
                                                        <option value="">-- Choose --</option>
                                                        <option {{ $allData->is_netbanking == 'yes' ? 'selected' : '' }}
                                                            value="yes">Yes</option>
                                                        <option {{ $allData->is_netbanking == 'no' ? 'selected' : '' }}
                                                            value="no">No</option>
                                                    </select>
                                                    <small class="text-muted">
                                                        <span
                                                            class="text-danger">{{ $errors->first('is_netbanking') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="is_debit">Yes Bank Debit Card
                                                        Allowed</label>
                                                    <select name="is_debit" id="is_debit" class="form-control">
                                                        <option value="">-- Choose --</option>
                                                        <option {{ $allData->is_debit == 'yes' ? 'selected' : '' }}
                                                            value="yes">Yes</option>
                                                        <option {{ $allData->is_debit == 'no' ? 'selected' : '' }}
                                                            value="no">No</option>
                                                    </select>
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('is_debit') }}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label class="form-label" for="amount">Amount</label>
                                                    <input type="number" name="amount" id="amount"
                                                        class="form-control" value="{{ $allData->amount }}">
                                                    <small class="text-muted">
                                                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                                                    </small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Update EMD
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
