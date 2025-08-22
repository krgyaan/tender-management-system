@extends('layouts.app')
@section('page-title', 'Raise RFQs')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('rfq.index') }}" class="btn btn-primary btn-sm">View All RFQs</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="new-user-info">
                            <form method="POST" action="{{ route('rfq.store') }}" enctype="multipart/form-data"
                                class="needs-validation" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="tender_id">Tender Number:</label>
                                        @if (empty($tender))
                                            <select name="tender_id" class="form-control" id="tender_id" required>
                                                <option value="">Select Tender</option>
                                                @foreach ($allTenders as $ten)
                                                    <option value="{{ $ten->id }}">{{ $ten->tender_no }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="hidden" name="tender_id" class="form-control" id="tender_id"
                                                value="{{ $tender->id }}">
                                            <input type="text" name="tender_no" class="form-control" id="tender_no"
                                                value="{{ $tender->tender_no }}" readonly>
                                        @endif
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_id') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="tender_name">Tender Name:</label>
                                        <input type="text" name="tender_name" class="form-control" id="tender_name"
                                            value="{{ $tender ? $tender->tender_name : '' }}" readonly>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="organisation">Organisation:</label>
                                        <input type="text" name="organisation" class="form-control" id="organisation"
                                            value="{{ $tender ? $tender->organizations->name : '' }}" readonly>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('organisation') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="location">Location:</label>
                                        <input type="text" name="location" class="form-control" id="location"
                                            value="{{ $tender ? optional($tender->locations)->address : '' }}" readonly>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('location') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="item_name">Item Name:</label>
                                        <input type="text" name="item_name" class="form-control" id="item_name"
                                            value="{{ $tender ? optional($tender->itemName)->name : '' }}" readonly>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('item_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="unit">Due Date:</label>
                                        <input type="datetime-local" name="due_date" id="due_date" class="form-control"
                                            value="{{ $tender ? date('Y-m-d\TH:i', strtotime($tender->due_date . ' ' . $tender->due_time)) : '' }}" required>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('due_date') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="scope">Scope of Work:</label>
                                        <input type="file" name="scope[]" id="scope" multiple>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('scope') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="techical">Technical Specifications:</label>
                                        <input type="file" name="techical[]" id="techical" multiple>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('techical') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="boq">Detailed BOQ:</label>
                                        <input type="file" name="boq[]" id="boq" multiple>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('boq') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="maf">MAF format:</label>
                                        <input type="file" name="maf[]" id="maf">
                                        <small>
                                            Upload 1 supported files. Max 10 MB per file.
                                            <span class="text-danger">{{ $errors->first('maf') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="mii">MII Format:</label>
                                        <input type="file" name="mii[]" id="mii">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('mii') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="docs_list">
                                            Other Documents needed:
                                        </label>
                                        <textarea name="docs_list" id="docs_list" class="form-control" required></textarea>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('docs_list') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="items">Items:</label>
                                            <button type="button" class="btn btn-sm btn-success" id="addItems">
                                                <i class="fa fa-plus"></i> Add
                                            </button>
                                        </div>
                                        <table class="table-bordered" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>Requirement</th>
                                                    <th>Unit</th>
                                                    <th>Quantity</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="items">
                                                @if (optional($tender)->rfqs && $tender->rfqs->requirements)
                                                    @foreach ($tender->rfqs->requirements as $item)
                                                        <tr>
                                                            <td>
                                                                <input type="text"
                                                                    name="req[{{ $loop->index }}][item]" id="item"
                                                                    class="form-control" value="{{ $item->requirement }}"
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    name="req[{{ $loop->index }}][unit]" id="unit"
                                                                    class="form-control" value="{{ $item->unit }}"
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    name="req[{{ $loop->index }}][qty]" id="qty"
                                                                    class="form-control" value="{{ $item->qty }}"
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <button type="button" data-id="{{ $item->id }}"
                                                                    class="btn btn-sm btn-danger remove-item">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('items') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-12 border py-2">
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="form-label">Vendors Selection</label>
                                            <button type="button" class="btn btn-sm btn-success" id="addVendorRow">
                                                <i class="fa fa-plus"></i> Add Vendor
                                            </button>
                                        </div>
                                        <div id="vendorRows">
                                            <div class="row vendor-row" data-row="0">
                                                <div class="col-md-4">
                                                    <div class="table-responsive">
                                                        <table class="table-borderless" style="width: 100%">
                                                            <tr>
                                                                <td>
                                                                    <label class="form-label"
                                                                        for="org_0">Organisation</label>
                                                                    <select name="vendor[0][org]" id="org_0"
                                                                        class="form-control org-select" required>
                                                                        <option value="">Select</option>
                                                                        @foreach ($orgs as $org)
                                                                            <option value="{{ $org->id }}">
                                                                                {{ $org->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="vendor-list mt-4" id="vendor_list_0"></div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button"
                                                        class="btn mt-4 btn-sm btn-danger remove-vendor-row"
                                                        style="display: none;">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label class="form-label" for="initiate_followup">Initiate Followup:</label>
                                        <select name="initiate_followup" id="initiate_followup" class="form-control"
                                            required>
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 frequency" style="display: none">
                                        <div class="form-group">
                                            <label class="form-label" for="frequency">Followup Frequency:</label>
                                            <select name="frequency" id="frequency" class="form-control">
                                                <option value="">choose</option>
                                                <option value="1">Daily</option>
                                                <option value="2">Alternate Days</option>
                                                <option value="3">Weekly(every Monday)</option>
                                                <option value="4">Stop</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 stop" style="display: none">
                                        <div class="form-group">
                                            <label class="form-label" for="stop_reason">Why Stop:</label>
                                            <select name="stop_reason" class="form-control" id="stop_reason">
                                                <option value="">choose</option>
                                                <option value="1" style="word-break: break-all">
                                                    The person is getting angry or has requested to stop
                                                </option>
                                                <option value="2">Followup Objective achieved</option>
                                                <option value="3">Remarks</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 stop_proof" style="display: none">
                                        <div class="form-group">
                                            <label class="form-label">Please give proof:</label>
                                            <textarea name="proof_text" class="form-control mb-2" id="proof_text"></textarea>
                                            <input type="file" name="proof_img" class="form-control mt-2"
                                                id="proof_img">
                                        </div>
                                    </div>
                                    <div class="col-md-4 stop_rem" style="display: none">
                                        <div class="form-group">
                                            <label class="form-label">Write Remarks:</label>
                                            <textarea name="stop_rem" class="form-control" id="stop_rem"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Submit RFQ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        th,
        td {
            padding: 8px;
        }

        .select2-selection,
        .select2-selection__choice {
            background: transparent !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#initiate_followup').on('change', function() {
                if ($(this).val() == '1') {
                    $('.frequency').show();
                } else {
                    $('.frequency').hide();
                }
            });
            $("select[name='frequency']").on('change', function() {
                if ($(this).val() == '4') {
                    $('.stop').show();
                } else {
                    $('.stop').hide();
                }
            });
            $("select[name='stop_reason']").on('change', function() {
                if ($(this).val() == '2') {
                    $('.stop_proof').show();
                } else {
                    $('.stop_proof').hide();
                }
                if ($(this).val() == '3') {
                    $('.stop_rem').show();
                } else {
                    $('.stop_rem').hide();
                }
            });

            // add requirement items
            let j = {{ optional(optional($tender)->rfqs, function ($t) {return optional($t->requirements)->count();}) ?? 0 }};
            $('#addItems').click(function() {
                let html = `
                <tr>
                    <td>
                        <input type="text" name="req[${j}][item]" id="item"
                            class="form-control" required></input>
                    </td>
                    <td>
                        <input type="text" name="req[${j}][unit]" id="unit"
                            class="form-control" required>
                    </td>
                    <td>
                        <input type="text" name="req[${j}][qty]" id="qty"
                            class="form-control" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-item">
                            <i class="fa fa-times"></i>
                        </button>
                    </td>
                </tr>
                `;
                $('#items').append(html);
                j++;
            });

            // remove requirement items
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
            });

            // Ajax for getting vendor details
            $(document).on('change', '.vendor_name', function() {
                var id = $(this).val();
                var rowIndex = $(this).attr('id').split('_')[2];
                $.ajax({
                    type: "GET",
                    url: "{{ route('getVendorDetails') }}",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        console.log(response);
                        $('#vendor_email_' + rowIndex).val(response.email);
                        $('#vendor_mobile_' + rowIndex).val(response.mobile);
                        $('#org_name_' + rowIndex).val(response.org);
                    }
                });
            });
            // Ajax for getting details for tender
            $(document).on('change', '#tender_id', function() {
                var id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('getTenderDetails') }}",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        console.log(response);
                        $('#tender_name').val(response.name);
                        $('#organisation').val(response.organisation);
                        $('#location').val(response.location);
                        $('#item_name').val(response.item);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            FilePond.registerPlugin(FilePondPluginFileValidateType);

            $('#techical').filepond({
                allowMultiple: true,
                credits: false,
                storeAsFile: true,
                maxFiles: '5',
                maxTotalFileSize: '25MB',
                required: true,
                acceptedFileTypes: [
                    'image/*',
                    'text/plain',
                    'application/doc',
                    'application/pdf',
                    'presentation/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                ],

                fileValidateTypeLabelExpectedTypesMap: {
                    'application/doc': '.doc',
                    'application/pdf': '.pdf',
                    'presentation/*': '.ppt',
                    'application/msword': '.doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': '.docx',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': '.pptx',
                },
                fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}'
            });
            $('#boq').filepond({
                allowMultiple: true,
                credits: false,
                storeAsFile: true,
                maxFiles: '5',
                required: true,
                acceptedFileTypes: [
                    'image/*',
                    'text/plain',
                    'application/doc',
                    'application/pdf',
                    'presentation/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                ],

                fileValidateTypeLabelExpectedTypesMap: {
                    'application/doc': '.doc',
                    'application/pdf': '.pdf',
                    'presentation/*': '.ppt',
                    'application/msword': '.doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': '.docx',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': '.pptx',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': '.xlsx',
                    'application/vnd.ms-excel': '.xls',
                },
                fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}'
            });
            $('#scope').filepond({
                allowMultiple: true,
                credits: false,
                storeAsFile: true,
                maxFiles: '5',
                maxTotalFileSize: '25MB',
                acceptedFileTypes: [
                    'image/*',
                    'text/plain',
                    'application/doc',
                    'application/pdf',
                    'presentation/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                ],

                fileValidateTypeLabelExpectedTypesMap: {
                    'application/doc': '.doc',
                    'application/pdf': '.pdf',
                    'presentation/*': '.ppt',
                    'application/msword': '.doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': '.docx',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': '.pptx',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': '.xlsx',
                    'application/vnd.ms-excel': '.xls',
                },
                fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}'
            });
            $('#maf').filepond({
                credits: false,
                storeAsFile: true,
                maxTotalFileSize: '25MB',
                acceptedFileTypes: [
                    'image/*',
                    'text/plain',
                    'application/doc',
                    'application/pdf',
                    'presentation/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                ],

                fileValidateTypeLabelExpectedTypesMap: {
                    'application/doc': '.doc',
                    'application/pdf': '.pdf',
                    'presentation/*': '.ppt',
                    'application/msword': '.doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': '.docx',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': '.pptx',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': '.xlsx',
                    'application/vnd.ms-excel': '.xls',
                },
                fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}'
            });
            $('#mii').filepond({
                credits: false,
                storeAsFile: true,
                maxTotalFileSize: '25MB',
                acceptedFileTypes: [
                    'image/*',
                    'text/plain',
                    'application/doc',
                    'application/pdf',
                    'presentation/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                ],

                fileValidateTypeLabelExpectedTypesMap: {
                    'application/doc': '.doc',
                    'application/pdf': '.pdf',
                    'presentation/*': '.ppt',
                    'application/msword': '.doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': '.docx',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': '.pptx',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': '.xlsx',
                    'application/vnd.ms-excel': '.xls',
                },
                fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}'
            });
        });

        // Add this to your existing
        $(document).ready(function() {
            let vendorRowCount = 0;

            // Function to get vendors by organization
            function getVendorsByOrg(orgId, rowId) {
                $.ajax({
                    url: "{{ route('getVendorsByOrg') }}", // Create this route
                    type: "GET",
                    data: {
                        org_id: orgId
                    },
                    success: function(response) {
                        let html = `
                            <div class="table-responsive">
                                <table class="table-borderless" style="width: 100%">
                                    <tr>
                                        <td>
                                            <select name="vendor[${rowId}][vendors][]" class="form-control mt-3 vendors-select" multiple>
                                                ${response.map(vendor => `
                                                                        <option value="${vendor.id}">${vendor.name} (${vendor.email})</option>
                                                                    `).join('')}
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        `;
                        $(`#vendor_list_${rowId}`).html(html);
                        $(`.vendors-select`).select2({
                            placeholder: "Select vendors",
                            allowClear: true
                        });
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

            // Add new vendor row
            $('#addVendorRow').click(function() {
                vendorRowCount++;
                let newRow = `
                    <div class="row vendor-row" data-row="${vendorRowCount}">
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table-borderless" style="width: 100%">
                                    <tr>
                                        <td>
                                            <label class="form-label" for="org_${vendorRowCount}">Organisation</label>
                                            <select name="vendor[${vendorRowCount}][org]" id="org_${vendorRowCount}" class="form-control org-select" required>
                                                <option value="">Select</option>
                                                @foreach ($orgs as $org)
                                                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="vendor-list mt-4" id="vendor_list_${vendorRowCount}"></div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn mt-4 btn-sm btn-danger remove-vendor-row">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#vendorRows').append(newRow);
                $('.remove-vendor-row').show();
            });

            // Remove vendor row
            $(document).on('click', '.remove-vendor-row', function() {
                $(this).closest('.vendor-row').remove();
                if ($('.vendor-row').length === 1) {
                    $('.remove-vendor-row').hide();
                }
            });

            // Handle organization change
            $(document).on('change', '.org-select', function() {
                let orgId = $(this).val();
                let rowId = $(this).closest('.vendor-row').data('row');
                if (orgId) {
                    getVendorsByOrg(orgId, rowId);
                } else {
                    $(`#vendor_list_${rowId}`).html('');
                }
            });
        });
    </script>
@endpush
