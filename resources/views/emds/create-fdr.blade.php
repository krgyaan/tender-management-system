@extends('layouts.app')
@section('page-title', 'Request FDR')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('emds.index') }}" class="btn btn-primary btn-sm">View All EMDs</a>
                </div>
                <div class="card">
                    @include('partials.messages')
                    <div class="card-body">
                        <form method="POST" action="{{ route('emds.store') }}" enctype="multipart/form-data">
                            @csrf
                            <!-- Step 1 Fields (Original First Form) -->
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="tender_id">Tender Number:</label>
                                    <input type="text" name="tender_no" class="form-control"
                                        value="{{ $tender ? $tender->tender_no : '' }}" id="tender_no" required
                                        {{ $tender ? 'readonly' : '' }}>
                                    <input type="hidden" name="tender_id" class="form-control"
                                        value="{{ $tender ? $tender->id : '00' }}" id="tender_id" required
                                        {{ $tender ? 'readonly' : '' }}>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('tender_no') }}</span></small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="tender_id">Tender Name:</label>
                                    <input type="text" name="project_name" class="form-control" id="project_name"
                                        value="{{ $tender ? $tender->tender_name : '' }}" required
                                        {{ $tender ? 'readonly' : '' }}>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('project_name') }}</span></small>
                                </div>
                                <div class="form-group col-md-4 {{ $tender ? 'd-none' : '' }}">
                                    <label class="form-label" for="due_date">Tender Due Date & Time:</label>
                                    <input type="datetime-local" name="due_date" id="due_date" class="form-control"
                                        {{ $tender ? '' : 'required' }}
                                        value="{{ $tender && $tender->due_date && $tender->due_time ? "{$tender->due_date}T{$tender->due_time}" : '' }}">
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('due_date') }}</span></small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="instrument_type">Instrument Type:</label>
                                    <select name="instrument_type" class="form-control" id="instrument_type" required
                                        readonly>
                                        <option value="">-- Select --</option>
                                        @foreach ($instrumentType as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ $instrument_type == $key ? 'selected' : '' }}>{{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('instrument_type') }}</span></small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="requested_by">Requested By:</label>
                                    <input type="text" name="requested_by" class="form-control" id="requested_by"
                                        value="{{ Auth::user()->name }}" readonly>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('requested_by') }}</span></small>
                                </div>
                            </div>
                            @include('partials.div-separator', ['text' => 'FDR Details'])
                            <!-- Step 2 Fields (FDR Specific) -->
                            <div class="row" id="fdr">
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="fdr_favour">FDR in Favour of:</label>
                                    <input type="text" name="fdr_favour" id="fdr_favour" class="form-control"
                                        value="{{ old('fdr_favour') }}" required>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('fdr_favour') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="fdr_amt">FDR Amount:</label>
                                    <input type="number" name="fdr_amt" id="fdr_amt" min="0" step="0.01"
                                        class="form-control" value="{{ old('fdr_amt') }}" required>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('fdr_amt') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="fdr_expiry">FDR Expiry Date:</label>
                                    <input type="date" name="fdr_expiry" id="fdr_expiry" class="form-control"
                                        value="{{ old('fdr_expiry') }}" required>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('fdr_expiry') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="fdr_needs">FDR deliver by:</label>
                                    <select name="fdr_needs" id="fdr_needs" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="due" {{ old('fdr_needs') == 'due' ? 'selected' : '' }}>Tender
                                            Due Date</option>
                                        <option value="24" {{ old('fdr_needs') == '24' ? 'selected' : '' }}>24 Hours
                                        </option>
                                        <option value="48" {{ old('fdr_needs') == '48' ? 'selected' : '' }}>48 Hours
                                        </option>
                                        <option value="72" {{ old('fdr_needs') == '72' ? 'selected' : '' }}>72 Hours
                                        </option>
                                        <option value="96" {{ old('fdr_needs') == '96' ? 'selected' : '' }}>96 Hours
                                        </option>
                                    </select>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('fdr_needs') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="fdr_purpose">Purpose of FDR:</label>
                                    <select name="fdr_purpose" id="fdr_purpose" class="form-control" required>
                                        <option value="">-- Choose --</option>
                                        @foreach ($dd_purposes as $key => $value)
                                            <option {{ old('fdr_purpose') == $key ? 'selected' : '' }}
                                                value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('fdr_purpose') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group ">
                                    <label class="form-label" for="courier_add">Courier Address:</label>
                                    <input type="text" name="courier_add" id="courier_add" class="form-control"
                                        value="{{ old('courier_add') }}">
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('courier_add') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group ">
                                    <label class="form-label" for="courier_deadline">Time required for courier to reach
                                        destination:</label>
                                    <div class="input-group">
                                        <input type="number" name="courier_deadline" id="courier_deadline"
                                            class="form-control" value="{{ old('courier_deadline') }}"
                                            onkeypress="return isNumberKey(event)" min="1" step="1">
                                        <div class="input-group-append"><span class="input-group-text">Hours</span></div>
                                    </div>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('courier_deadline') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="fdr_date">FDR Date:</label>
                                    <input type="date" name="fdr_date" id="fdr_date" class="form-control"
                                        value="{{ old('fdr_date') }}">
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('fdr_date') }}</span></small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <a href="{{ URL::previous() }}" class="btn btn-outline-light">Back</a>
                                <button type="submit" name="submit" class="btn btn-primary">Request EMD</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
