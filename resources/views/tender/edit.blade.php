@extends('layouts.app')
@section('page-title', 'Edit Tender')
@section('content')
    @php
        $roles = ['admin', 'coordinator', 'team-leader'];
        $isEditable = in_array(auth()->user()->role, $roles);
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('tender.index') }}" class="btn btn-primary btn-sm">View All Tenders</a>
                    <span class="badge bg-success">Completed {{ $tenderInfo->calculateTenderCompletion() }}%</span>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="new-user-info">
                            <form method="POST" action="{{ route('tender.update', $tenderInfo->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="form-label">Team Name:</label>
                                        <select name="team" class="form-control" id="team">
                                            <option value="" disabled>Select Team Name</option>
                                            @if (in_array(auth()->user()->role, ['admin', 'coordinator']))
                                                @foreach ($teams as $team)
                                                    <option value="{{ $team }}"
                                                        {{ old('team') || $tenderInfo->team == $team ? 'selected' : '' }}>
                                                        {{ $team }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="{{ auth()->user()->team }}" selected>
                                                    {{ auth()->user()->team }}
                                                </option>
                                            @endif
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('team') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label">Tender No:</label>
                                        <input type="text" name="tender_no" class="form-control" id="tender_no"
                                            placeholder="Tender No" value="{{ $tenderInfo->tender_no }}"
                                            {{ $isEditable ? '' : 'readonly' }}>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_no') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="tender_name">Tender Name:</label>
                                        <input type="text" name="tender_name" class="form-control" id="tender_name"
                                            placeholder="Tender Name" value="{{ $tenderInfo->tender_name }}" readonly>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="organisation">Organization:</label>
                                        <select name="organisation" class="form-control" id="organisation">
                                            <option value="">Select Organization</option>
                                            @foreach ($organisations as $organisation)
                                                <option value="{{ $organisation->id }}"
                                                    {{ $organisation->id == $tenderInfo->organisation ? 'selected' : '' }}>
                                                    {{ $organisation->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('organisation') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="gst">Tender Value (GST Inclusive):</label>
                                        <input type="number" name="gst" class="form-control" id="gst"
                                            min="0" step="0.01" placeholder="GST Value"
                                            value="{{ $tenderInfo->gst_values }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('gst') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="mobile">Tender Fee:</label>
                                        <input type="number" name="tender_fees" class="form-control" id="tender_fees"
                                            min="0" step="0.01" placeholder="Tender Fees"
                                            value="{{ $tenderInfo->tender_fees }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_fees') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="designation">EMD:</label>
                                        <input type="number" name="emd" class="form-control" id="emd"
                                            min="0" step="0.01" placeholder="Earnest Money Deposit"
                                            value="{{ $tenderInfo->emd }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('emd') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="address">Team Member:</label>
                                        <select name="team_member" class="form-control" id="team_member"
                                            {{ $isEditable ? '' : 'readonly' }}>
                                            <option value="{{ $tenderInfo->team_member }}" selected>
                                                @foreach ($users as $user)
                                                    @if ($user->id == $tenderInfo->team_member)
                                                        {{ $user->name }} ({{ $user->role }})
                                                    @endif
                                                @endforeach
                                            </option>
                                            @if ($isEditable)
                                                <option value="">Select</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ $user->id == $tenderInfo->team_member ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->role }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('team_member') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <div class="profile-img-edit position-relative">
                                            <label class="form-label" for="due_date">Due Date:</label>
                                            <div class="input-group">
                                                <input type="date" name="due_date" class="form-control"
                                                    id="due_date" value="{{ $tenderInfo->due_date }}">
                                                <input type="time" name="due_time" class="form-control"
                                                    id="due_time" value="{{ $tenderInfo->due_time }}">
                                            </div>
                                            <small>
                                                <span class="text-danger">{{ $errors->first('due_date') }}</span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 d-none">
                                        <label class="form-label" for="status">Status:</label>
                                        <select name="status" class="form-control" id="status">
                                            <option value="">Select Status</option>
                                            @foreach ($statuses as $status)
                                                <option {{ $status->id == $tenderInfo->status ? 'selected' : '' }}
                                                    value="{{ $status->id }}">
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('status') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="location">Location:</label>
                                        <select name="location" class="form-control" id="location">
                                            <option value="">Select Location</option>
                                            @foreach ($locations as $location)
                                                <option {{ $location->id == $tenderInfo->location ? 'selected' : '' }}
                                                    value="{{ $location->id }}">
                                                    {{ $location->address }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('location') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="website">Website:</label>
                                        <select name="website" class="form-control" id="website">
                                            <option value="">Select Website</option>
                                            @foreach ($websites as $website)
                                                <option {{ $website->id == $tenderInfo->website ? 'selected' : '' }}
                                                    value="{{ $website->id }}">
                                                    {{ $website->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('website') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="items">Item:</label>
                                        <select name="item" id="item" class="form-control">
                                            <option value="">Select Item Name</option>
                                            @foreach ($items as $item)
                                                <option {{ $tenderInfo->item == $item->id ? 'selected' : '' }}
                                                    value="{{ $item->id }}">
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('items') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="items">Upload Documents:</label>
                                        <input type="file" name="docs[]" id="docs" class="form-control"
                                            multiple>
                                        <div>
                                            <table
                                                class="table-bordered {{ $tenderInfo->docs->isEmpty() ? 'd-none' : '' }}">
                                                <thead>
                                                    <tr>
                                                        <th>File Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($tenderInfo->docs as $document)
                                                        <tr>
                                                            <td>
                                                                <a href="/uploads/docs/{{ $document->doc_path }}"
                                                                    target="_blank">
                                                                    {{ explode('_', $document->doc_path)[0] }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-xs"
                                                                    id="remove_docs" data-id="{{ $document->id }}">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="remark">Remark</label>
                                        <textarea class="form-control" name="remarks" id="remark" rows="4">{{ $tenderInfo->remarks }}</textarea>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('remarks') }}</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Update Tender Info
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
    </style>
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            function updateTenderName() {
                let organisation = $('#organisation').find('option:selected').text().trim();
                let itemName = $('#item').find('option:selected').text().trim();
                let location = $('#location').find('option:selected').text().trim();

                $('#tender_name').val(organisation + ' ' + itemName + ' ' + location);
            }

            $('#organisation').on('change', updateTenderName);
            $('#location').on('change', updateTenderName);
            $('#item').on('change', updateTenderName);

            FilePond.registerPlugin(FilePondPluginFileValidateType);
            FilePond.registerPlugin(FilePondPluginFileValidateSize);
            $('#docs').filepond({
                allowMultiple: true,
                storeAsFile: true,
                maxTotalFileSize: '25MB',
                credits: false,
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

            // Delete document
            $('#remove_docs').on('click', function() {
                var id = $(this).data('id');
                var row = $(this).closest('tr');
                if (!id) {
                    row.remove();
                } else {
                    $.ajax({
                        url: '{{ route('tender.doc.delete', ':id') }}'.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                row.remove();
                                console.log("Document deleted successfully.");
                            } else {
                                alert('Failed to delete document.');
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
