@extends('layouts.app')
@section('page-title', 'Request Demand Draft')
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
                        <form method="POST" action="{{ route('dd.emds.request') }}" enctype="multipart/form-data"
                            id="requestForm">
                            @csrf
                            <!-- Step 1 Fields (Common Information) -->
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="tender_id">Tender Number:</label>
                                    <input type="text" name="tender_no" class="form-control"
                                        value="{{ $tender ? $tender->tender_no : '' }}" id="tender_no" required
                                        {{ $tender ? 'readonly' : '' }}>
                                    <input type="hidden" name="tender_id" class="form-control"
                                        value="{{ $tender ? $tender->id : '00' }}" id="tender_id" required
                                        {{ $tender ? 'readonly' : '' }}>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="tender_id">Tender Name:</label>
                                    <input type="text" name="project_name" class="form-control" id="project_name"
                                        value="{{ $tender ? $tender->tender_name : '' }}" required
                                        {{ $tender ? 'readonly' : '' }}>
                                </div>
                                <div class="form-group col-md-4 {{ $tender ? 'd-none' : '' }}">
                                    <label class="form-label" for="due_date">Tender Due Date & Time:</label>
                                    <input type="datetime-local" name="due_date" id="due_date" class="form-control"
                                        {{ $tender ? '' : 'required' }}
                                        value="{{ $tender && $tender->due_date && $tender->due_time ? "{$tender->due_date}T{$tender->due_time}" : '' }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="instrument_type">Instrument Type:</label>
                                    <select name="instrument_type" class="form-control" id="instrument_type" required
                                        readonly>
                                        <option value="">-- Select --</option>
                                        @foreach ($instrumentType as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ $instrument_type == $key ? 'selected' : '' }}>
                                                {{ $value }}
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
                                </div>
                            </div>

                            @include('partials.div-separator', ['text' => 'EMD Details'])

                            <!-- EMD Specific Fields -->
                            <div class="row" id="demand_draft">
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="dd_favour">DD in Favour of:</label>
                                    <input type="text" name="dd_favour" id="dd_favour" class="form-control" required
                                        value="{{ old('dd_favour') }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="dd_amt">DD Amount:</label>
                                    <input type="number" name="dd_amt" id="dd_amt" min="0" step="0.01"
                                        class="form-control" required value="{{ old('dd_amt', $tender->emd) }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="dd_payable">Payable At:</label>
                                    <input type="text" name="dd_payable" id="dd_payable" class="form-control" required
                                        value="{{ old('dd_payable') }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="dd_needs">DD deliver by:</label>
                                    <select name="dd_needs" id="dd_needs" class="form-control">
                                        <option value="">-- Choose --</option>
                                        <option {{ old('dd_needs') == 'due' ? 'selected' : '' }} value="due">Tender Due
                                            Date</option>
                                        <option {{ old('dd_needs') == '24' ? 'selected' : '' }} value="24">24 Hours
                                        </option>
                                        <option {{ old('dd_needs') == '36' ? 'selected' : '' }} value="36">36 Hours
                                        </option>
                                        <option {{ old('dd_needs') == '48' ? 'selected' : '' }} value="48">48 Hours
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="dd_purpose">Purpose of the DD:</label>
                                    <select name="dd_purpose" id="dd_purpose" class="form-control" required>
                                        <option value="">-- Choose --</option>
                                        @foreach ($dd_purposes as $key => $value)
                                            <option {{ old('dd_purpose') == $key ? 'selected' : '' }}
                                                value="{{ $key }}">
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="courier_add">Courier Address:</label>
                                    <input type="text" name="courier_add" id="courier_add" class="form-control"
                                        value="{{ old('courier_add') }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="courier_deadline">Time required for courier to reach
                                        destination:</label>
                                    <div class="input-group">
                                        <input type="number" name="courier_deadline" id="courier_deadline"
                                            class="form-control" value="{{ old('courier_deadline') }}"
                                            onkeypress="return isNumberKey(event)" min="1" step="1">
                                        <div class="input-group-append"><span class="input-group-text">Hours</span></div>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="dd_date">DD Date:</label>
                                    <input type="date" name="dd_date" id="dd_date" class="form-control"
                                        value="{{ old('dd_date') }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="remarks">Remarks (if any):</label>
                                    <input type="text" name="remarks" id="remarks" class="form-control"
                                        value="{{ old('remarks') }}">
                                </div>
                            </div>

                            <!-- Tender Fee Section (Conditional) -->
                            @if ($tender->tender_fees > 0)
                                <div class="col-md-12 my-3">
                                    <label for="tf_mode" class="d-block mb-2">Tender Fee Mode</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tf_consent"
                                            id="tf_consent_1" value="1" required>
                                        <label class="form-check-label" for="tf_consent_1">Payment on Portal</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tf_consent"
                                            id="tf_consent_2" value="2">
                                        <label class="form-check-label" for="tf_consent_2">Bank Transfer</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tf_consent"
                                            id="tf_consent_3" value="3" checked>
                                        <label class="form-check-label" for="tf_consent_3">Demand Draft</label>
                                    </div>
                                </div>
                                <div id="tf_pop" class="tender-fee-mode d-none">
                                    @include('partials.pop-tender-fee')
                                </div>
                                <div id="tf_bt" class="tender-fee-mode d-none">
                                    @include('partials.bt-tender-fee')
                                </div>
                                <div id="tf_dd" class="tender-fee-mode d-none">
                                    @include('partials.dd-tender-fee')
                                </div>
                            @endif

                            <div class="d-flex align-items-center justify-content-between mt-4">
                                <a href="{{ URL::previous() }}" class="btn btn-outline-light">Back</a>
                                <button type="submit" name="submit" class="btn btn-primary">Send Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function toggleTenderFeeForms(selectedMode) {
                const modes = {
                    '1': '#tf_pop',
                    '2': '#tf_bt',
                    '3': '#tf_dd'
                };

                // Hide and disable 'required' from all fields
                Object.entries(modes).forEach(([mode, selector]) => {
                    const section = document.querySelector(selector);
                    if (section) {
                        section.classList.add('d-none');
                        section.querySelectorAll('[required]').forEach(el => {
                            el.dataset.required = "true"; // Mark it
                            el.removeAttribute('required');
                        });
                    }
                });

                // Show selected and re-enable 'required'
                const activeSection = document.querySelector(modes[selectedMode]);
                if (activeSection) {
                    activeSection.classList.remove('d-none');
                    activeSection.querySelectorAll('[data-required="true"]').forEach(el => {
                        el.setAttribute('required', 'required');
                    });
                }
            }

            // Trigger on change
            document.querySelectorAll('input[name="tf_consent"]').forEach(function(input) {
                input.addEventListener('change', function() {
                    toggleTenderFeeForms(this.value);
                });
            });

            // Trigger once on page load (if pre-checked)
            const preChecked = document.querySelector('input[name="tf_consent"]:checked');
            if (preChecked) {
                toggleTenderFeeForms(preChecked.value);
            }
        });
    </script>
@endpush
