@extends('layouts.app')
@section('page-title', 'Request Payment on Portal')
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
                        <form method="POST" action="{{ route('pop.emds.request') }}" enctype="multipart/form-data"
                            id="popRequestForm">
                            @csrf
                            <!-- Common Information Section -->
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="tender_id">Tender Number:</label>
                                    <input type="text" name="tender_no" class="form-control"
                                        value="{{ $tender ? $tender->tender_no : '' }}" id="tender_no" required
                                        {{ $tender ? 'readonly' : '' }}>
                                    <input type="hidden" name="tender_id" class="form-control"
                                        value="{{ $tender ? $tender->id : '00' }}" id="tender_id" required
                                        {{ $tender ? 'readonly' : '' }}>
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
                                    <input type="datetime-local" name="due_date" id="due_date" class="form-control"
                                        {{ $tender ? '' : 'required' }}
                                        value="{{ $tender && $tender->due_date && $tender->due_time ? "{$tender->due_date}T{$tender->due_time}" : '' }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('due_date') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="instrument_type">Instrument Type:</label>
                                    <select name="instrument_type" class="form-control" id="instrument_type" required>
                                        <option value="">-- Select --</option>
                                        @foreach ($instrumentType as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ $instrument_type == '6' ? 'selected' : '' }}>{{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('instrument_type') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="requested_by">Requested By:</label>
                                    <input type="text" name="requested_by" class="form-control" id="requested_by"
                                        value="{{ Auth::user()->name }}" readonly>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('requested_by') }}</span>
                                    </small>
                                </div>
                            </div>

                            @include('partials.div-separator', ['text' => 'EMD Payment Details'])

                            <!-- EMD Payment Section -->
                            <div class="row" id="pay_on_portal">
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="purpose">Purpose</label>
                                    <select name="purpose" id="purpose" class="form-control" required>
                                        <option value="">-- Choose --</option>
                                        <option {{ old('purpose') == 'EMD' ? 'selected' : '' }} value="EMD">EMD</option>
                                        <option {{ old('purpose') == 'Tender Fees' ? 'selected' : '' }}
                                            value="Tender Fees">Tender Fees</option>
                                        <option {{ old('purpose') == 'Others' ? 'selected' : '' }} value="Others">Others
                                        </option>
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('purpose') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="portal">Name of Portal/Website</label>
                                    <input type="text" name="portal" id="portal" class="form-control"
                                        value="{{ old('portal') }}" required>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('portal') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="is_netbanking">Net Banking Available</label>
                                    <select name="is_netbanking" id="is_netbanking" class="form-control" required>
                                        <option value="">-- Choose --</option>
                                        <option {{ old('is_netbanking') == 'yes' ? 'selected' : '' }} value="yes">Yes
                                        </option>
                                        <option {{ old('is_netbanking') == 'no' ? 'selected' : '' }} value="no">No
                                        </option>
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('is_netbanking') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="is_debit">Yes Bank Debit Card Allowed</label>
                                    <select name="is_debit" id="is_debit" class="form-control" required>
                                        <option value="">-- Choose --</option>
                                        <option {{ old('is_debit') == 'yes' ? 'selected' : '' }} value="yes">Yes
                                        </option>
                                        <option {{ old('is_debit') == 'no' ? 'selected' : '' }} value="no">No</option>
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('is_debit') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="amount">Amount</label>
                                    <input type="number" name="amount" id="amount" required class="form-control"
                                        value="{{ old('amount', $tender->emd) }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                                    </small>
                                </div>
                            </div>

                            <!-- Tender Fee Section (Conditional) -->
                            @if ($tender->tender_fees > 0)
                                <div class="col-md-12 my-3">
                                    <label for="tf_mode" class="d-block mb-2">Tender Fee Mode</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tf_consent"
                                            id="tf_consent_1" value="1" required checked>
                                        <label class="form-check-label" for="tf_consent_1">Payment on Portal</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tf_consent"
                                            id="tf_consent_2" value="2">
                                        <label class="form-check-label" for="tf_consent_2">Bank Transfer</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tf_consent"
                                            id="tf_consent_3" value="3">
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
                                <button type="submit" name="submit" class="btn btn-primary">Submit Request</button>
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
