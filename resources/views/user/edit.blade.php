@extends('layouts.app')
@section('page-title', "Edit $user->name's Info")
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
                                        <input type="text" name="name" value="{{ $user->name }}"
                                            class="form-control" id="name" placeholder="First Name">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="email">Email ID:</label>
                                        <input type="email" name="email" value="{{ $user->email }}"
                                            class="form-control" id="email" placeholder="Email">
                                    </div>
                                    <div class="form-group col-md-6 d-none">
                                        <label class="form-label" for="password">Password:</label>
                                        <input type="password" name="password" value="{{ $user->password }}"
                                            class="form-control" id="password" placeholder="Password">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="mobile">Mobile Number:</label>
                                        <input type="number" name="mobile" value="{{ $user->mobile }}"
                                            class="form-control" id="mobile" placeholder="Mobile Number">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="address">Address:</label>
                                        <textarea name="address" class="form-control" id="address" placeholder="Address">{{ $user->address }}</textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="team">Team Name:</label>
                                        <select name="team" class="form-control" id="team">
                                            <option value="">Select Team Name</option>
                                            @foreach ($teams as $key => $team)
                                                <option value="{{ $key }}"
                                                    {{ $user->team == $key ? 'selected' : '' }}>{{ $team }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('team') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">User Role:</label>
                                        <select name="role" class="selectpicker form-select" data-style="py-0">
                                            <option value="">Select</option>
                                            @foreach ($roles as $key => $role)
                                                <option {{ $user->role == $key ? 'selected' : '' }}
                                                    value="{{ $key }}">{{ $role }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="designation">Designation:</label>
                                        <select name="designation" class="form-control" id="designation">
                                            <option value="">Select</option>
                                            @foreach ($designations as $key => $designation)
                                                <option {{ $user->designation == $key ? 'selected' : '' }}
                                                    value="{{ $key }}">{{ $designation }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="joining_date">Status:</label>
                                        <select name="status" class="form-control" id="status">
                                            <option value="">Select</option>
                                            <option {{ $user->status == '1' ? 'selected' : '' }} value="1">
                                                Active
                                            </option>
                                            <option {{ $user->status == '0' ? 'selected' : '' }} value="0">
                                                Inactive
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        @php
                                            $perms = explode(',', $user->permissions);
                                        @endphp
                                        <label class="form-label" for="permissions">Permissions:</label>
                                        <div class="row">
                                            @foreach ($permissions as $key => $permission)
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox"
                                                            type="checkbox" value="{{ $key }}"
                                                            id="{{ $key }}"
                                                            {{ in_array($key, $perms) ? 'checked' : '' }}
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
                                                src="{{ url('/uploads/') }}/{{ $user->image }}" alt="profile-pic">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="profile-img-edit2 position-relative">
                                            <label class="form-label" for="id_proof">ID Proof:</label>
                                            <input type="file" name="id_proof" id="id_proof"
                                                class="form-control mb-2" accept="image/*"
                                                onchange="previewFile(event, 'profileDisplayIdProof')" />
                                            <img class="img-fluid avatar" id="profileDisplayIdProof"
                                                src="{{ url('/uploads/') }}/{{ $user->id_proof }}" alt="id_proof">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('admin/user/all') }}" type="button"
                                        class="btn text-danger">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Employee</button>
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
