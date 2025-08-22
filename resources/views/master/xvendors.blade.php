@extends('layouts.app')

@section('page-title', 'Vendors')

@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary" id="addVendorBtn">Create New Vendor</button>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('partials.messages')
                    <div class="table-responsive">
                        <table class="table" id="vendors table">
                            <thead class="">
                                <tr>
                                    <th>S.No.</th>
                                    <th>Organisation</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vendors as $vendor)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $vendor->org }}</td>
                                        <td>{{ $vendor->name }}</td>
                                        <td>{{ $vendor->email }}</td>
                                        <td>{{ $vendor->mobile }}</td>
                                        <td>{{ $vendor->address }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm editVendorBtn"
                                                data-id="{{ $vendor->id }}" data-name="{{ $vendor->name }}" data-org="{{ $vendor->org }}" data-email="{{ $vendor->email }}" data-mobile="{{ $vendor->mobile }}" data-address="{{ $vendor->address }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <a href="#"
                                                onclick="event.preventDefault(); document.getElementById('deleteForm{{ $vendor->id }}').submit();"
                                                class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST"
                                                id="deleteForm{{ $vendor->id }}" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $vendor->id }}">
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

    <!-- Add/Edit Status Modal -->
    <div class="modal fade" id="vendorModal" tabindex="-1" role="dialog" aria-labelledby="vendorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title text-white" id="vendorModalLabel"></h5>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <span class="text-white fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="vendorForm" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                    @csrf
                    <span id="update-method"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="org">Organisation</label>
                            <input type="text" class="form-control" id="org" name="org" required>
                        </div>
                        <div class="form-group">
                            <label for="vendor_name">Name</label>
                            <input type="text" class="form-control" id="vendor_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="vendor_email">Email</label>
                            <input type="text" class="form-control" id="vendor_email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="vendor_mobile">Mobile</label>
                            <input type="number" class="form-control" id="vendor_mobile" name="mobile" required>
                        </div>
                        <div class="form-group">
                            <label for="vendor_address">Address (optional)</label>
                            <textarea class="form-control" id="vendor_address" name="address"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" id="saveVendorBtn">Save Vendor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
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
                var vendorOrg = $(this).data('org');
                var vendorName = $(this).data('name');
                var vendorEmail = $(this).data('email');
                var vendorMobile = $(this).data('mobile');
                var vendorAddress = $(this).data('address');
                $('#vendorModalLabel').text('Edit Vendor');
                $('#vendorForm').attr('action', '/admin/vendors/' + vendorId);
                $('#update-method').html('<input type="hidden" name="_method" value="PUT">');
                $('#org').val(vendorOrg);
                $('#vendor_name').val(vendorName);
                $('#vendor_email').val(vendorEmail);
                $('#vendor_mobile').val(vendorMobile);
                $('#vendor_address').val(vendorAddress);
                $('#vendorModal').modal('show');
            });
        });
    </script>
@endpush
