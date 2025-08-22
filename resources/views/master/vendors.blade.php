@extends('layouts.app')
@section('page-title', 'Vendors')
@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary" id="addVendorBtn">Create New Vendor</button>
                <a href="{{ route('vendors.export') }}" class="btn btn-success">Export to Excel</a>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('partials.messages')
                    <div class="table-responsive">
                        <table class="table" id="vendors table">
                            <thead class="">
                                <tr>
                                    <th>S.No.</th>
                                    <th>Organisation Name</th>
                                    <th>GST Numbers</th>
                                    <th>Accounts</th>
                                    <th>All Vendors</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vendors as $v)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $v->name }}</td>
                                        <td>
                                            @if (count($v->gsts) > 0)
                                                <button class="btn btn-outline-info btn-sm"
                                                    data-vendors="{{ $v->gsts }}" data-bs-toggle="modal"
                                                    data-bs-target="#vendorGstModal">
                                                    See GSTs
                                                </button>
                                            @endif
                                            <br><br>
                                            {{ 'Total Gsts: ' . count($v->gsts) }}
                                        </td>
                                        <td>
                                            @if (count($v->accounts) > 0)
                                                <button class="btn btn-outline-info btn-sm"
                                                    data-vendors="{{ $v->accounts }}" data-bs-toggle="modal"
                                                    data-bs-target="#vendorAccountModal">
                                                    See Accounts
                                                </button>
                                            @endif
                                            <br><br>
                                            {{ 'Total Accounts: ' . count($v->accounts) }}
                                        </td>
                                        <td>
                                            @if (count($v->vendors) > 0)
                                                <button class="btn btn-outline-info btn-sm"
                                                    data-vendors="{{ $v->vendors }}" data-bs-toggle="modal"
                                                    data-bs-target="#vendorListModal">
                                                    See Vendors
                                                </button>
                                            @endif
                                            <br><br>
                                            {{ 'Total Vendors: ' . count($v->vendors) }}
                                        </td>
                                        <td>
                                            <a href="{{ route('vendors.edit', $v->id) }}" type="button"
                                                class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
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

    <!-- Add Vendor Modal -->
    <div class="modal fade" id="vendorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="vendorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title" id="vendorModalLabel"></h5>
                    <button type="button" class="btn btn-xs btn-outline-danger" data-bs-dismiss="modal" aria-label="Close">
                        <span class=" fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="vendorForm" method="POST">
                    @csrf
                    <span id="update-method"></span>
                    <div class="modal-body row">
                        <div class="form-group col-md-6">
                            <label for="org">Organisation</label>
                            <input type="text" class="form-control" id="org" name="org"
                                placeholder="Vendor Organisation Name" required>
                        </div>
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
                                            <th>GST State</th>
                                            <th>GST Number</th>
                                        </tr>
                                    </thead>
                                    <tbody id="gsts">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-info btn-sm" id="addAccRow">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table-bordered table-primary" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>Account Name</th>
                                            <th>Account Number</th>
                                            <th>Account IFSC</th>
                                        </tr>
                                    </thead>
                                    <tbody id="accounts">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-info btn-sm" id="addVendorRow">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table-bordered" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="vendors">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" id="saveVendorBtn">Save Vendor</button>
                    </div>
                </form>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
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

            // Open the modal for adding a new status
            $('#addVendorBtn').click(function() {
                $('#vendorModalLabel').text('Add Vendor');
                $('#vendorForm').attr('action', '{{ route('vendors.store') }}');
                $('#vendorForm').trigger('reset');
                $('#vendorModal').modal('show');
            });

            // Open the modal for editing an existing vendor
            $('.editVendorBtn').click(function() {
                var vendorId = $(this).data('id');
                $('#vendorModalLabel').text('Edit Vendor');
                $('#vendorForm').attr('action', '/admin/vendors/' + vendorId);
                $('#update-method').html('<input type="hidden" name="_method" value="PUT">');
                $('#vendorModal').modal('show');
            });

            // Add new row for account
            let a = 1;
            $('#addAccRow').click(function() {
                var newRow = `
                    <tr>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" id="acc_name_${a}" name="accounts[${a}][account_name]" required>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" class="form-control" id="acc_num_${a}" name="accounts[${a}][account_num]" required>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" id="acc_ifsc_${a}" name="accounts[${a}][account_ifsc]" required>
                            </div>
                        </td>
                        <td>
                            <button type="button" id="removeAcc" class="btn btn-danger btn-xs">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#accounts').append(newRow);
                a++;
            });
            // Add new row for account
            let g = 1;
            $('#addGstRow').click(function() {
                var newRow = `
                    <tr>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" id="gst_name_${a}" name="gsts[${a}][gst_state]" required>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" class="form-control" id="gst_num_${g}" name="gsts[${g}][gst_num]" required>
                            </div>
                        </td>
                        <td>
                            <button type="button" id="removeAcc" class="btn btn-danger btn-xs">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#gsts').append(newRow);
                g++;
            });
            // Add new row for vendor
            let v = 1;
            $('#addVendorRow').click(function() {
                var newRow = `
                    <tr>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" id="vendor_name${v}" name="vendor[${v}][name]" required>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" id="vendor_email${v}" name="vendor[${v}][email]" required>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" class="form-control" id="vendor_mobile${v}" name="vendor[${v}][mobile]" required>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <textarea class="form-control" id="vendor_address${v}" name="vendor[${v}][address]"></textarea>
                            </div>
                        </td>
                        <td>
                            <button type="button" id="removeVendor" class="btn btn-danger btn-xs">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#vendors').append(newRow);
                v++;
            });

            // Remove account row
            $(document).on('click', '#removeAcc', function() {
                $(this).closest('tr').remove();
            });
            // Remove vendor row
            $(document).on('click', '#removeVendor', function() {
                $(this).closest('tr').remove();
            });
            // Remove gst row
            $(document).on('click', '#removeGst', function() {
                $(this).closest('tr').remove();
            });

            // on close, the modal should be reset
            $('#vendorModal').on('hidden.bs.modal', function() {
                $('#vendorForm').trigger('reset');
                $('#update-method').html('');
                $('#vendors').html('');
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
