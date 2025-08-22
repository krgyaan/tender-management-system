@extends('layouts.app')
@section('page-title', 'Edit GST 3B Form')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Edit GST 3B Form</h4>
                        <small class="text-white-50">Last updated: {{ $form->updated_at->format('d M Y, h:i A') }}</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('gst3b.update', $form->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Tally Data Link -->
                            <div class="mb-4">
                                <label for="tally_data_link" class="form-label fw-bold">
                                    <i class="fas fa-link me-2"></i>Tally Data (Google Drive Link)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fab fa-google-drive"></i>
                                    </span>
                                    <input type="url" class="form-control" id="tally_data_link" name="tally_data_link"
                                        value="{{ old('tally_data_link', $form->tally_data_link) }}"
                                        placeholder="https://drive.google.com/..." required>
                                </div>
                                @if ($form->tally_data_link)
                                    <div class="mt-2">
                                        <a href="{{ $form->tally_data_link }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt me-1"></i> View Current File
                                        </a>
                                    </div>
                                @endif
                                <div class="form-text text-muted">Please provide a shareable Google Drive link</div>
                                @error('tally_data_link')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- GST 2A Section -->
                            <div class="mb-4 border-top pt-3">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-file-excel me-2"></i>GST 2A Details
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div>
                                            <label for="gst_2a_file" class="form-label">Update GST 2A File</label>
                                        </div>
                                        <input type="file" class="form-control" id="gst_2a_file" name="gst_2a_file">
                                        @if ($form->gst_2a_file_path)
                                            <div class="mt-2">
                                                <span class="fw-bold">Current File:</span>
                                                <a href="{{ Storage::url($form->gst_2a_file_path) }}" target="_blank"
                                                    class="ms-2">
                                                    <i class="fas fa-file-download me-1"></i>Download
                                                </a>
                                            </div>
                                        @endif
                                        <div class="form-text text-muted">Accepted formats: XLS, XLSX, PDF</div>
                                        @error('gst_2a_file')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- GST TDS Section -->
                            <div class="mb-4 border-top pt-3">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-hand-holding-usd me-2"></i>GST TDS Details
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check my-5">
                                            <input class="form-check-input" type="checkbox" id="gst_tds_accepted"
                                                name="gst_tds_accepted"
                                                {{ old('gst_tds_accepted', $form->gst_tds_accepted) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gst_tds_accepted">
                                                GST TDS Accepted
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div><label for="gst_tds_amount" class="form-label">Amount</label></div>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" class="form-control" id="gst_tds_amount"
                                                name="gst_tds_amount" step="0.01"
                                                value="{{ old('gst_tds_amount', $form->gst_tds_amount) }}"
                                                {{ $form->gst_tds_accepted ? '' : 'disabled' }}>
                                        </div>
                                        @error('gst_tds_amount')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Challan Section -->
                            <div class="mb-4 border-top pt-3">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-receipt me-2"></i>Payment Details
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="payment_challan" class="form-label">Update Payment Challan</label>
                                        <input type="file" class="form-control" id="payment_challan"
                                            name="payment_challan">
                                        @if ($form->payment_challan_path)
                                            <div class="mt-2">
                                                <span class="fw-bold">Current File:</span>
                                                <a href="{{ Storage::url($form->payment_challan_path) }}" target="_blank"
                                                    class="ms-2">
                                                    <i class="fas fa-file-download me-1"></i>Download
                                                </a>
                                            </div>
                                        @endif
                                        <div class="form-text text-muted">Upload payment challan copy</div>
                                        @error('payment_challan')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- GST Paid Section -->
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mt-5">
                                            <input class="form-check-input" type="checkbox" id="gst_paid" name="gst_paid"
                                                {{ old('gst_paid', $form->gst_paid) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gst_paid">
                                                GST Paid
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gst_paid_amount" class="form-label">Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" class="form-control" id="gst_paid_amount"
                                                name="gst_paid_amount" step="0.01"
                                                value="{{ old('gst_paid_amount', $form->gst_paid_amount) }}"
                                                {{ $form->gst_paid ? '' : 'disabled' }}>
                                        </div>
                                        @error('gst_paid_amount')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- UTR Message -->
                            <div class="mb-4">
                                <label for="utr_message" class="form-label fw-bold">
                                    <i class="fas fa-comment-alt me-2"></i>UTR Message
                                </label>
                                <textarea class="form-control" id="utr_message" name="utr_message" rows="3">{{ old('utr_message', $form->utr_message) }}</textarea>
                                <div class="form-text text-muted">Enter UTR reference or payment details</div>
                                @error('utr_message')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between mt-4 border-top pt-3">
                                <div>
                                    <a href="{{ route('gst3b.show', $form->id) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                </div>
                                <div>
                                    <button type="reset" class="btn btn-outline-secondary me-3">
                                        <i class="fas fa-undo me-2"></i>Reset Changes
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            border-radius: 0.5rem 0.5rem 0 0 !important;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        }

        .form-label {
            font-weight: 500;
        }

        .border-top {
            border-top: 1px solid #dee2e6 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Enable amount fields when checkboxes are checked
        document.getElementById('gst_tds_accepted').addEventListener('change', function() {
            document.getElementById('gst_tds_amount').disabled = !this.checked;
            if (!this.checked) {
                document.getElementById('gst_tds_amount').value = '';
            }
        });

        document.getElementById('gst_paid').addEventListener('change', function() {
            document.getElementById('gst_paid_amount').disabled = !this.checked;
            if (!this.checked) {
                document.getElementById('gst_paid_amount').value = '';
            }
        });

        // Initialize disabled states based on current values
        document.addEventListener('DOMContentLoaded', function() {
            const tdsAccepted = document.getElementById('gst_tds_accepted').checked;
            document.getElementById('gst_tds_amount').disabled = !tdsAccepted;

            const gstPaid = document.getElementById('gst_paid').checked;
            document.getElementById('gst_paid_amount').disabled = !gstPaid;
        });
    </script>
@endpush
