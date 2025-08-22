@extends('layouts.app')
@section('page-title', ' Employees Imprest (Account)')
@section('content')
    @php
        $projects = App\Models\Project::all();
    @endphp
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-header px-4 py-3">
                            <h5 class="mb-0">Add Employee Imprest(Account)</h5>
                        </div>
                        @include('partials.messages')
                        <div class="card-body p-4">
                            <form method="post" action="{{ asset('/admin/employeeimprest_amount_post') }}"
                                id="formatDistrict-update" class="row g-3 needs-validation" novalidate onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                @csrf
                                <div class="col-md-6">
                                    <label for="name_id" class="form-label">Name<span class="text-danger">*</span></label>
                                    <select name="name_id" class="form-control" id="name_id" required>
                                        <option value="">{{ $employeeimprest_update->user->name }}</option>

                                        @foreach ($employee as $key => $employee)
                                            <option value="{{ $employee->id }}"
                                                data-encrypted-id="{{ Crypt::encrypt($employee->id) }}">
                                                {{ $employee->user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('name_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Buttons (Initially Hidden) -->
                                <div class="col-md-6 mt-5" id="buttons-container" style="display: none;">
                                    <button type="button" class="btn btn-primary" id="pay-imprest-btn">Pay Imprest</button>
                                    <a href="#" id="view-dashboard-btn" class="btn btn-secondary"
                                        style="pointer-events: none;">View Dashboard</a>
                                </div>

                                <!-- Additional Fields (Initially Hidden) -->
                                <div id="additional-fields" style="display: none; width: 100%;">
                                    <div class="row">
                                        <!-- Date Input -->
                                        <div class="col-md-6">
                                            <label for="date" class="form-label">Date</label>
                                            <input type="date" name="date" class="form-control" id=""
                                                placeholder="Date" required>
                                            @error('date')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Team Member Name Input -->
                                        <div class="col-md-6">
                                            <label for="team_member_name" class="form-label">Team Member Name</label>
                                            <input type="text" name="team_member_name" class="form-control"
                                                id="team_member_name" placeholder="Team Member Name" required>
                                            @error('team_member_name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Amount Input -->
                                        <div class="col-md-6">
                                            <label for="amount" class="form-label">Amount</label>
                                            <input type="text" name="amount" class="form-control" id="amount"
                                                placeholder="Amount" required>
                                            @error('amount')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Project Name Input -->
                                        <div class="col-md-6" id="someOptional2">
                                            <label for="project_name" class="form-label">Project Name</label>
                                            <select name="project_name" class="form-control" id="project_name" required>
                                                <option value="">Select Project</option>
                                                @foreach ($projects as $pro)
                                                    <option value="{{ $pro->project_name }}">{{ $pro->project_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('project_name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit and Cancel Buttons -->
                                <div class="col-md-12">
                                    <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                                        <button type="reset" class="btn btn-light px-4">Cancel</button>
                                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#project_name').select2({
                placeholder: 'Select Project',
                allowClear: true,
                width: '100%',
                height: 38,
            });
        });
    </script>
@endpush
