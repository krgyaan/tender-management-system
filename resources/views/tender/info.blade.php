@extends('layouts.app')
@section('page-title', 'Fill Tender Full Info')
@php
    $pqr = App\Models\Pqr::all();
    $finance = App\Models\Finance::all();
@endphp
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('tender.index') }}" class="btn btn-primary btn-xs">View All Tenders</a>
                    <span class="badge bg-success">Completed {{ $tenderInfo->calculateTenderCompletion() }}%</span>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="new-user-info">
                            <div class="bd-example">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h4 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                aria-expanded="false" aria-controls="collapseTwo">
                                                See Tender Details
                                            </button>
                                        </h4>
                                        <div id="collapseTwo" class="accordion-collapse collapse"
                                            aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div
                                                    class="table-responsive d-flex align-items-center justify-content-center w-100">
                                                    <table class="table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <th>Tender No</th>
                                                                <td>{{ $tenderInfo->tender_no }}</td>
                                                                <th>Tender Name</th>
                                                                <td>{{ $tenderInfo->tender_name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Organization</th>
                                                                <td>
                                                                    {{ $tenderInfo->organizations ? $tenderInfo->organizations->name : '' }}
                                                                </td>
                                                                <th>Tender Value (GST Inclusive)</th>
                                                                <td>{{ $tenderInfo->gst_values }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Tender Fee</th>
                                                                <td>{{ $tenderInfo->tender_fees }}</td>
                                                                <th>EMD</th>
                                                                <td>{{ $tenderInfo->emd }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Team Member</th>
                                                                <td>
                                                                    {{ $tenderInfo->users ? $tenderInfo->users->name . ' (' . $tenderInfo->users->designation . ')' : '' }}
                                                                </td>
                                                                <th>Due Date</th>
                                                                <td>
                                                                    {{ date('d-m-Y', strtotime($tenderInfo->due_date)) }}<br>
                                                                    {{ date('h:i A', strtotime($tenderInfo->due_time)) }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Status</th>
                                                                <td>
                                                                    {{ $tenderInfo->statuses->name }}
                                                                </td>
                                                                <th>Location</th>
                                                                <td>
                                                                    {{ $tenderInfo->locations ? $tenderInfo->locations->address : 'NA' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Website</th>
                                                                <td>
                                                                    @if ($tenderInfo->websites)
                                                                        <a href="http://{{ $tenderInfo->websites->url }}"
                                                                            target="_blank" rel="noopener noreferrer">
                                                                            {{ $tenderInfo->websites->name }}
                                                                            <i class="fa fa-external-link"
                                                                                aria-hidden="true"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                                <th>Items</th>
                                                                <td>
                                                                    {{ $tenderInfo->itemName ? $tenderInfo->itemName->name : '' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Documents</th>
                                                                <td>
                                                                    <ul>
                                                                        @foreach ($tenderInfo->docs as $doc)
                                                                            <li>
                                                                                <a href="/uploads/docs/{{ $doc->doc_path }}"
                                                                                    target="_blank"
                                                                                    class="text-decoration-none">
                                                                                    Document - {{ $loop->iteration }}
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </td>
                                                                <th>Remarks</th>
                                                                <td>{{ $tenderInfo->remarks }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('tender.info.update', $tenderInfo->id) }}"
                                class="pt-4 need-validation" novalidate enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row pt-2">
                                    <div class="form-group col-md-4">
                                        <label for="is_rejectable">Is This Tender Acceptable:</label>
                                        <select class="form-control" name="is_rejectable" id="is_rejectable"
                                            data-is-rejectable="{{ $tender->is_rejectable ?? '' }}" required>
                                            <option value="">Select</option>
                                            <option value="1"
                                                {{ $tender && $tender->is_rejectable == '1' ? 'selected' : '' }}>
                                                No
                                            </option>
                                            <option value="0"
                                                {{ $tender && $tender->is_rejectable == '0' ? 'selected' : '' }}>
                                                Yes
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 {{ $tender && $tender->is_rejectable == 1 ? '' : 'd-none' }}"
                                        id="rejectable">
                                        <label for="reject_reason">Reason of Rejection:</label>
                                        <select class="form-control" name="reject_reason" id="reject_reason">
                                            <option value="">Select Reason</option>
                                            @foreach ($reason as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ $tender ? ($tender->reject_reason == $key ? 'selected' : '') : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 {{ $tender && $tender->is_rejectable == 1 ? '' : 'd-none' }}"
                                        id="reject_remarks">
                                        <label for="reject_remarks">Remarks:</label>
                                        <textarea name="reject_remarks" class="form-control" id="reject_remarks">{{ $tender ? $tender->reject_remarks : '' }}</textarea>
                                    </div>
                                </div>

                                <div class="row" id="not_rejectable">
                                    <div class="form-group col-md-4">
                                        <label for="tender_fee">Tender Fees (mode of payment)</label>
                                        <select name="tender_fee[]" class="form-control" id="tender_fee" multiple required>
                                            <option value="">Select</option>
                                            @foreach ($tenderFees as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ old('tender_fee') && in_array($key, old('tender_fee')) ? 'selected' : ($tender && in_array($key, explode(',', $tender->tender_fees)) ? 'selected' : '') }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_fee') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="emd_req">EMD Required</label>
                                        <select name="emd_req" class="form-control" id="emd_req" required>
                                            <option value="">Select</option>
                                            @foreach ($emdReq as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ old('emd_req') == $key || ($tender && $tender->emd_req == $key) ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('emd_req') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="emd_opt">EMD Required (mode of payment)</label>
                                        <select name="emd_opt[]" class="form-control" id="emd_opt" multiple required>
                                            <option value="">Select</option>
                                            @foreach ($emdOpt as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ old('emd_opt') == $key || ($tender && in_array($key, explode(',', $tender->emd_opt))) ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('emd_opt') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="rev_auction">Reverse Auction Applicable</label>
                                        <select name="rev_auction" class="form-control" id="rev_auction" required>
                                            <option value="">Select</option>
                                            @foreach ($revAuction as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ old('rev_auction') == $key || ($tender && $tender->rev_auction == $key) ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('rev_auction') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="pt_supply">Payment Terms on Supply</label>
                                        <select name="pt_supply" class="form-control" id="pt_supply" required>
                                            <option value="">Select</option>
                                            @for ($i = 0; $i <= 100; $i += 5)
                                                <option value="{{ $i }}"
                                                    {{ old('pt_supply') == $i || ($tender && $tender->pt_supply == $i) ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('pt_supply') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="pt_ic">Payment Terms on I&C</label>
                                        <select name="pt_ic" class="form-control" id="pt_ic" required>
                                            <option value="">Select</option>
                                            @for ($i = 0; $i <= 100; $i += 5)
                                                <option value="{{ $i }}"
                                                    {{ old('pt_ic') == $i || ($tender && $tender->pt_ic == $i) ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('pt_ic') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="pbg">Performance Bank Guarantee %age</label>
                                        <div class="input-group mb-3">
                                            <select name="pbg" class="form-select" id="pbg" required>
                                                <option value="">Select</option>
                                                @for ($i = 0; $i < 101; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ old('pbg') == $i || ($tender && $tender->pbg == $i) ? 'selected' : '' }}>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <span class="btn btn-outline-secondary" id="basic-addon2">%</span>
                                        </div>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('pbg') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="pbg_duration">PBG Duration</label>
                                        <div class="input-group mb-3">
                                            <select name="pbg_duration" class="form-select" id="pbg_duration" required>
                                                <option value="">Select</option>
                                                @for ($i = 0; $i < 121; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ old('pbg_duration') == $i || ($tender && (int) $tender->pbg_duration == $i) ? 'selected' : '' }}>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <span class="btn btn-outline-secondary" id="basic-addon2">Month</span>
                                        </div>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('pbg_duration') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="bid_valid">Bid Validity</label>
                                        <div class="input-group mb-3">
                                            <select name="bid_valid" class="form-select" id="bid_valid" required>
                                                <option value="">Select</option>
                                                @for ($day = 1; $day <= 365; $day++)
                                                    <option value="{{ $day }}"
                                                        {{ old('bid_valid') == $day || ($tender && $tender->bid_valid == $day) ? 'selected' : '' }}>
                                                        {{ $day }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <span class="btn btn-outline-secondary" id="basic-addon2">Days</span>
                                        </div>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('bid_valid') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="comm_eval">Commercial Evaluation</label>
                                        <select name="comm_eval" class="form-select" id="comm_eval">
                                            <option value="">Select</option>
                                            @foreach ($commercial as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ old('comm_eval') == $key || ($tender && $tender->comm_eval == $key) ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('comm_eval') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="maf_req">MAF Required</label>
                                        <select name="maf_req" class="form-select" id="maf_req" required>
                                            <option value="">Select</option>
                                            @foreach ($maf as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ old('maf_req') == $key || ($tender && $tender->maf_req == $key) ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('maf_req') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="supply">Delivery Time for Supply </label>
                                        <div class="input-group">
                                            <input type="number" name="supply" class="form-control" id="supply"
                                                value="{{ $tender && $tender->supply ? $tender->supply : old('supply', 1) }}">
                                            <span class="btn btn-outline-secondary" id="basic-addon2">Days</span>
                                        </div>
                                        <i class="mb-3">overall (in case timeline not given)</i>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('supply') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="installation">Delivery Time for Installation</label>
                                        <div class="input-group mb-3">
                                            <input type="number" name="installation" class="form-control"
                                                id="installation"
                                                value="{{ $tender && $tender->installation ? $tender->installation : old('installation', 1) }}">
                                            <span class="btn btn-outline-secondary" id="basic-addon2">Days</span>
                                        </div>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('installation') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="ldperweek">LD/PRS %age</label>
                                        <div class="input-group mb-3">
                                            <select name="ldperweek" class="form-control" id="ldperweek">
                                                <option value="">Select</option>
                                                @for ($i = 0; $i <= 50; $i++)
                                                    <option value="{{ $i * 0.1 }}"
                                                        {{ $tender && $tender->ldperweek == $i * 0.1 ? 'selected' : '' }}>
                                                        {{ $i * 0.1 }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <span class="btn btn-outline-secondary" id="basic-addon2">Per Week</span>
                                        </div>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('ldperweek') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="maxld">Maximum LD%</label>
                                        <select name="maxld" class="form-control" id="maxld">
                                            <option value="">Select</option>
                                            @for ($i = 0; $i <= 20; $i++)
                                                <option value="{{ $i }}"
                                                    {{ $tender && $tender->maxld == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('maxld') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="phyDocs">Physical Documents submission required</label>
                                            <select name="phyDocs" class="form-select" id="phyDocs">
                                                <option value="">Select</option>
                                                <option value="Yes"
                                                    {{ $tender && $tender->phyDocs == 'Yes' ? 'selected' : '' }}>
                                                    Yes
                                                </option>
                                                <option value="No"
                                                    {{ $tender && $tender->phyDocs == 'No' ? 'selected' : '' }}>
                                                    No
                                                </option>
                                            </select>
                                            <small>
                                                <span class="text-danger">{{ $errors->first('phyDocs') }}</span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 {{ !isset($tender->phyDocs) || $tender->phyDocs == 'No' ? 'd-none' : '' }}"
                                        id="phyDocsDate">
                                        <div class="form-group">
                                            <label for="phyDocs">Physical Documents Submission deadline</label>
                                            <div class="input-group mb-3">
                                                <!-- Date Input -->
                                                <input type="date" name="dead_date" class="form-control"
                                                    id="dead_date"
                                                    value="{{ isset($tender->dead_date) ? date('Y-m-d', strtotime($tender->dead_date)) : '' }}">
                                                <!-- Time Input -->
                                                <input type="time" name="dead_time" class="form-control"
                                                    id="dead_time"
                                                    value="{{ isset($tender->dead_time) ? date('H:i', strtotime($tender->dead_time)) : '' }}">
                                            </div>

                                            <small>
                                                <span class="text-danger">{{ $errors->first('phyDocs') }}</span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="techEligible">Technical Eligibility Criterion Age</label>
                                            <div class="input-group mb-3">
                                                <input type="text" name="tech_eligible" id="techEligible"
                                                    class="form-control"
                                                    value="{{ $tender && $tender->tech_eligible ? $tender->tech_eligible : old('tech_eligible', 1) }}">
                                                <span class="btn btn-outline-secondary" id="basic-addon2">Year</span>
                                            </div>
                                            <small>
                                                <span class="text-danger">{{ $errors->first('tech_eligible') }}</span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <p class="pb-1">Technical Eligibility Criterion Value</p>
                                            <div class="table-responsive">
                                                <table class="table-bordered border">
                                                    <thead>
                                                        <tr>
                                                            <th>1 Order of</th>
                                                            <th>2 Order of</th>
                                                            <th>3 Order of</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="number" name="tecv[order1]"
                                                                    placeholder="Value" id="techEligibleVal1"
                                                                    class="form-control"
                                                                    value="{{ $tender && $tender->order1 ? $tender->order1 : old('order1', 1) }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="tecv[order2]"
                                                                    placeholder="Value" id="techEligibleVal2"
                                                                    class="form-control"
                                                                    value="{{ $tender && $tender->order2 ? $tender->order2 : old('order2', 1) }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="tecv[order3]"
                                                                    placeholder="Value" id="techEligibleVal3"
                                                                    class="form-control"
                                                                    value="{{ $tender && $tender->order3 ? $tender->order3 : old('order3', 1) }}">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <p class="pb-1">
                                                Work Orders to be submitted to meet technical eligibility criteria
                                            </p>
                                            <div class="table-responsive">
                                                <table class="table-bordered w-100">
                                                    <tr class="text-end bg-transparent">
                                                        <td colspan="3" class="p-0">
                                                            <button type="button" class="btn btn-xs btn-secondary"
                                                                id="addWorkOrder">Add</button>
                                                        </td>
                                                    </tr>
                                                    <tbody id="workOrderTable">
                                                        @if ($tender && $tender->workOrder)
                                                            @foreach ($tender->workOrder as $workorder)
                                                                <tr>
                                                                    <td colspan="2">
                                                                        <select name="wo[{{ $loop->index }}][wo_name]"
                                                                            class="form-select" id="workOrder">
                                                                            <option value="">Select Docs</option>
                                                                            @foreach ($pqr as $it)
                                                                                <option value="{{ $it->id }}"
                                                                                    {{ $workorder->wo_name == $it->id ? 'selected' : '' }}>
                                                                                    {{ $it->project_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                            <small>
                                                <span class="text-danger">{{ $errors->first('documents') }}</span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table-bordered">
                                                <p class="text-center">Commercial Eligibility Criteria</p>
                                                <tbody id="commercialEligible">
                                                    <tr>
                                                        <th>Average Annual<br> Turnover</th>
                                                        <td>
                                                            <div class="form-group">
                                                                <select name="aat" class="form-select"
                                                                    id="aat">
                                                                    <option value="">Choose</option>
                                                                    <option
                                                                        {{ $tender && $tender->aat == 'Not Applicable' ? 'selected' : '' }}
                                                                        value="Not Applicable">Not Applicable</option>
                                                                    <option
                                                                        {{ $tender && $tender->aat == 'amt' ? 'selected' : '' }}
                                                                        value="amt">Amount</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="number" name="aat_amt" id="aat_amt"
                                                                    class="form-control" placeholder="Enter Amount"
                                                                    {{ $tender && $tender->aat == 'amt' ? '' : 'readonly' }}
                                                                    value="{{ $tender && $tender->aat_amt }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Working<br> Capital</th>
                                                        <td>
                                                            <div class="form-group">
                                                                <select name="wc" class="form-select"
                                                                    id="wc">
                                                                    <option value="">Choose</option>
                                                                    <option
                                                                        {{ $tender && $tender->wc == 'Not Applicable' ? 'selected' : '' }}
                                                                        value="Not Applicable">Not Applicable</option>
                                                                    <option
                                                                        {{ $tender && $tender->wc == 'Positive' ? 'selected' : '' }}
                                                                        value="Positive">Positive</option>
                                                                    <option
                                                                        {{ $tender && $tender->wc == 'amt' ? 'selected' : '' }}
                                                                        value="amt">Amount</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="number" name="wc_amt" id="wc_amt"
                                                                    class="form-control" placeholder="Enter Amount"
                                                                    {{ $tender && $tender->wc == 'amt' ? '' : 'readonly' }}
                                                                    value="{{ $tender && $tender->wc_amt }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Solvency<br> Certificate</th>
                                                        <td>
                                                            <div class="form-group">
                                                                <select name="sc" id="sc"
                                                                    class="form-select">
                                                                    <option value="">Choose</option>
                                                                    <option
                                                                        {{ $tender && $tender->sc == 'Not Applicable' ? 'selected' : '' }}
                                                                        value="Not Applicable">Not Applicable</option>
                                                                    <option
                                                                        {{ $tender && $tender->sc == 'amt' ? 'selected' : '' }}
                                                                        value="amt">Amount</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="number" name="sc_amt" id="sc_amt"
                                                                    class="form-control" placeholder="Enter Amount"
                                                                    {{ $tender && $tender->sc == 'amt' ? '' : 'readonly' }}
                                                                    value="{{ $tender && $tender->sc_amt }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Net Worth</th>
                                                        <td>
                                                            <div class="form-group">
                                                                <select name="nw" class="form-select"
                                                                    id="nw">
                                                                    <option value="">Choose</option>
                                                                    <option
                                                                        {{ $tender && $tender->nw == 'Not Applicable' ? 'selected' : '' }}
                                                                        value="Not Applicable">Not Applicable</option>
                                                                    <option
                                                                        {{ $tender && $tender->nw == 'Positive' ? 'selected' : '' }}
                                                                        value="Positive">Positive</option>
                                                                    <option
                                                                        {{ $tender && $tender->nw == 'amt' ? 'selected' : '' }}
                                                                        value="amt">Amount</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="number" name="nw_amt" id="nw_amt"
                                                                    class="form-control" placeholder="Enter Amount"
                                                                    {{ $tender && $tender->nw == 'amt' ? '' : 'readonly' }}
                                                                    value="{{ $tender && $tender->nw_amt }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <p class="pb-1">
                                                Documents to be submitted to meet commercial eligibility criteria
                                            </p>
                                            <div class="table-responsive">
                                                <table class="table-bordered w-100">
                                                    <tr class="text-end bg-transparent">
                                                        <td colspan="3" class="p-0">
                                                            <button type="button" class="btn btn-xs btn-secondary"
                                                                id="addDoc">Add</button>
                                                        </td>
                                                    </tr>
                                                    <tbody id="documentstable">
                                                        @if ($tender && $tender->eligibleDocs)
                                                            @foreach ($tender->eligibleDocs as $doc)
                                                                <tr>
                                                                    <td colspan="2">
                                                                        <select
                                                                            name="docs[{{ $loop->index }}][doc_name]"
                                                                            class="form-select" id="doc_name">
                                                                            <option value="">Select</option>
                                                                            @foreach ($finance as $it)
                                                                                <option value="{{ $it->id }}"
                                                                                    {{ $doc->doc_name == $it->id ? 'selected' : '' }}>
                                                                                    {{ $it->document_name }}
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                            <small>
                                                <span class="text-danger">{{ $errors->first('documents') }}</span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-12 pt-2">
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label class="form-label" for="client_organisation">Client
                                                    Organisation</label>
                                                <input type="text" name="client_organisation" id="client_organisation"
                                                    class="form-control"
                                                    value="{{ $tenderInfo->client_organisation ?? '' }}">
                                                @error('client_organisation')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12 text-end">
                                                    <button type="button" class="btn btn-xs btn-secondary"
                                                        id="addClient">Add Client</button>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table-bordered w-100">
                                                            <thead class="text-center fw-bold">
                                                                <tr>
                                                                    <th style="font-size: 16px;">Client Name</th>
                                                                    <th style="font-size: 16px;">Designation</th>
                                                                    <th style="font-size: 16px;">Mobile</th>
                                                                    <th style="font-size: 16px;">Email</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="clientstable">
                                                                @if ($tenderInfo && $tenderInfo->client)
                                                                    @foreach ($tenderInfo->client as $client)
                                                                        <tr>
                                                                            <td style="font-size: 14px;">
                                                                                {{ $client->client_name }}</td>
                                                                            <td style="font-size: 14px;">
                                                                                {{ $client->client_designation }}</td>
                                                                            <td style="font-size: 14px;">
                                                                                {{ $client->client_mobile }}</td>
                                                                            <td style="font-size: 14px;">
                                                                                {{ $client->client_email }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="form-label" for="courier_address">Courier Address</label>
                                                <textarea name="courier_address" id="courier_address" rows="3" class="form-control">{{ $tenderInfo->courier_address ?? '' }}</textarea>
                                                @error('courier_address')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="form-label" for="te_remark">TE Remark</label>
                                                <textarea name="te_remark" id="te_remark" rows="3" class="form-control">{{ $tender->te_remark ?? '' }}</textarea>
                                                @error('te_remark')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Update Tender Info
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

@push('styles')
    <style>
        .select2-selection,
        .select2-selection__choice {
            background: transparent !important;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            $('form').submit(function() {
                $(this).find(':submit').attr('disabled', 'true');
            });

            const isRejectableFromDB = $('#is_rejectable').data('is-rejectable');
            $('#is_rejectable').on('change', function() {
                const selectedValue = $(this).val();
                console.log(selectedValue);
                if ($(this).val() === '1') {
                    console.log($('#rejectable'));

                    $('#rejectable').removeClass('d-none');
                    $('#reject_remarks').removeClass('d-none');
                } else {
                    $('#rejectable').addBack('d-none');
                    $('#reject_remarks').addBack('d-none');
                }
            });

            $('#phyDocs').on('change', function() {
                const phyDocs = $('#phyDocsDate');
                if ($(this).val() === 'No') {
                    phyDocs.addClass('d-none');
                    $('#dead_time').removeAttr('required');
                    $('#dead_date').removeAttr('required');
                } else {
                    phyDocs.removeClass('d-none');
                    $('#dead_time').attr('required', 'true');
                    $('#dead_date').attr('required', 'true');
                }
            });
            $('#phyDocs').trigger('change');

            // All select tag which has multiple attribute make it select2
            $('select[name="tender_fee[]"]').select2({
                'width': '100%',
            });
            $('select[name="emd_opt[]"]').select2({
                'width': '100%',
            });

            // Add new row for documents onclick #addDoc
            let docc = {{ count($tender->eligibleDocs ?? []) ?: 1 }};

            $('#addDoc').click(function() {
                let html = '';
                html += '<tr>';
                html +=
                    '<td><select name="docs[' + docc +
                    '][doc_name]" class="form-select" id="documents"><option value="">Select</option>';
                @foreach ($finance as $i)
                    html +=
                        '<option value="{{ $i->id }}">{{ $i->document_name }}</option>';
                @endforeach
                html += '</select></td>';
                html +=
                    '<td><button type="button" class="btn btn-danger btn-xs" id="removeDoc"><i class="fa fa-minus"></i></button></td>';
                html += '</tr>';
                $('tbody#documentstable').append(html);
                docc++;
            });

            $('#documentstable').on('click', '#removeDoc', function() {
                $(this).closest('tr').remove();
            });

            // Add new row for workOrderTable onclick #addWorkOrder
            let woc = {{ count($tender->workOrder ?? []) ?: 1 }};
            $('#addWorkOrder').click(function() {
                let html = '';
                html += '<tr>';
                html += '<td><select name="wo[' + woc +
                    '][wo_name]" class="form-select" id="workorder"><option value="">Select</option>';
                @foreach ($pqr as $it)
                    html +=
                        '<option value="{{ $it->id }}">{{ $it->project_name }}</option>';
                @endforeach
                html += '</select></td>';
                html +=
                    '<td><button type="button" class="btn btn-danger btn-xs" id="removeWorkOrder"><i class="fa fa-minus"></i></button></td>';
                html += '</tr>';
                $('tbody#workOrderTable').append(html);
                woc++;
            });

            $('#workOrderTable').on('click', '#removeWorkOrder', function() {
                $(this).closest('tr').remove();
            });

            $('#sc').on('change', function() {
                if ($(this).val() == 'amt') {
                    $('#sc_amt').prop('readonly', false);
                } else {
                    $('#sc_amt').prop('readonly', true);
                }
            })
            $('#aat').on('change', function() {
                if ($(this).val() == 'amt') {
                    $('#aat_amt').prop('readonly', false);
                } else {
                    $('#aat_amt').prop('readonly', true);
                }
            })
            $('#wc').on('change', function() {
                if ($(this).val() == 'amt') {
                    $('#wc_amt').prop('readonly', false);
                } else {
                    $('#wc_amt').prop('readonly', true);
                }
            })
            $('#nw').on('change', function() {
                if ($(this).val() == 'amt') {
                    $('#nw_amt').prop('readonly', false);
                } else {
                    $('#nw_amt').prop('readonly', true);
                }
            })

            // add client row
            let client = {{ count($tenderInfo->client ?? []) ?: 0 }};
            $('#addClient').click(function() {
                let html = '';
                html += '<tr>';
                html += '<td><input type="text" name="client[' + client +
                    '][client_name]" class="form-control" placeholder="Client Name"></td>';
                html += '<td><input type="text" name="client[' + client +
                    '][client_designation]" class="form-control" placeholder="Client Designation"></td>';
                html += '<td><input type="number" name="client[' + client +
                    '][client_mobile]" class="form-control" placeholder="Client Mobile"></td>';
                html += '<td><input type="email" name="client[' + client +
                    '][client_email]" class="form-control" placeholder="Client Email"></td>';
                html +=
                    '<td><button type="button" class="btn btn-danger btn-xs" id="removeClient"><i class="fa fa-minus"></i></button></td>';
                html += '</tr>';
                $('tbody#clientstable').append(html);
                client++;
            });

            $('#clientstable').on('click', '#removeClient', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endpush
