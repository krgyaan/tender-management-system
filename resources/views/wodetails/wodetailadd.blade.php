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
                    <form method="post" action="{{ asset('/admin/wodetailaddpost') }}" enctype="multipart/form-data">
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
                                        <tr id="addr0">
                                            <td>
                                                <input type="text" name="organization[]" class="form-control"
                                                    value="{{ $tender?->organizations->name }}" required>
                                            </td>
                                            <td>
                                                <select name="departments[]" class="form-control" required>
                                                    <option value="" disabled selected>Select Departments</option>
                                                    <option value="EIC">EIC</option>
                                                    <option value="User">User</option>
                                                    <option value="C&P">C&P</option>
                                                    <option value="Finance">Finance</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="name[]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="text" name="designation[]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="number" name="phone[]" class="form-control" maxlength="10"
                                                    required>
                                            </td>
                                            <td>
                                                <input type="email" name="email[]" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr id="addr1"></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pt-3">
                                <label for="budget_pre_gst" class="form-label">Budget(Pre GST)</label>
                                <input type="text" class="form-control" name="budget_pre_gst" id="budget_pre_gst"
                                    required>
                            </div>
                            <div class="col-md-4 pt-3">
                                <label for="maxld" class="form-label">Max LD%</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="max_ld" id="maxld" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-4 pt-3">
                                <label for="ldstart" class="form-label">LD Start Date</label>
                                <input type="date" class="form-control" name="ldstartdate" id="ldstart" required>
                            </div>
                            <div class="col-md-4 pt-3">
                                <label for="maxld" class="form-label">Max. LD Date</label>
                                <input type="date" class="form-control" name="maxlddate" id="maxld" required>
                            </div>

                            <div class="col-md-4 pt-3">
                                <label for="pbg_select" class="form-label">PBG Applicable</label>
                                <select name="pbg_applicable" class="form-control" id="pbg_select" required>
                                    <option value="" disabled selected>Select PBG</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <div class="pt-3" id="input_field" style="display:none;">
                                    <label for="file_applicable" class="form-label">Upload the filled BG Format</label>
                                    <input type="file" class="form-control" id="file_applicable"
                                        name="file_applicable">
                                </div>
                            </div>
                            <div class="col-md-4 pt-3">
                                <label for="input28" class="form-label">Contract Agreement</label>
                                <select name="contract_agreement" class="form-control" id="contract_agreement_select">
                                    <option value="" disabled selected>Select Contract</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <div class="pt-3" id="contract_input_field" style="display:none;">
                                    <label for="contract_input" class="form-label">Upload the Contract Agreement
                                        Format</label>
                                    <input type="file" class="form-control" id="contract_input"
                                        name="file_agreement">
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
        $(document).ready(function() {
            var i = 1;

            $("#add_row").click(function() {
                var b = i - 1;
                $('#addr' + i).html($('#addr' + b).html());
                $('#tab_logic').append('<tr id="addr' + (i + 1) + '"></tr>');
                i++;
            });

            $("#delete_row").click(function() {
                if (i > 1) {
                    $("#addr" + (i - 1)).html('');
                    i--;
                }
            });

            $('#tab_logic tbody').on('keyup change', function() {
                calc();
            });

            $('#tax').on('keyup change', function() {
                calc_total();
            });
        });

        document.getElementById('pbg_select').addEventListener('change', function() {
            var inputField = document.getElementById('input_field');
            if (this.value === '1') {
                inputField.style.display = 'block';
            } else {
                inputField.style.display = 'none';
            }
        });

        document.getElementById('contract_agreement_select').addEventListener('change', function() {
            var contractInputField = document.getElementById('contract_input_field');
            if (this.value === '1') {
                contractInputField.style.display = 'block';
            } else {
                contractInputField.style.display = 'none';
            }
        });
    </script>
@endpush
