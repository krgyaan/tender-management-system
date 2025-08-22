@extends('layouts.app')
@section('page-title', 'Initiate Kick Off Meeting')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="{{ asset('/admin/initiate_meeting_post') }}" enctype="multipart/form-data"
                            id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                            @csrf
                            <input type="hidden" value="{{ $datawo->id }}" name="id">
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
                                    <table class="table-bordered" id="tab_logic">
                                        <thead>
                                            <tr>
                                                <th class="fs-6 fw-bold">Organization</th>
                                                <th class="fs-6 fw-bold">Departments</th>
                                                <th class="fs-6 fw-bold">Name</th>
                                                <th class="fs-6 fw-bold">Designation</th>
                                                <th class="fs-6 fw-bold">Phone</th>
                                                <th class="fs-6 fw-bold">Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($woData['departments']) && count($woData['departments']))
                                                @for ($i = 0; $i < count($woData['departments']); $i++)
                                                    <tr id="addr0">
                                                        <td>
                                                            <input type="text" name="organization[]" class="form-control"
                                                                value="{{ $woData['organization'][$i] }}" required>
                                                        </td>
                                                        <td>
                                                            <select name="departments[]" class="form-control" required>
                                                                <option value="" disabled selected>Select Departments
                                                                </option>
                                                                @foreach (['EIC', 'User', 'C&P', 'Finance'] as $department)
                                                                    <option value="{{ $department }}"
                                                                        @if (isset($woData['departments'][$i]) && $woData['departments'][$i] == $department) selected @endif>
                                                                        {{ $department }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="name[]"
                                                                value="{{ $woData['name'][$i] }}" class="form-control"
                                                                required>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="designation[]"
                                                                value="{{ $woData['designation'][$i] }}"
                                                                class="form-control" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="phone[]"
                                                                value="{{ $woData['phone'][$i] }}" class="form-control"
                                                                required>
                                                        </td>
                                                        <td>
                                                            <input type="email" name="email[]"
                                                                value="{{ $woData['email'][$i] }}" class="form-control"
                                                                required>
                                                        </td>
                                                    </tr>
                                                @endfor
                                            @endif
                                            <tr id="addr1"></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 pt-3">
                                    <label for="meeting_date_time" class="form-label">Kick Off Meeting Date And Time</label>
                                    <input type="datetime-local" class="form-control" name="meeting_date_time"
                                        id="meeting_date_time" required value="{{ $datawo->meeting_date_time }}">
                                </div>
                                <div class="col-md-6 pt-3">
                                    <label for="google_meet_link" class="form-label">Google Meet Link</label>
                                    <input type="url" class="form-control" name="google_meet_link" id="google_meet_link"
                                        required value="{{ $datawo->google_meet_link }}">
                                    <small>
                                        <i class="fa fa-info-circle"></i> Visit
                                        <a href="https://meet.google.com/" target="_blank">Google Meet</a> to create a
                                        meeting
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-12 pt-4 text-end">
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
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
    </script>
@endpush
