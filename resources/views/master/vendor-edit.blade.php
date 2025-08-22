@extends('layouts.app')
@section('page-title', 'Edit Raised RFQs')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('vendors.index') }}" class="btn btn-danger btn-sm">Back</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <form method="POST" action="{{ route('vendors.update', $vendor->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="org">Organisation</label>
                                    <input type="text" class="form-control" id="org" name="org"
                                        placeholder="Vendor Organisation Name" required value="{{ $vendor->name }}">
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
                                                @if ($vendor->gsts)
                                                    @foreach ($vendor->gsts as $gst)
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="gsts[{{ $loop->index }}][gst_state]"
                                                                    placeholder="GST State" value="{{ $gst->gst_state }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="gsts[{{ $loop->index }}][gst_num]"
                                                                    placeholder="GST Number" value="{{ $gst->gst_num }}">
                                                            </td>
                                                            <td>
                                                                <button type="button" data-gid="{{ $gst->id }}"
                                                                    id="removeGst" class="btn btn-danger btn-xs">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12 text-end mt-3">
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
                                                @if ($vendor->accounts)
                                                    @foreach ($vendor->accounts as $account)
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="accounts[{{ $loop->index }}][account_name]"
                                                                    placeholder="Account Name"
                                                                    value="{{ $account->account_name }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="accounts[{{ $loop->index }}][account_num]"
                                                                    placeholder="Account Number"
                                                                    value="{{ $account->account_num }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="accounts[{{ $loop->index }}][account_ifsc]"
                                                                    placeholder="Account IFSC"
                                                                    value="{{ $account->account_ifsc }}">
                                                            </td>
                                                            <td>
                                                                <button type="button" data-aid="{{ $account->id }}"
                                                                    id="removeAcc" class="btn btn-danger btn-xs">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12 text-end mt-3">
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
                                                @if ($vendor->vendors)
                                                    @foreach ($vendor->vendors as $vendor)
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="vendor[{{ $loop->index }}][name]"
                                                                    placeholder="Vendor Name" value="{{ $vendor->name }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="vendor[{{ $loop->index }}][email]"
                                                                    placeholder="Vendor Email"
                                                                    value="{{ $vendor->email }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="vendor[{{ $loop->index }}][mobile]"
                                                                    placeholder="Vendor Mobile"
                                                                    value="{{ $vendor->mobile }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="vendor[{{ $loop->index }}][address]"
                                                                    placeholder="Vendor Address"
                                                                    value="{{ $vendor->address }}">
                                                            </td>
                                                            <td>
                                                                <button type="button" data-vid="{{ $vendor->id }}"
                                                                    id="removeVendor" class="btn btn-danger btn-sm">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
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
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // Counter variables for unique IDs
            let accountCounter = $('#accounts tr').length || 0;
            let gstCounter = $('#gsts tr').length || 0;
            let vendorCounter = $('#vendors tr').length || 0;

            // Add new row for account
            $('#addAccRow').click(function() {
                const newRow = `
                <tr>
                    <td>
                        <input type="text" class="form-control"
                            name="accounts[${accountCounter}][account_name]"
                            placeholder="Account Name" required>
                    </td>
                    <td>
                        <input type="text" class="form-control"
                            name="accounts[${accountCounter}][account_num]"
                            placeholder="Account Number" required>
                    </td>
                    <td>
                        <input type="text" class="form-control"
                            name="accounts[${accountCounter}][account_ifsc]"
                            placeholder="Account IFSC" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                $('#accounts').append(newRow);
                accountCounter++;
            });

            // Add new row for GST
            $('#addGstRow').click(function() {
                const newRow = `
                <tr>
                    <td>
                        <input type="text" class="form-control"
                            name="gsts[${gstCounter}][gst_state]"
                            placeholder="GST State" required>
                    </td>
                    <td>
                        <input type="text" class="form-control"
                            name="gsts[${gstCounter}][gst_num]"
                            placeholder="GST Number" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                $('#gsts').append(newRow);
                gstCounter++;
            });

            // Add new row for vendor
            $('#addVendorRow').click(function() {
                const newRow = `
                <tr>
                    <td>
                        <input type="text" class="form-control"
                            name="vendor[${vendorCounter}][name]"
                            placeholder="Vendor Name" required>
                    </td>
                    <td>
                        <input type="text" class="form-control"
                            name="vendor[${vendorCounter}][email]"
                            placeholder="Vendor Email" required>
                    </td>
                    <td>
                        <input type="text" class="form-control"
                            name="vendor[${vendorCounter}][mobile]"
                            placeholder="Vendor Mobile" required>
                    </td>
                    <td>
                        <textarea class="form-control"
                            name="vendor[${vendorCounter}][address]"
                            placeholder="Vendor Address"></textarea>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                $('#vendors').append(newRow);
                vendorCounter++;
            });

            // Universal remove row handler
            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
            });

            // Delete existing records with AJAX
            $(document).on('click', '#removeAcc', function() {
                const accountId = $(this).data('aid');
                const row = $(this).closest('tr');

                if (accountId) {
                    if (confirm('Are you sure you want to delete this account?')) {
                        $.ajax({
                            // url: `/vendors/delete-account/${accountId}`,
                            url: "{{ route('vendors.delete-account', '') }}/" + accountId,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                row.remove();
                                console.log(response);
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                            }
                        });
                    }
                } else {
                    row.remove();
                }
            });

            $(document).on('click', '#removeGst', function() {
                const gstId = $(this).data('gid');
                const row = $(this).closest('tr');

                if (gstId) {
                    if (confirm('Are you sure you want to delete this GST record?')) {
                        $.ajax({
                            // url: `/vendors/delete-gst/${gstId}`,
                            url: "{{ route('vendors.delete-gst', '') }}/" + gstId,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                row.remove();
                                console.log(response);

                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                            }
                        });
                    }
                } else {
                    row.remove();
                }
            });

            $(document).on('click', '#removeVendor', function() {
                const vendorId = $(this).data('vid');
                const row = $(this).closest('tr');

                if (vendorId) {
                    if (confirm('Are you sure you want to delete this vendor contact?')) {
                        $.ajax({
                            // url: `/vendors/delete-contact/${vendorId}`,
                            url: "{{ route('vendors.delete-contact', '') }}/" + vendorId,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                row.remove();
                                console.log(response);
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                            }
                        });
                    }
                } else {
                    row.remove();
                }
            });
        });
    </script>
@endpush
