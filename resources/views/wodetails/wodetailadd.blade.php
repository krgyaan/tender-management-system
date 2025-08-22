@extends('layouts.app')
@section('page-title', 'Add WO Details')
@section('content')
    <section>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title text-center">WO Details for {{ $tender->tender_name }}</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ url('/admin/wodetailaddpost') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ $tender->basic_details->id }}" name="basic_detail_id">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="button" id="add_row" class="btn btn-primary btn-xs">
                                    <i class="fa fa-plus"></i>
                                </button>
                                <button type="button" id="delete_row" class="btn btn-danger btn-xs">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover table-responsive" id="tab_logic">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Organization</th>
                                            <th class="text-center">Departments</th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Designation</th>
                                            <th class="text-center">Phone</th>
                                            <th class="text-center">Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $orgs = $wodetails ? json_decode($wodetails->organization, true) : [old('organization.0', $tender?->organizations->name)];
                                            $depts = $wodetails ? json_decode($wodetails->departments, true) : [old('departments.0')];
                                            $names = $wodetails ? json_decode($wodetails->name, true) : [old('name.0')];
                                            $designations = $wodetails ? json_decode($wodetails->designation, true) : [old('designation.0')];
                                            $phones = $wodetails ? json_decode($wodetails->phone, true) : [old('phone.0')];
                                            $emails = $wodetails ? json_decode($wodetails->email, true) : [old('email.0')];
                                            $rowCount = max(count($orgs), count($depts), count($names), count($designations), count($phones), count($emails));
                                        @endphp
                                        @for($i = 0; $i < $rowCount; $i++)
                                            <tr id="addr{{ $i }}">
                                                <td>
                                                    <input type="text" name="organization[]" class="form-control"
                                                        value="{{ $orgs[$i] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <select name="departments[]" class="form-control" required>
                                                        <option value="" disabled {{ empty($depts[$i]) ? 'selected' : '' }}>
                                                            Select Departments</option>
                                                        <option value="EIC" {{ (isset($depts[$i]) && $depts[$i] == 'EIC') ? 'selected' : '' }}>EIC</option>
                                                        <option value="User" {{ (isset($depts[$i]) && $depts[$i] == 'User') ? 'selected' : '' }}>User</option>
                                                        <option value="C&P" {{ (isset($depts[$i]) && $depts[$i] == 'C&P') ? 'selected' : '' }}>C&amp;P</option>
                                                        <option value="Finance" {{ (isset($depts[$i]) && $depts[$i] == 'Finance') ? 'selected' : '' }}>Finance</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="name[]" class="form-control"
                                                        value="{{ $names[$i] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="designation[]" class="form-control"
                                                        value="{{ $designations[$i] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="phone[]" class="form-control" maxlength="10"
                                                        value="{{ $phones[$i] ?? '' }}" required>
                                                </td>
                                                <td>
                                                    <input type="email" name="email[]" class="form-control"
                                                        value="{{ $emails[$i] ?? '' }}" required>
                                                </td>
                                            </tr>
                                        @endfor
                                        <tr id="addr{{ $rowCount }}"></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pt-3">
                                <label for="budget_pre_gst" class="form-label">Budget(Pre GST)</label>
                                <input type="text" class="form-control" name="budget_pre_gst" id="budget_pre_gst"
                                    value="{{ $wodetails->budget ?? '' }}" required>
                            </div>
                            <div class="col-md-4 pt-3">
                                <label for="maxld" class="form-label">Max LD%</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="max_ld" id="maxld"
                                        value="{{ $wodetails->max_ld ?? '' }}" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-4 pt-3">
                                <label for="ldstart" class="form-label">LD Start Date</label>
                                <input type="date" class="form-control" name="ldstartdate" id="ldstart"
                                    value="{{ $wodetails->ldstartdate ?? '' }}" required>
                            </div>
                            <div class="col-md-4 pt-3">
                                <label for="maxlddate" class="form-label">Max. LD Date</label>
                                <input type="date" class="form-control" name="maxlddate" id="maxlddate"
                                    value="{{ $wodetails->maxlddate ?? '' }}" required>
                            </div>
                            <div class="col-md-4 pt-3">
                                <label for="pbg_select" class="form-label">PBG Applicable</label>
                                <select name="pbg_applicable" class="form-control" id="pbg_select" required>
                                    <option value="" disabled {{ !isset($wodetails->pbg_applicable_status) ? 'selected' : '' }}>Select PBG</option>
                                    <option value="1" {{ (isset($wodetails->pbg_applicable_status) && $wodetails->pbg_applicable_status == 1) ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ (isset($wodetails->pbg_applicable_status) && $wodetails->pbg_applicable_status == 0) ? 'selected' : '' }}>No</option>
                                </select>
                                <div class="pt-3" id="input_field"
                                    style="display:{{ (isset($wodetails->pbg_applicable_status) && $wodetails->pbg_applicable_status == 1) ? 'block' : 'none' }};">
                                    <label for="file_applicable" class="form-label">Upload the filled BG Format</label>
                                    <input type="file" class="form-control" id="file_applicable" name="file_applicable">
                                    @if($wodetails && $wodetails->file_applicable)
                                        <a href="{{ asset('upload/applicable/' . $wodetails->file_applicable) }}"
                                            target="_blank">View Existing</a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 pt-3">
                                <label for="contract_agreement_select" class="form-label">Contract Agreement</label>
                                <select name="contract_agreement" class="form-control" id="contract_agreement_select">
                                    <option value="" disabled {{ !isset($wodetails->contract_agreement_status) ? 'selected' : '' }}>Select Contract</option>
                                    <option value="1" {{ (isset($wodetails->contract_agreement_status) && $wodetails->contract_agreement_status == 1) ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ (isset($wodetails->contract_agreement_status) && $wodetails->contract_agreement_status == 0) ? 'selected' : '' }}>No</option>
                                </select>
                                <div class="pt-3" id="contract_input_field"
                                    style="display:{{ (isset($wodetails->contract_agreement_status) && $wodetails->contract_agreement_status == 1) ? 'block' : 'none' }};">
                                    <label for="contract_input" class="form-label">Upload the Contract Agreement
                                        Format</label>
                                    <input type="file" class="form-control" id="contract_input" name="file_agreement">
                                    @if($wodetails && $wodetails->file_agreement)
                                        <a href="{{ asset('upload/applicable/' . $wodetails->file_agreement) }}"
                                            target="_blank">View Existing</a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 pt-4 text-end">
                                <button type="submit" class="btn btn-primary btn-sm px-4">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var i = {{ $rowCount ?? 1 }};

            $("#add_row").click(function () {
                var b = i - 1;
                $('#addr' + i).html($('#addr' + b).html());
                $('#tab_logic').append('<tr id="addr' + (i + 1) + '"></tr>');
                i++;
            });

            $("#delete_row").click(function () {
                if (i > 1) {
                    $("#addr" + (i - 1)).html('');
                    i--;
                }
            });
        });

        document.getElementById('pbg_select').addEventListener('change', function () {
            var inputField = document.getElementById('input_field');
            if (this.value === '1') {
                inputField.style.display = 'block';
            } else {
                inputField.style.display = 'none';
            }
        });

        document.getElementById('contract_agreement_select').addEventListener('change', function () {
            var contractInputField = document.getElementById('contract_input_field');
            if (this.value === '1') {
                contractInputField.style.display = 'block';
            } else {
                contractInputField.style.display = 'none';
            }
        });
    </script>
@endpush
