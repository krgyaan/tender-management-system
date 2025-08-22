@extends('layouts.app')
@section('page-title', 'Physical Docs Submission')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('phydocs.index') }}" class="btn btn-primary btn-sm">View All Physical Docs</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="new-user-info">
                            <form method="POST" action="{{ route('phydocs.update', $info->id) }}"
                                enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="tender_id" class="form-label">Tender Name</label>
                                        <input type="hidden" name="tender_id" id="tender_id" value="{{ $info->tender_id }}"
                                            required>
                                        <input type="text" class="form-control" value="{{ $info->tender->tender_name }}"
                                            readonly>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('tender_id') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row" id="popfollowup">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <label class="form-label">Client Details:</label>
                                                <a href="javascript:void(0)" class="addPerson">Add Person</a>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group" id="add_person">
                                                    @if ($tender->client)
                                                        @foreach ($tender->client as $key => $clt)
                                                            <div class="row">
                                                                <div class="col-md-4 form-group">
                                                                    <input type="text"
                                                                        name="client[name][{{ $key }}]"
                                                                        class="form-control" id="name"
                                                                        placeholder="Name"
                                                                        value="{{ $clt['client_name'] ?? '' }}">
                                                                </div>
                                                                <div class="col-md-4 form-group">
                                                                    <input type="email"
                                                                        name="client[email][{{ $key }}]"
                                                                        class="form-control" id="email"
                                                                        placeholder="Email"
                                                                        value="{{ $clt['client_email'] ?? '' }}">
                                                                </div>
                                                                <div class="col-md-4 form-group">
                                                                    <input type="number"
                                                                        name="client[phone][{{ $key }}]"
                                                                        class="form-control" id="phone"
                                                                        placeholder="Phone"
                                                                        value="{{ $clt['client_phone'] ?? '' }}">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <input type="text" name="client[name][0]"
                                                                    class="form-control" id="name" placeholder="Name">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <input type="email" name="client[email][0]"
                                                                    class="form-control" id="email" placeholder="Email">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <input type="number" name="client[phone][0]"
                                                                    class="form-control" id="phone" placeholder="Phone">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="courier_no">Courier Request No.:</label>
                                        <select name="courier_no" class="form-control" id="courier_no" required>
                                            <option value="">Select Courier</option>
                                            @foreach ($couriers as $courier)
                                                <option value="{{ $courier->id }}"
                                                    {{ optional($tender->phyDocs)->courier_no == $courier->id ? 'selected' : '' }}>
                                                    {{ $courier->id }} - {{ $courier->to_org }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('courier_no') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="submitted_docs">Document Submitted</label>
                                        <select name="submitted_docs[]" class="form-control select2" id="submitted_docs" multiple required>
                                            <option value="">Select Courier</option>
                                            @foreach ($docs as $doc)
                                                <option value="{{ $doc->id }}"
                                                    {{ in_array($doc->id, json_decode(optional($tender->phyDocs)->submitted_docs) ?? []) ? 'selected' : '' }}>
                                                    {{ $doc->id }} - {{ $doc->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small>
                                            <span class="text-danger">{{ $errors->first('submitted_docs') }}</span>
                                        </small>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            let fp = {{ count($tender->client) ?? 1 }};
            $(document).on('click', '.addPerson', function(e) {
                let html = `
                <div class="row">
                    <div class="col-md-4 form-group">
                        <input type="text" name="client[name][${fp}]" class="form-control" id="name" placeholder="Name">
                    </div>
                    <div class="col-md-4 form-group">
                        <input type="email" name="client[email][${fp}]" class="form-control" id="email" placeholder="Email">
                    </div>
                    <div class="col-md-4 form-group">
                        <input type="number" name="client[phone][${fp}]" class="form-control" id="phone" placeholder="Phone">
                    </div>
                </div>
                `;
                $('#add_person').append(html);
                fp++;
            });

            $('.select2').select2({
                placeholder: 'Select Submitted Document',
                allowClear: true
            });
            $('#courier_no').select2({
                placeholder: 'Select Courier Number',
                allowClear: true
            });
        });
    </script>
@endpush
@push('styles')
    <style>
        .select2-selection,
        .select2-selection__choice {
            background: transparent !important;
        }
    </style>
@endpush
