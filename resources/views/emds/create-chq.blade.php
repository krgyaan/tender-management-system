@extends('layouts.app')
@section('page-title', 'Request Cheque')
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
                        <form method="POST" action="{{ route('chq.emds.request') }}" enctype="multipart/form-data">
                            @csrf
                            <!-- Step 1 Fields (Original First Form) -->
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="tender_id">Tender Number:</label>
                                    <input type="text" name="tender_no" class="form-control" value="{{ $tender ? $tender->tender_no : '' }}"
                                    id="tender_no" required {{ $tender ? 'readonly' : '' }}>
                                    <input type="hidden" name="tender_id" class="form-control" required
                                    value="{{ $tender ? $tender->id : '00' }}" id="tender_id"  {{ $tender ? 'readonly' : '' }}>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="tender_id">Tender Name:</label>
                                    <input type="text" name="project_name" class="form-control" id="project_name"
                                    value="{{ $tender ? $tender->tender_name : '' }}" required {{ $tender ? 'readonly' : '' }}>
                                </div>
                                <div class="form-group col-md-4 {{ $tender ? 'd-none' : '' }}">
                                    <label class="form-label" for="due_date">Tender Due Date & Time:</label>
                                    <input type="datetime-local" name="due_date" id="due_date" class="form-control"{{ $tender ? '' : 'required' }}
                                        value="{{ $tender && $tender->due_date && $tender->due_time ? "{$tender->due_date}T{$tender->due_time}" : '' }}">
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
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="requested_by">Requested By:</label>
                                    <input type="text" name="requested_by" class="form-control" id="requested_by"
                                        value="{{ Auth::user()->name }}" readonly>
                                </div>
                            </div>
                            @include('partials.div-separator', ['text' => 'Cheque Details'])
                            <!-- Step 2 Fields (Cheque Specific) -->
                            <div class="row" id="cheque">
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="cheque_favour">Cheque in Favour of</label>
                                    <input type="text" name="cheque_favour" id="cheque_favour" class="form-control"
                                        required value="{{ old('cheque_favour') }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="cheque_date">Cheque Date</label>
                                    <input type="date" name="cheque_date" id="cheque_date" class="form-control"
                                        value="{{ old('cheque_date') }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="cheque_amt">Cheque Amount:</label>
                                    <input type="number" name="cheque_amt" id="cheque_amt" min="0" step="0.01"
                                        class="form-control" value="{{ old('cheque_amt', $tender->emd) }}" required>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="cheque_needs">Cheque Needed in</label>
                                    <select name="cheque_needs" id="cheque_needs" class="form-control" required>
                                        <option value="">Select</option>
                                        <option {{ old('cheque_needs') == '3' ? 'selected' : '' }} value="3">3 Hours
                                        </option>
                                        <option {{ old('cheque_needs') == '6' ? 'selected' : '' }} value="6">6 Hours
                                        </option>
                                        <option {{ old('cheque_needs') == '12' ? 'selected' : '' }} value="12">12
                                            Hours</option>
                                        <option {{ old('cheque_needs') == '24' ? 'selected' : '' }} value="24">24
                                            Hours</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="cheque_reason">Purpose of the Cheque</label>
                                    <select name="cheque_reason" id="cheque_reason" class="form-control" required>
                                        <option value="">Select</option>
                                        <option {{ old('cheque_reason') == 'Payable' ? 'selected' : '' }} value="Payable">
                                            Payable</option>
                                        <option {{ old('cheque_reason') == 'Security' ? 'selected' : '' }}
                                            value="Security">Security</option>
                                        <option {{ old('cheque_reason') == 'DD' ? 'selected' : '' }} value="DD">DD
                                        </option>
                                        <option {{ old('cheque_reason') == 'FDR' ? 'selected' : '' }} value="FDR">FDR
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="cheque_bank">Account to be debited from</label>
                                    <select name="cheque_bank" id="cheque_bank" class="form-control" required>
                                        <option value="">Choose Bank Name</option>
                                        @foreach ($banks as $key => $bank)
                                            <option value="{{ $key }}"
                                                {{ old('cheque_bank') == $key ? 'selected' : '' }}>{{ $bank }}
                                            </option>
                                        @endforeach
                                    </select>
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
