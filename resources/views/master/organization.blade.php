@extends('layouts.app')

@section('page-title', 'Organizations')

@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary" id="addOrgBtn">Create New Organization</button>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('partials.messages')
                    <div class="table-responsive">
                        <table class="table" id="statuses table">
                            <thead class="">
                                <tr>
                                    <th>S.No.</th>
                                    <th>Name</th>
                                    <th>Full Form</th>
                                    <th>Industry</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($organizations as $org)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $org->name }}</td>
                                        <td>{{ $org->full_form }}</td>
                                        <td>{{ $org->industry }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-xs editOrgBtn"
                                                data-id="{{ $org->id }}" data-name="{{ $org->name }}"
                                                data-full_form="{{ $org->full_form }}" data-industry="{{ $org->industry }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <a href="#"
                                                onclick="event.preventDefault(); document.getElementById('deleteForm{{ $org->id }}').submit();"
                                                class="btn btn-danger btn-xs">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <form action="{{ route('organizations.destroy', $org->id) }}" method="POST"
                                                id="deleteForm{{ $org->id }}" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $org->id }}">
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Organization Modal -->
    <div class="modal fade" id="orgModal" tabindex="-1" role="dialog" aria-labelledby="orgModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title" id="orgModalLabel"></h5>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <span class="fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="orgForm" method="POST">
                    @csrf
                    <span id="update-method"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="org_name">Organization Name</label>
                            <input type="text" class="form-control" id="org_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="full_form">Full Form</label>
                            <textarea class="form-control" id="full_form" name="full_form" required></textarea>
                        </div>
                        <div class="input-group">
                            <select name="industry" id="industry" class="form-control" required>
                                <option value="" disabled selected>Select Industry</option>
                                @foreach ($industries as $industry)
                                    <option value="{{ $industry->name }}">{{ $industry->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <a href="{{ route('org-industries.add') }}" class="btn btn-outline-info"
                                    id="addIndustryBtn">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" id="saveOrgBtn">Save Organization</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Open the modal for adding a new Organization
            $('#addOrgBtn').click(function() {
                $('#orgModalLabel').text('Add Organization');
                $('#orgForm').attr('action', '{{ route('organizations.store') }}');
                $('#orgForm').trigger('reset');
                $('#orgModal').modal('show');
            });

            // Open the modal for editing an existing Organization
            $('.editOrgBtn').click(function() {
                var orgId = $(this).data('id');
                var orgName = $(this).data('name');
                var full = $(this).data('full_form');
                var ind = $(this).data('industry');
                $('#orgModalLabel').text('Edit Organization');
                $('#orgForm').attr('action', '/admin/organizations/' + orgId);
                $('#update-method').html('<input type="hidden" name="_method" value="PUT">');
                $('#org_name').val(orgName);
                $('#full_form').val(full);
                $('#industry option[value="' + ind + '"]').prop('selected', true);
                $('#orgModal').modal('show');
            });
        });
    </script>
@endpush
