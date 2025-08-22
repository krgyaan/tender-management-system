@extends('layouts.app')
@section('page-title', 'Courier Dispatch Form')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('courier.index') }}" class="btn btn-outline-danger btn-sm">Go Back</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="new-user-info">
                            <form method="POST" action="{{ route('courier.despatch', $id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="courier_provider">Courier Provider:</label>
                                        <input type="hidden" name="id" class="form-control"
                                            value="{{ $id }}">
                                        <input type="text" name="courier_provider" class="form-control"
                                            id="courier_provider" value="{{ old('courier_provider') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('courier_provider') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="pickup_date">Pickup date and time:</label>
                                        <input type="datetime-local" name="pickup_date" class="form-control" id="pickup_date"
                                            value="{{ old('pickup_date') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('pickup_date') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="docket_no">Docket No:</label>
                                        <input type="text" name="docket_no" class="form-control" id="docket_no"
                                            value="{{ old('docket_no') }}">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('docket_no') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="docket_slip">Docket Slip:</label>
                                        <input type="file" name="docket_slip" id="docket_slip" class="form-control">
                                        <small>
                                            <span class="text-danger">{{ $errors->first('docket_slip') }}</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit" class="btn btn-primary btn-sm">
                                        Submit
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
            FilePond.registerPlugin(FilePondPluginFileValidateType);
            FilePond.registerPlugin(FilePondPluginFileValidateSize);
            $('#docket_slip').filepond({
                allowMultiple: false,
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
