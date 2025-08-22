@extends('layouts.app')
@section('page-title', 'TQ Received Form')
@section('content')

    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="{{ asset('/admin/tq_received_form_post') }}" enctype="multipart/form-data"
                            id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                            @csrf
                            <input type="hidden" value="{{ $tender_id }}" name="tender_id">
                            <div class="row">
                                <div class="col-md-12 text-right mt-3">
                                    <div class="d-flex justify-content-end">
                                        <p id="add_row" class="btn btn-primary"
                                            style="font-size: 14px; padding: 5px 10px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <rect x="10" y="4" width="4" height="16" />
                                                <rect x="4" y="10" width="16" height="4" />
                                            </svg>
                                        </p>
                                        <p id="delete_row" class="btn btn-danger"
                                            style="font-size: 14px; padding: 5px 10px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <rect x="4" y="10" width="16" height="4" />
                                            </svg>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-bordered table-hover table-responsive" id="tab_logic">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 10%;">Sr.No</th>
                                                <th class="text-center" style="width: 30%;">TQ Type</th>
                                                <th class="text-center" style="width: 70%;">Query description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="addr0">
                                                <td style="width: 10%;" class="text-center">1</td>
                                                <td style="width: 30%;">
                                                    <select name="tq_type_id[]" class="form-control" required>
                                                        <option value="" disabled selected>TQ Type</option>
                                                        @foreach ($typedata as $data)
                                                            <option value="{{ $data->id }}">
                                                                {{ $data->tq_type }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="width: 70%;">
                                                    <input type="text" name="description[]"
                                                        placeholder="Enter description" class="form-control" required>
                                                </td>
                                            </tr>
                                            <tr id="addr1"></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 pt-3">
                                    <label for="input28" class="form-label">TQ Submission Date <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" name="date" id="input28"
                                            placeholder="Date" required>
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3">
                                    <label for="input28" class="form-label">TQ Submission Time <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="time" class="form-control" name="time" id="input28"
                                            placeholder="Date" required>
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3">
                                    <label for="input28" class="form-label">Uplode TQ document <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">

                                        <input type="file" class="form-control" name="tq_img" id="input28"
                                            placeholder="Max LD" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pt-5">
                                <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var i = 1;

                $("#add_row").click(function() {
                    var b = i - 1;
                    $('#addr' + i).html($('#addr' + b).html()).find('td:first-child').html(i + 1);
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
        </script>
    @endpush
@endsection
