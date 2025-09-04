@extends('layouts.app')
@section('page-title', 'Vendors')
@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="card">
                <div class="card-body">
                    @include('partials.messages')
                    <div class="table-responsive">
                        <table class="table" id="vendors table">
                            <thead class="">
                                <tr>
                                    <th>Organisation Name</th>
                                    <th>All Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vendors as $v)
                                    <tr>
                                        <td>{{ $v->name }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                @if (count($v->gsts) > 0)
                                                    <button class="btn btn-outline-info btn-sm"
                                                        data-vendors="{{ $v->gsts }}" data-bs-toggle="modal"
                                                        data-bs-target="#vendorGstModal">
                                                        See GSTs ({{ count($v->gsts) }})
                                                    </button>
                                                @endif
                                                @if (count($v->accounts) > 0)
                                                    <button class="btn btn-outline-info btn-sm"
                                                        data-vendors="{{ $v->accounts }}" data-bs-toggle="modal"
                                                        data-bs-target="#vendorAccountModal">
                                                        See Accounts ({{ count($v->accounts) }})
                                                    </button>
                                                @endif
                                                @if (count($v->vendors) > 0)
                                                    <button class="btn btn-outline-info btn-sm"
                                                        data-vendors="{{ $v->vendors }}" data-bs-toggle="modal"
                                                        data-bs-target="#vendorListModal">
                                                        See Vendors ({{ count($v->vendors) }})
                                                    </button>
                                                @endif
                                                @if (count($v->files) > 0)
                                                    <button class="btn btn-outline-info btn-sm"
                                                        data-files="{{ $v->files }}" data-bs-toggle="modal"
                                                        data-bs-target="#vendorFileShowModal">
                                                        See Files ({{ count($v->files) }})
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                data-vendors="{{ $v->id }}" data-bs-toggle="modal"
                                                data-bs-target="#vendorFileModal">
                                                Add Files
                                            </button>
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

    <!-- Vendor Files Modal -->
    <div class="modal fade" id="vendorFileShowModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="vendorFileShowModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title" id="vendorFileShowModalLabel">Vendor Files</h5>
                    <button type="button" class="btn btn-xs btn-outline-danger" data-bs-dismiss="modal" aria-label="Close">
                        <span class=" fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table-bordered" style="width: 100%">
                            <thead class="">
                                <tr>
                                    <th>File Path</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="vendorFiles">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor List Modal -->
    <div class="modal fade" id="vendorListModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="vendorListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title" id="vendorListModalLabel">Vendor List</h5>
                    <button type="button" class="btn btn-xs btn-outline-danger" data-bs-dismiss="modal" aria-label="Close">
                        <span class=" fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table-bordered" style="width: 100%">
                            <thead class="">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tbody id="vendorList">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor GST Modal -->
    <div class="modal fade" id="vendorGstModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="vendorGstModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title" id="vendorGstModalLabel">Vendor GST</h5>
                    <button type="button" class="btn btn-xs btn-outline-danger" data-bs-dismiss="modal" aria-label="Close">
                        <span class=" fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table-bordered" style="width: 100%">
                            <thead class="">
                                <tr>
                                    <th>State</th>
                                    <th>GST Number</th>
                                </tr>
                            </thead>
                            <tbody id="vendorGst">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor Account Modal -->
    <div class="modal fade" id="vendorAccountModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="vendorAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title" id="vendorAccountModalLabel">Vendor Account</h5>
                    <button type="button" class="btn btn-xs btn-outline-danger" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span class=" fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table-bordered" style="width: 100%">
                            <thead class="">
                                <tr>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>Account IFSC</th>
                                </tr>
                            </thead>
                            <tbody id="vendorAccount">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Vendor Files Modal -->
    <div class="modal fade" id="vendorFileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="vendorFileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title" id="vendorFileModalLabel"></h5>
                    <button type="button" class="btn btn-xs btn-outline-danger" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span class=" fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="vendorForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <span id="update-method"></span>
                    <div class="modal-body row">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-info btn-sm" id="addGstRow">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table-bordered table-info" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Upload File</th>
                                        </tr>
                                    </thead>
                                    <tbody id="files">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" id="saveFileBtn">Save Files</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // pass vendors to vendorListModal
        $('#vendorListModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var vendors = button.data('vendors');
            var modal = $(this);
            var vendorList = '';
            vendors.forEach(ve => {
                vendorList += `
                        <tr>
                            <td>${ve.name}</td>
                            <td>${ve.email}</td>
                            <td>${ve.mobile}</td>
                            <td>${ve.address}</td>
                        </tr>
                    `;
            });
            $('#vendorList').html(vendorList);
        });
        // pass vendors to vendorGstModal
        $('#vendorGstModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var vendors = button.data('vendors');
            var modal = $(this);
            var vendorGst = '';
            vendors.forEach(ve => {
                vendorGst += `
                        <tr>
                            <td>${ve.gst_state}</td>
                            <td>${ve.gst_num}</td>
                        </tr>
                    `;
            });
            $('#vendorGst').html(vendorGst);
        });
        // pass vendors to vendorAccountModal
        $('#vendorAccountModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var vendors = button.data('vendors');
            var modal = $(this);
            var vendorAccount = '';
            vendors.forEach(ve => {
                vendorAccount += `
                        <tr>
                            <td>${ve.account_name}</td>
                            <td>${ve.account_num}</td>
                            <td>${ve.account_ifsc}</td>
                        </tr>
                    `;
            });
            $('#vendorAccount').html(vendorAccount);
        })
        // pass files to vendorFileShowModal
        $('#vendorFileShowModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var files = button.data('files');
            var fileRows = '';

            if (Array.isArray(files)) {
                files.forEach(file => {
                    fileRows += `
                <tr>
                    <td><a href="/uploads/vendors/${file.file_path}" target="_blank">${file.name}</a></td>
                    <td>
                        <a href="/uploads/vendors/${file.file_path}" Download class="btn btn-sm btn-primary" target="_blank">Download</a>
                    </td>
                </tr>
            `;
                });
            }

            $('#vendorFiles').html(fileRows);
        });


        document.addEventListener('DOMContentLoaded', function() {
            let currentVendorId = null;

            // Add new file upload row
            document.getElementById('addGstRow').addEventListener('click', function() {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td><input type="text" name="file_names[]" class="form-control" placeholder="File Name" required></td>
                <td><input type="file" name="files[]" class="form-control" required></td>
            `;
                document.getElementById('files').appendChild(row);
            });

            // Set vendor ID when "Add Files" button is clicked
            document.querySelectorAll('button[data-bs-target="#vendorFileModal"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentVendorId = this.getAttribute('data-vendors');
                    document.getElementById('vendorForm').action =
                        `/vendors/${currentVendorId}/files`; // update with correct route
                    document.getElementById('files').innerHTML = ''; // clear previous rows
                    document.getElementById('vendorFileModalLabel').textContent =
                        'Upload Files for Vendor ID ' + currentVendorId;
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        th,
        td {
            padding: 8px;
            font-size: medium;
        }
    </style>
@endpush
