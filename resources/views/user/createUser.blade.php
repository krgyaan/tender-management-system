@extends('layouts.app')
@section('page-title', 'Create Employees')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin/user/all') }}" class="btn btn-primary btn-sm">View All Employees</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="new-user-info">
                            <form method="POST" action="" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="name">Full Name:</label>
                                        <input type="text" name="name" class="form-control" id="name"
                                            placeholder="First Name">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('name') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="email">Email ID:</label>
                                        <input type="email" name="email" class="form-control" id="email"
                                            placeholder="Email">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="password">Password:</label>
                                        <input type="password" name="password" class="form-control" id="password"
                                            placeholder="Password">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('password') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="mobile">Mobile Number:</label>
                                        <input type="number" name="mobile" class="form-control" id="mobile"
                                            placeholder="Mobile Number">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('mobile') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="address">Address:</label>
                                        <textarea name="address" class="form-control" id="address" placeholder="Address"></textarea>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('address') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="team">Team Name:</label>
                                        <select name="team" class="form-control" id="team">
                                            <option value="">Select Team Name</option>
                                            @foreach ($teams as $key => $team)
                                                <option value="{{ $key }}">{{ $team }}</option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('team') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">User Role:</label>
                                        <select name="role" class="selectpicker form-select" data-style="py-0">
                                            <option value="">Select User Role</option>
                                            @foreach ($roles as $key => $role)
                                                <option value="{{ $key }}">{{ $role }}</option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('role') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="designation">Designation:</label>
                                        <select name="designation" class="form-control" id="designation">
                                            <option value="">Select Designation Name</option>
                                            @foreach ($designations as $key => $designation)
                                                <option value="{{ $key }}">{{ $designation }}</option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('designation') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="form-label" for="permissions">Permissions:</label>
                                        <div class="row">
                                            @foreach ($permissions as $key => $permission)
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox" type="checkbox"
                                                            value="{{ $key }}" id="{{ $key }}"
                                                            id="permissions{{ $key }}" name="permissions[]">
                                                        <label class="form-check-label" for="{{ $key }}">
                                                            {{ $permission }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('permissions') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="profile-img-edit position-relative">
                                            <label class="form-label" for="image">Profile Image:</label>
                                            <input type="file" name="image" id="image"
                                                class="form-control mb-2" accept="image/*"
                                                onchange="previewFile(event, 'profileDisplay')" />
                                            <img class="img-fluid avatar avatar-100 avatar-rounded" id="profileDisplay"
                                                src="{{ asset('assets/images/avatars/01.png') }}" alt="profile-pic">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="profile-img-edit2 position-relative">
                                            <label class="form-label" for="id_proof">ID Proof:</label>
                                            <input type="file" name="id_proof" id="id_proof"
                                                class="form-control mb-2" accept="image/*"
                                                onchange="previewFile(event, 'profileDisplayIdProof')" />
                                            <img class="img-fluid avatar" id="profileDisplayIdProof"
                                                src="{{ asset('assets/images/pages/02-page.png') }}" alt="id_proof">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit" class="btn btn-primary">Add New
                                        Employee</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Function to preview the selected file
        function previewFile(event, previewId) {
            const input = event.target;
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "{{ asset('assets/images/avatars/01.png') }}";
            }
        }

        // Function to handle "ALL" permission checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const allPermissionCheckbox = document.querySelector('input[value="all"]');
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

            allPermissionCheckbox.addEventListener('change', function() {
                permissionCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = allPermissionCheckbox.checked;
                });
            });

            permissionCheckboxes.forEach(function(checkbox) {
                if (checkbox.value !== 'ALL') {
                    checkbox.addEventListener('change', function() {
                        if (!checkbox.checked) {
                            allPermissionCheckbox.checked = false;
                        }
                    });
                }
            });
        });
    </script>
@endpush
