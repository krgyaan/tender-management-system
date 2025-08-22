@extends('layouts.app')
@section('page-title', 'Courier Form')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('courier.index') }}" class="btn btn-primary btn-sm">View All Couriers</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="new-user-info">
                            <form method="POST" action="{{ route('courier.store') }}" enctype="multipart/form-data" id="courier-form">
                                @csrf
                                <div class="row">
                                    <p class="form-label">Courier to:</p>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="to_org">Organization Name:</label>
                                        <input type="text" name="to_org" class="form-control" id="to_org"
                                            value="{{ old('to_org') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('org') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="to_name">Name:</label>
                                        <input type="text" name="to_name" class="form-control" id="to_name"
                                            value="{{ old('to_name') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('to_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="to_addr">Address:</label>
                                        <input type="text" name="to_addr" class="form-control" id="to_addr"
                                            value="{{ old('to_addr') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('to_addr') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="to_pin">Pin Code:</label>
                                        <input type="text" name="to_pin" class="form-control" id="to_pin"
                                            value="{{ old('to_pin') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('to_pin') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="to_mobile">Mobile Number:</label>
                                        <input type="text" name="to_mobile" class="form-control" id="to_mobile"
                                            value="{{ old('to_mobile') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('to_mobile') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="emp_from">Courier from:</label>
                                        <select name="emp_from" class="form-control" id="emp_from">
                                            <option value="">Select Employee</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}"
                                                    {{ old('emp_from') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }}</option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('emp_from') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="del_date">Expected Delivery Date:</label>
                                        <input type="date" name="del_date" class="form-control" id="del_date"
                                            value="{{ old('del_date') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('del_date') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="courier_docs">Soft Copy of the documents:</label>
                                        <input type="file" name="courier_docs[]" id="courier_docs" class="form-control"
                                            multiple>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('courier_docs') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="urgency">Dispatch Urgency:</label>
                                        <select name="urgency" class="form-control" id="urgency">
                                            <option value="">Select Urgency</option>
                                            <option value="1" {{ old('urgency') == '1' ? 'selected' : '' }}>
                                                Same Day (Urgent)
                                            </option>
                                            <option value="2" {{ old('urgency') == '2' ? 'selected' : '' }}>
                                                Next Day
                                            </option>
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('urgency') }}</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit" class="btn btn-primary" id="submit-btn">
                                        Submit Courier Request
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
            $('#courier-form').on('submit', function(e) {
                $('#submit-btn').prop('disabled', true);
            });
            
            FilePond.registerPlugin(FilePondPluginFileValidateType);
            FilePond.registerPlugin(FilePondPluginFileValidateSize);
            $('#courier_docs').filepond({
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
            })
        });
    </script>
@endpush
