@extends('layouts.app')
@section('page-title', ' Employees Imprest (Account)')
@section('content')
    <div class="page-wrapper">
        @include('partials.messages')
        <div class="page-content">
            <div class="row">
                <div class="col-xl-11 mx-auto">
                    <div class="card">
                        <div class="card-header px-4 py-3">
                            <h5 class="mb-0">Update Employee Imprest(Account)</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="post" action="{{ asset('/admin/employeeimprest_account_update') }}"
                                id="employee-imprest-form" class="row g-3 needs-validation" novalidate>
                                @csrf
                                <input type="hidden" value="{{ $employeeimprest_update->id }}" name="id"
                                    class="form-control" id="">
                                <div class="col-md-6">
                                    <label for="name_id" class="form-label">Name<span class="text-danger">*</span></label>
                                    <select name="name_id" class="form-control" id="name_id" required>
                                        <option value="">Select Option</option>
                                        @foreach ($employeeimprest as $key => $employee)
                                            <option value="{{ $employee->id }}"
                                                @if ($employeeimprest_update->name == $employee->id) selected @endif>{{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="additional-fields" style="display: none; width: 100%;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="date" class="form-label">Date<span
                                                    class="text-danger">*</span></label>
                                            <input type="date"
                                                value="{{ \Carbon\Carbon::parse($employeeimprest_update->date)->format('Y-m-d') }}"
                                                name="date" class="form-control" id="date" placeholder="Date"
                                                required>

                                        </div>
                                        <div class="col-md-6">
                                            <label for="team_member_name" class="form-label">Team Member Name<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" value="{{ $employeeimprest_update->team_member_name }}"
                                                name="team_member_name" class="form-control" id="team_member_name"
                                                placeholder="Team Member Name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="amount" class="form-label">Amount<span
                                                    class="text-danger">*</span></label>
                                            <input value="{{ $employeeimprest_update->amount }}" type="text"
                                                name="amount" class="form-control" id="amount" placeholder="Amount"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="project_name" class="form-label">Project Name<span
                                                    class="text-danger">*</span></label>
                                            <select name="project_name" class="form-control" id="project_name" required>
                                            </select>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">

                                                <button type="submit" class="btn btn-primary px-4">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $("#name_id").change(function() {
            var name_id = $('#name_id').val();
            if (name_id == '') {
                $('#additional-fields').hide();
            } else {
                $('#additional-fields').show();
                $.ajax({
                    type: "GET",
                    url: "{{ asset('/admin/employeeimprest_amount_project') }}",
                    data: {
                        name_id: name_id
                    },
                    success: function(data) {
                        $("#project_name").html(data);

                    }
                });
            }
        });
    </script>
@endpush
