@extends('layouts.app')
@section('page-title', 'Request EMD')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('emds.index') }}" class="btn btn-primary btn-sm">View All EMDs</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="new-user-info">
                            <form method="POST" action="{{ route('emds.post.step1') }}">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="tender_no">Tender Number:</label>
                                        <input type="text" name="tender_no" class="form-control"
                                            value="{{ $tender ? $tender->tender_no : '' }}" id="tender_no" required
                                            {{ $tender ? 'readonly' : '' }}>
                                        <input type="hidden" name="tender_id" value="{{ $tender ? $tender->id : '00' }}">
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('tender_no') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="tender_id">Tender Name:</label>
                                        <input type="text" name="project_name" class="form-control" id="project_name"
                                            value="{{ $tender ? $tender->tender_name : '' }}" required
                                            {{ $tender ? 'readonly' : '' }}>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('project_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4 {{ $tender ? 'd-none' : '' }}">
                                        <label class="form-label" for="due_date">Tender Due Date & Time:</label>
                                        <input type="datetime-local" name="due_date" id="due_date" class="form-control" {{ $tender ? '' : 'required' }}
                                            value="{{ $tender && $tender->due_date && $tender->due_time ? "{$tender->due_date}T{$tender->due_time}" : '' }}">
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('due_date') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="instrument_type">Instrument Type</label>
                                        <select name="instrument_type" class="form-control" id="instrument_type" required>
                                            <option value="">-- Select --</option>
                                            @foreach ($instrumentType as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('instrument_type') }}</span>
                                        </small>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="requested_by">Requested By</label>
                                        <input type="text" name="requested_by" class="form-control" id="requested_by"
                                            value="{{ Auth::user()->name }}" readonly>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('requested_by') }}</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Next
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
