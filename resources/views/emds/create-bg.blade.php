@extends('layouts.app')
@section('page-title', 'Request Bank Guarantee')
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
                        <form method="POST" action="{{ route('bg.emds.request') }}" enctype="multipart/form-data">
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
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="due_date">Tender Due Date & Time:</label>
                                    <input type="datetime-local" name="due_date" id="due_date" class="form-control"
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

                            @include('partials.div-separator', ['text' => 'Bank Guarantee Details'])

                            <!-- Step 2 Fields (Bank Guarantee Specific) -->
                            <div class="row" id="bg">
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_needs">BG needed in</label>
                                    <select name="bg_needs" id="bg_needs" class="form-control" required>
                                        <option value="">Select</option>
                                        <option {{ old('bg_needs') == '72' ? 'selected' : '' }} value="72">72 Hours
                                        </option>
                                        <option {{ old('bg_needs') == '96' ? 'selected' : '' }} value="96">96 Hours
                                        </option>
                                        <option {{ old('bg_needs') == '120' ? 'selected' : '' }} value="120">120 Hours
                                        </option>
                                    </select>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('bg_needs') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_purpose">Purpose of the BG</label>
                                    <select name="bg_purpose" id="bg_purpose" class="form-control" required>
                                        <option value="">Select</option>
                                        @foreach ($purpose as $key => $value)
                                            <option {{ old('bg_purpose') == $key ? 'selected' : '' }}
                                                value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('bg_purpose') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_favour">BG in Favour of</label>
                                    <input type="text" name="bg_favour" id="bg_favour" class="form-control"
                                        value="{{ old('bg_favour') }}" required>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('bg_favour') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_address">BG Address</label>
                                    <input type="text" name="bg_address" id="bg_address" class="form-control"
                                        value="{{ old('bg_address') }}" required>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('bg_address') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_expiry">BG Expiry Date</label>
                                    <input type="date" name="bg_expiry" id="bg_expiry" class="form-control"
                                        value="{{ old('bg_expiry') }}" required>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('bg_expiry') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_claim">BG Claim Period:</label>
                                    <input type="date" name="bg_claim" id="bg_claim" class="form-control"
                                        value="{{ old('bg_claim') }}" required>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('bg_claim') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_amt">BG Amount:</label>
                                    <input type="number" name="bg_amt" id="bg_amt" min="0" step="0.01"
                                        class="form-control" value="{{ old('bg_amt') }}" required>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('bg_amt') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_stamp">BG Stamp Paper Value</label>
                                    <input type="number" name="bg_stamp" id="bg_stamp" min="0" step="0.01"
                                        class="form-control" value="{{ old('bg_stamp') }}" required>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('bg_stamp') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_format_te">Upload BG Format TE</label>
                                    <input type="file" name="bg_format_te" id="bg_format_te" class="form-control"
                                        multiple required>
                                    <small class="text-muted">Upload max 5 files. <span
                                            class="text-danger">{{ $errors->first('bg_format_te') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_po">PO/Tender/Request letter Upload</label>
                                    <input type="file" name="bg_po" id="bg_po" accept=".pdf,.doc,.docx,image/*"
                                        class="form-control" value="{{ old('bg_po') }}" required>
                                    <small class="text-muted">Upload only 1 file. <span
                                            class="text-danger">{{ $errors->first('bg_po') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_client_email">Client Emails</label>
                                    <div class="input-group mb-1">
                                        <input type="email" name="bg_client_user" id="bg_client_user"
                                            class="form-control" placeholder="User Dept. Email"
                                            value="{{ old('bg_client_user') }}" required>
                                    </div>
                                    <div class="input-group mb-1">
                                        <input type="email" name="bg_client_cp" id="bg_client_cp" class="form-control"
                                            placeholder="C&P Dept. Email" value="{{ old('bg_client_cp') }}" required>
                                    </div>
                                    <div class="input-group mb-1">
                                        <input type="email" name="bg_client_fin" id="bg_client_fin"
                                            class="form-control" placeholder="Finance Dept. Email"
                                            value="{{ old('bg_client_fin') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_bank_details">Client Bank Details</label>
                                    <div class="input-group mb-1">
                                        <input type="text" name="bg_bank_name" id="bg_bank_name" class="form-control"
                                            placeholder="Bank Account Name" value="{{ old('bg_bank_name') }}" required>
                                    </div>
                                    <div class="input-group mb-1">
                                        <input type="number" name="bg_bank_acc" id="bg_bank_acc" min="0"
                                            class="form-control" placeholder="Account Number"
                                            value="{{ old('bg_bank_acc') }}">
                                    </div>
                                    <div class="input-group mb-1">
                                        <input type="text" name="bg_bank_ifsc" id="bg_bank_ifsc" class="form-control"
                                            placeholder="IFSC" value="{{ old('bg_bank_ifsc') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_courier_addr">BG Courier Address</label>
                                    <input type="text" name="bg_courier_addr" id="bg_courier_addr"
                                        class="form-control" value="{{ old('bg_courier_addr') }}" required>
                                    <small class="text-muted">Address where BG will be sent through courier. <span
                                            class="text-danger">{{ $errors->first('bg_courier_addr') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="courier_deadline">Courier Delivery Time</label>
                                    <div class="input-group">
                                        <select name="courier_deadline" id="courier_deadline" class="form-control"
                                            required>
                                            <option value="">Select Days</option>
                                            @for ($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}"
                                                    {{ old('courier_deadline') == $i ? 'selected' : '' }}>
                                                    {{ $i }}</option>
                                            @endfor
                                        </select>
                                        <span class="input-group-text">days</span>
                                    </div>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('courier_deadline') }}</span></small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bg_bank">Bank</label>
                                    <select name="bg_bank" id="bg_bank" class="form-control">
                                        <option value="">Select Bank</option>
                                        @foreach ($banks as $key => $bank)
                                            <option value="{{ $key }}">{{ $bank }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted"><span
                                            class="text-danger">{{ $errors->first('bg_bank') }}</span></small>
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
