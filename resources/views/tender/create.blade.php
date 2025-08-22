@extends('layouts.app')
@section('page-title', 'Create Tender')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('tender.index') }}" class="btn btn-primary btn-sm">View All Tenders</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')

                        <div class="new-user-info">
                            <form method="POST" action="{{ route('tender.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="form-label">Team Name:</label>
                                        <select name="team" class="form-control" id="team">
                                            <option value="" disabled>Select Team Name</option>
                                            @foreach ($teams as $team)
                                                <option value="{{ $team }}"
                                                    {{ old('team') || auth()->user()->team == $team ? 'selected' : 'disabled' }}>
                                                    {{ $team }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('team') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label">Tender No:</label>
                                        <input type="text" name="tender_no" class="form-control" id="tender_no"
                                            placeholder="Tender No" value="{{ old('tender_no') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_no') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="tender_name">Tender Name:</label>
                                        <input type="text" name="tender_name" class="form-control" id="tender_name"
                                            placeholder="Tender Name" readonly value="{{ old('tender_name') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="organisation">Organization:</label>
                                        <select name="organisation" class="form-control" id="organisation">
                                            <option value="">Select Organization</option>
                                            @foreach ($organisations as $organisation)
                                                <option {{ old('organisation') == $organisation->id ? 'selected' : '' }}
                                                    value="{{ $organisation->id }}">
                                                    {{ $organisation->name }}</option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('organisation') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="gst">Tender Value (GST Inclusive) :</label>
                                        <input type="number" name="gst" class="form-control" id="gst"
                                            min="0" step="0.01" placeholder="GST Value"
                                            value="{{ old('gst') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('gst') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="mobile">Tender Fee:</label>
                                        <input type="number" name="tender_fees" class="form-control" id="tender_fees"
                                            min="0" step="0.01" placeholder="Tender Fees"
                                            value="{{ old('tender_fees') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_fees') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="designation">EMD:</label>
                                        <input type="number" name="emd" class="form-control" id="emd"
                                            min="0" step="0.01" placeholder="Earnest Money Deposit"
                                            value="{{ old('emd') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('emd') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="address">Team Member:</label>
                                        <select name="team_member" class="form-control" id="team_member">
                                            <option value="">Select</option>
                                            @foreach ($users as $user)
                                                <option {{ old('team_member') == $user->id ? 'selected' : '' }}
                                                    value="{{ $user->id }}">
                                                    {{ $user->name }} ({{ $user->designation }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('team_member') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <div class="profile-img-edit position-relative">
                                            <label class="form-label" for="due_date">Due Date:</label>
                                            <div class="input-group">
                                                <input type="date" name="due_date" class="form-control" id="due_date"
                                                    value="{{ old('due_date') }}">
                                                <input type="time" name="due_time" class="form-control"
                                                    id="due_time" value="{{ old('due_time') }}">
                                            </div>
                                            <small>
                                                <span class="text-danger">{{ $errors->first('due_date') }}</span>
                                                <span class="text-danger">{{ $errors->first('due_time') }}</span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="location">Location:</label>
                                        <select name="location" class="form-control" id="location">
                                            <option value="">Select Location</option>
                                            @foreach ($locations as $loc)
                                                <option {{ old('location') == $loc->id ? 'selected' : '' }}
                                                    value="{{ $loc->id }}">
                                                    {{ $loc->address }}
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
                                                <option {{ old('website') == $website->id ? 'selected' : '' }}
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
                                        <label class="form-label" for="item">Item:</label>
                                        <select name="item" id="item" class="form-control">
                                            <option value="">Select Item Name</option>
                                            @foreach ($items as $item)
                                                <option {{ old('item') == $item->id ? 'selected' : '' }}
                                                    value="{{ $item->id }}">
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('item') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="items">Upload Documents:</label>
                                        <input type="file" name="docs[]" id="docs" class="form-control"
                                            multiple>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('docs[]') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="remark">Remark</label>
                                        <textarea class="form-control" name="remarks" id="remark" rows="4"></textarea>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('remark') }}</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" id="submit" name="submit" class="btn btn-primary">
                                        Add New Tender
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
            function updateTenderName() {
                let organisation = $('#organisation').find('option:selected').text().trim();
                let itemName = $('#item').find('option:selected').text().trim();
                let location = $('#location').find('option:selected').text().trim();

                // Send AJAX request to server
                $.ajax({
                    url: '/generate-tender-name',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        organisation: organisation,
                        item: itemName,
                        location: location
                    },
                    success: function(response) {
                        $('#tender_name').val(response.tender_name);
                    },
                    error: function(xhr) {
                        console.error('An error occurred while generating the tender name.');
                    }
                });
            }

            // Attach the event listeners
            $('#organisation').on('change', updateTenderName);
            $('#location').on('change', updateTenderName);
            $('#item').on('change', updateTenderName);

            FilePond.registerPlugin(FilePondPluginFileValidateType);
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
    </script>
@endpush
