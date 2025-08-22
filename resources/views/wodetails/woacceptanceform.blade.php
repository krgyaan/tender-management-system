@extends('layouts.app')
@section('page-title', 'WO Acceptance')
@section('content')
    <section>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="{{ url('/admin/woacceptanceformpost') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ $basic->id }}" name="basic_detail_id">
                        <div class="row">
                            <div class="col-md-4 pt-3">
                                <label for="pbg_applicable" class="form-label">WO Amendment Needed</label>
                                <select name="amendment_needed" class="form-control" id="pbg_applicable" required>
                                    <option value="" disabled {{ !isset($woacc) ? 'selected' : '' }}>Select</option>
                                    <option value="1" {{ (isset($woacc) && $woacc->page_no) ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="0" {{ (isset($woacc) && $woacc->accepted_initiate) ? 'selected' : '' }}>
                                        No</option>
                                </select>
                            </div>

                            <div id="file_inputs" class="col-md-12 pt-3" style="display: none;">
                                <div class="row">
                                    <input type="hidden" value="yes" name="accepted_initiate">

                                    <div class="mb-2 col-md-4">
                                        <label class="form-label">Upload Accepted and Signed Copy of the WO</label>
                                        <input type="file" class="form-control" name="accepted_signed"
                                            accept="image/*,application/pdf">
                                        @if(isset($woacc) && $woacc->accepted_signed)
                                            <div class="mt-2">
                                                <a href="{{ asset('upload/acceptance/' . $woacc->accepted_signed) }}"
                                                    target="_blank">View Existing File</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="col-md-12 text-end mt-3" id="buttons" style="display: none;">
                                    <button type="button" id="add_row" class="btn btn-primary btn-xs">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                    <button type="button" id="delete_row" class="btn btn-danger btn-xs">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>

                                <div class="col-md-12" id="data_row" style="display: none;">
                                    <table class="table table-bordered table-hover table-responsive" id="tab_logic">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">Page No</th>
                                                <th style="width: 5%">Clause No</th>
                                                <th style="width: 45%">Current Statement</th>
                                                <th style="width: 45%">Corrected Statement</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $page_no = isset($woacc) && $woacc->page_no ? json_decode($woacc->page_no, true) : [''];
                                                $clause_no = isset($woacc) && $woacc->clause_no ? json_decode($woacc->clause_no, true) : [''];
                                                $current_statement = isset($woacc) && $woacc->current_statement ? json_decode($woacc->current_statement, true) : [''];
                                                $corrected_statement = isset($woacc) && $woacc->corrected_statement ? json_decode($woacc->corrected_statement, true) : [''];
                                                $maxRows = max(count($page_no), count($clause_no), count($current_statement), count($corrected_statement));
                                            @endphp
                                            @for($i = 0; $i < $maxRows; $i++)
                                                <tr id="addr{{ $i }}">
                                                    <td>
                                                        <input type="text" name="page_no[]" class="form-control"
                                                            value="{{ $page_no[$i] ?? '' }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="clause_no[]" class="form-control"
                                                            value="{{ $clause_no[$i] ?? '' }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="current_statement[]" class="form-control"
                                                            value="{{ $current_statement[$i] ?? '' }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="corrected_statement[]" class="form-control"
                                                            value="{{ $corrected_statement[$i] ?? '' }}">
                                                    </td>
                                                </tr>
                                            @endfor
                                            <tr id="addr{{ $maxRows }}"></tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 pt-3" id="data_row2" style="display: none;">
                                        <label for="pbg_applicable_2" class="form-label">Followup Frequency</label>
                                        <div class="input-group">
                                            <select name="followup_frequency" class="form-control" id="pbg_applicable_2">
                                                <option value="" disabled {{ !isset($woacc) ? 'selected' : '' }}>Select
                                                </option>
                                                <option value="daily" {{ (isset($woacc) && $woacc->followup_frequency == 'daily') ? 'selected' : '' }}>Daily</option>
                                                <option value="alternate" {{ (isset($woacc) && $woacc->followup_frequency == 'alternate') ? 'selected' : '' }}>Alternate
                                                    Days</option>
                                                <option value="weekly" {{ (isset($woacc) && $woacc->followup_frequency == 'weekly') ? 'selected' : '' }}>Weekly (every
                                                    Monday)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pt-4 text-end">
                                <button type="submit"
                                    class="btn btn-primary btn-sm">{{ isset($woacc) ? 'Update' : 'Submit' }}</button>
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
            var i = 1;

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

            $('#tab_logic tbody').on('keyup change', 'input', function () {
                calc();
            });

            $('#tax').on('keyup change', function () {
                calc_total();
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Trigger the change event on page load to set the correct visibility
            var select = document.getElementById('pbg_applicable');
            if (select) {
                var event = new Event('change');
                select.dispatchEvent(event);
            }
        });

        document.getElementById('pbg_applicable').addEventListener('change', function () {
            var dataRow = document.getElementById('data_row');
            var dataRow2 = document.getElementById('data_row2');
            var buttons = document.getElementById('buttons');
            var fileInputs = document.getElementById('file_inputs');
            if (this.value == '1') {
                dataRow.style.display = 'block';
                dataRow2.style.display = 'block';
                buttons.style.display = 'block';
                fileInputs.style.display = 'none';
            } else if (this.value == '0') {
                dataRow.style.display = 'none';
                dataRow2.style.display = 'none';
                buttons.style.display = 'none';
                fileInputs.style.display = 'block';
            } else {
                dataRow.style.display = 'none';
                dataRow2.style.display = 'none';
                buttons.style.display = 'none';
                fileInputs.style.display = 'none';
            }
        });
    </script>
@endpush
