@extends('layouts.app')
@section('page-title', 'Edit Raised RFQs')
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
                            <form method="POST" action="{{ route('rfq.update', $rfq->id) }}" enctype="multipart/form-data" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="tender_id">Tender Number:</label>
                                        <select name="tender_id" id="tender_id" class="form-control">
                                            <option value="">Select Tender</option>
                                            @foreach ($tenders as $tender)
                                                <option {{ $rfq->tender_id == $tender->id ? 'selected' : '' }}
                                                    value="{{ $tender->id }}">
                                                    {{ $tender->tender_no }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_id') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="tender_name">Tender Name:</label>
                                        <input type="text" name="tender_name" class="form-control" id="tender_name"
                                            value="{{ $rfq->tender->tender_name ?? '' }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="team_name">Team Name:</label>
                                        <input type="text" name="team_name" class="form-control" id="team_name"
                                            value="{{ $rfq->team_name ?? '' }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('team_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="organisation">Organisation:</label>
                                        <input type="text" name="organisation" class="form-control" id="organisation"
                                            value="{{ $rfq->organisation ?? '' }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('organisation') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="location">Location:</label>
                                        <input type="text" name="location" class="form-control" id="location"
                                            value="{{ $rfq->location ?? '' }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('location') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="item_name">Item Name:</label>
                                        <input type="text" name="item_name" class="form-control" id="item_name"
                                            value="{{ $rfq->item_name ?? '' }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('item_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="techical">Technical Specifications:</label>
                                        <input type="file" name="techical[]" id="techical" multiple>
                                        <small>
                                            Upload up to 5 supported files. Max 10 MB per file.
                                            <span class="text-danger">{{ $errors->first('techical') }}</span>
                                        </small>
                                        <ol class="py-2 techical">
                                            @if ($rfq->technicals->count() > 0)
                                                @foreach ($rfq->technicals as $technical)
                                                    <li>
                                                        <a href="{{ asset('uploads/rfqdocs/' . $technical->name) }}"
                                                            target="_blank">
                                                            {{ explode('.', $technical->name)[1] }}
                                                        </a>

                                                        <button type="button" class="btn text-danger" id="rm_techical"
                                                            data-id="{{ $technical->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ol>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="boq">Detailed BOQ:</label>
                                        <input type="file" name="boq[]" id="boq" multiple>
                                        <small>
                                            Upload up to 5 supported files. Max 10 MB per file.
                                            <span class="text-danger">{{ $errors->first('boq') }}</span>
                                        </small>
                                        <ol class="py-2 boq">
                                            @if ($rfq->boqs->count() > 0)
                                                @foreach ($rfq->boqs as $boq)
                                                    <li>
                                                        <a href="{{ asset('uploads/rfqdocs/' . $boq->name) }}"
                                                            target="_blank">
                                                            {{ explode('.', $boq->name)[1] }}
                                                        </a>

                                                        <button type="button" class="btn text-danger" id="rm_boq"
                                                            data-id="{{ $boq->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ol>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="scope">Scope of Work:</label>
                                        <input type="file" name="scope[]" id="scope" multiple>
                                        <small>
                                            Upload up to 5 supported files. Max 10 MB per file.
                                            <span class="text-danger">{{ $errors->first('scope') }}</span>
                                        </small>
                                        <ol class="py-2 scope">
                                            @if ($rfq->scopes->count() > 0)
                                                @foreach ($rfq->scopes as $scope)
                                                    <li>
                                                        <a href="{{ asset('uploads/rfqdocs/' . $scope->name) }}"
                                                            target="_blank">
                                                            {{ explode('.', $scope->name)[1] }}
                                                        </a>

                                                        <button type="button" class="btn text-danger" id="rm_scope"
                                                            data-id="{{ $scope->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ol>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="maf">MAF format:</label>
                                        <input type="file" name="maf[]" id="maf" multiple>
                                        <small>
                                            Upload 1 supported files. Max 10 MB per file.
                                            <span class="text-danger">{{ $errors->first('maf') }}</span>
                                        </small>
                                        <ol class="py-2 maf">
                                            @if ($rfq->mafs->count() > 0)
                                                @foreach ($rfq->mafs as $maf)
                                                    <li>
                                                        <a href="{{ asset('uploads/rfqdocs/' . $maf->name) }}"
                                                            target="_blank">
                                                            {{ explode('.', $maf->name)[1] }}
                                                        </a>

                                                        <button type="button" class="btn text-danger" id="rm_maf"
                                                            data-id="{{ $maf->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ol>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="mii">MII Format:</label>
                                        <input type="file" name="mii[]" id="mii" multiple>
                                        <small>
                                            Upload 1 supported files. Max 10 MB per file.
                                            <span class="text-danger">{{ $errors->first('mii') }}</span>
                                        </small>
                                        <ol class="py-2 mii">
                                            @if ($rfq->miis->count() > 0)
                                                @foreach ($rfq->miis as $mii)
                                                    <li>
                                                        <a href="{{ asset('uploads/rfqdocs/' . $mii->name) }}"
                                                            target="_blank">
                                                            {{ explode('.', $mii->name)[1] }}
                                                        </a>

                                                        <button type="button" class="btn text-danger" id="rm_mii"
                                                            data-id="{{ $mii->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ol>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="docs_list">
                                            List of Docs Needed from Manufacturer:
                                        </label>
                                        <textarea name="docs_list" id="docs_list" class="form-control">{{ $rfq->docs_list ?? '' }}</textarea>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('docs_list') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="requirements">Requirement:</label>
                                        <textarea name="requirements" id="requirements" class="form-control">{{ $rfq->requirements ?? '' }}</textarea>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('requirements') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="unit">Unit:</label>
                                        <input type="text" name="unit" id="unit" class="form-control"
                                            value="{{ $rfq->unit ?? '' }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('unit') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="qty">Quantity:</label>
                                        <input type="text" name="qty" id="qty" class="form-control"
                                            value="{{ $rfq->qty ?? '' }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('qty') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="unit">Due Date:</label>
                                        <input type="text" name="due_date" id="due_date" class="form-control"
                                            value="{{ $rfq->due_date ?? '' }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('due_date') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="items">Vendor Details:</label>
                                            <button type="button" class="btn btn-sm btn-primary" id="addVendors">
                                                <i class="fa fa-plus"></i> Add
                                            </button>
                                        </div>
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>Vendor Name</th>
                                                    <th>Email</th>
                                                    <th>Mobile</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="vendors">
                                                @foreach ($rfq->rfqVendors as $vendor)
                                                    <tr>
                                                        <td>
                                                            <select name="vendor[{{ $loop->iteration }}][name]"
                                                                class="form-control vendor_name"id="vendor_name_{{ $loop->iteration }}">
                                                                <option value="">Select Vendor Name</option>
                                                                @foreach ($vendors as $v)
                                                                    <option value="{{ $v->id }}"
                                                                        {{ $vendor->vendor == $v->id ? 'selected' : '' }}>
                                                                        {{ $v->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                name="vendor[{{ $loop->iteration }}][email]"
                                                                id="vendor_email_{{ $loop->iteration }}"
                                                                class="form-control" value="{{ $vendor->email }}">
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                name="vendor[{{ $loop->iteration }}][mobile]"
                                                                id="vendor_mobile_{{ $loop->iteration }}"
                                                                class="form-control" value="{{ $vendor->mobile }}">
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove_vendor"
                                                                id="remove_vendor" data-id="{{ $vendor->id }}">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('vendor') }}</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Update RFQ
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

@push('scripts')
    <script>
        $(document).ready(function() {
            var i = {{ count($rfq->rfqVendors) + 1 }};
            $('#addVendors').click(function() {
                var html = '';
                html += '<tr>';
                html += '<td>';
                html += '<select id="vendor_name_' + i + '" name="vendor[' + i +
                    '][name]" class="form-control vendor_name">';
                html += '<option value="">Select Vendor Name</option>';
                @foreach ($vendors as $vendor)
                    html += '<option value="{{ $vendor->id }}">{{ $vendor->name }}</option>';
                @endforeach
                html += '</select>';
                html += '</td>';
                html += '<td><input type="text" id="vendor_email_' + i + '" name="vendor[' + i +
                    '][email]" class="form-control"></td>';
                html += '<td><input type="text" id="vendor_mobile_' + i + '" name="vendor[' + i +
                    '][mobile]" class="form-control"></td>';
                html +=
                    '<td><button type="button" class="btn btn-danger btn-sm" id="remove_vendor"><i class="fa fa-minus"></i></button></td>';
                html += '</tr>';
                $('#vendors').append(html);
                i++;
            });

            $(document).on('click', '.remove_vendor', function() {
                if (confirm('Are you sure? This step is irreversible!')) {
                    let row = $(this).closest('tr');
                    let id = $(this).data('id');
                    if (id) {
                        $.ajax({
                            type: "DELETE",
                            url: '{{ route('deleteVendor', ':id') }}'.replace(':id', id),
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(response) {
                                console.log(response);
                                row.remove();
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                alert('An error occurred while deleting the vendor.');
                            }
                        });
                    } else {
                        row.remove();
                    }
                }
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
                        $('#tender_name').val(response.name);
                        $('#team_name').val(response.member);
                        $('#organisation').val(response.organisation);
                        $items = response.item;
                        $('#due_date').val(response.due_date);
                    }
                });
            });

            // Ajax for getting vendor details
            $(document).on('change', '.vendor_name', function() {
                var id = $(this).val();
                var rowIndex = $(this).attr('id').split('_')[2];
                $.ajax({
                    type: "GET",
                    url: "{{ route('getVendorDetails') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    success: function(response) {
                        $('#vendor_email_' + rowIndex).val(response.email);
                        $('#vendor_mobile_' + rowIndex).val(response.mobile);
                    }
                });
            });

            let techicalCnt = $('.techical li').length;
            let boqCnt = $('.boq li').length;
            let scopeCnt = $('.scope li').length;
            let mafCnt = $('.maf li').length;
            let miiCnt = $('.mii li').length;

            let teCnt = 5 - techicalCnt;
            let boCnt = 5 - boqCnt;
            let scCnt = 5 - scopeCnt;
            let maCnt = 1 - mafCnt;
            let miCnt = 1 - miiCnt;

            $('#techical').prop('disabled', techicalCnt >= 5)
            $('#boq').prop('disabled', boqCnt >= 5)
            $('#scope').prop('disabled', scopeCnt >= 5)
            $('#maf').prop('disabled', mafCnt >= 1)
            $('#mii').prop('disabled', miiCnt >= 1)

            FilePond.registerPlugin(FilePondPluginFileValidateType);

            $('#techical').filepond({
                allowMultiple: true,
                storeAsFile: true,
                maxFiles: teCnt,
                maxTotalFileSize: '25MB',
                credits: flase,
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
            $('#boq').filepond({
                allowMultiple: true,
                storeAsFile: true,
                maxFiles: boCnt,
                maxTotalFileSize: '25MB',
                credits: flase,
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
                storeAsFile: true,
                maxFiles: scCnt,
                maxTotalFileSize: '25MB',
                credits: flase,
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
                allowMultiple: true,
                storeAsFile: true,
                maxFiles: maCnt,
                maxTotalFileSize: '25MB',
                credits: flase,
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
                allowMultiple: true,
                storeAsFile: true,
                maxFiles: miCnt,
                maxTotalFileSize: '25MB',
                credits: flase,
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

            $(document).on('click', '#rm_techical', function() {
                if (confirm('Are You Sure? This step is irreversible!')) {
                    let id = $(this).data('id');
                    let li = $(this).closest('li');
                    if (id) {
                        $.ajax({
                            type: "DELETE",
                            url: '{{ route('delTechical', ':id') }}'.replace(':id', id),
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(response) {
                                console.log(response);
                                li.remove();
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                alert(
                                    'An error occurred while deleting the Technical Specification.'
                                );
                            }
                        });
                    }
                }
            });
            $(document).on('click', '#rm_boq', function() {
                if (confirm('Are You Sure? This step is irreversible!')) {
                    let id = $(this).data('id');
                    let li = $(this).closest('li');
                    if (id) {
                        $.ajax({
                            type: "DELETE",
                            url: '{{ route('delBoq', ':id') }}'.replace(':id', id),
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(response) {
                                console.log(response);
                                li.remove();
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                alert('An error occurred while deleting the BOQ.');
                            }
                        });
                    }
                }
            });
            $(document).on('click', '#rm_scope', function() {
                if (confirm('Are You Sure? This step is irreversible!')) {
                    let id = $(this).data('id');
                    let li = $(this).closest('li');
                    if (id) {
                        $.ajax({
                            type: "DELETE",
                            url: '{{ route('delScope', ':id') }}'.replace(':id', id),
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(response) {
                                console.log(response);
                                li.remove();
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                alert('An error occurred while deleting the Scope of Work.');
                            }
                        });
                    }
                }
            });
            $(document).on('click', '#rm_maf', function() {
                if (confirm('Are You Sure? This step is irreversible!')) {
                    let id = $(this).data('id');
                    let li = $(this).closest('li');
                    if (id) {
                        $.ajax({
                            type: "DELETE",
                            url: '{{ route('delMaf', ':id') }}'.replace(':id', id),
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(response) {
                                console.log(response);
                                li.remove();
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                alert('An error occurred while deleting the MAF.');
                            }
                        });
                    }
                }
            });
            $(document).on('click', '#rm_mii', function() {
                if (confirm('Are You Sure? This step is irreversible!')) {
                    let id = $(this).data('id');
                    let li = $(this).closest('li');
                    if (id) {
                        $.ajax({
                            type: "DELETE",
                            url: '{{ route('delMii', ':id') }}'.replace(':id', id),
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(response) {
                                console.log(response);
                                li.remove();
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                alert('An error occurred while deleting the MII.');
                            }
                        });
                    }
                }
            });

        });
    </script>
@endpush
