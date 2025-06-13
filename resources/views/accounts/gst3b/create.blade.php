@extends('layouts.app')
@section('page-title', 'GST 3B Form')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Tally Data Link -->
                            <div class="mb-4">
                                <label for="tally_data_link" class="form-label fw-bold">
                                    <i class="fas fa-link me-2"></i>Tally Data (Google Drive Link)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fab fa-google-drive "></i>
                                    </span>
                                    <input type="url" class="form-control" id="tally_data_link" name="tally_data_link"
                                        placeholder="https://drive.google.com/..." required>
                                </div>
                                <div class="form-text text-muted">Please provide a shareable Google Drive link</div>
                            </div>

                            <!-- GST 2A Section -->
                            <div class="mb-4 border-top pt-3">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-file-excel me-2"></i>GST 2A Details
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div>
                                            <label for="gst_2a_file" class="form-label">Upload GST 2A File</label>
                                        </div>
                                        <input type="file" class="form-control" id="gst_2a_file" name="gst_2a_file"
                                            required>
                                        <div class="form-text text-muted">Accepted formats: XLS, XLSX, PDF</div>
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
                                                name="gst_tds_accepted">
                                            <label class="form-check-label " for="gst_tds_accepted">
                                                GST TDS Accepted
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div><label for="gst_tds_amount" class="form-label">Amount</label></div>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" class="form-control" id="gst_tds_amount"
                                                name="gst_tds_amount" step="0.01">
                                        </div>
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
                                        <label for="payment_challan" class="form-label">Payment Challan</label>
                                        <input type="file" class="form-control" id="payment_challan"
                                            name="payment_challan">
                                        <div class="form-text text-muted">Upload payment challan copy</div>
                                    </div>
                                </div>
                            </div>

                            <!-- GST Paid Section -->
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mt-5">
                                            <input class="form-check-input" type="checkbox" id="gst_paid" name="gst_paid">
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
                                                name="gst_paid_amount" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- UTR Message -->
                            <div class="mb-4">
                                <label for="utr_message" class="form-label fw-bold">
                                    <i class="fas fa-comment-alt me-2"></i>UTR Message
                                </label>
                                <textarea class="form-control" id="utr_message" name="utr_message" rows="3"></textarea>
                                <div class="form-text text-muted">Enter UTR reference or payment details</div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end mt-4 border-top pt-3">
                                <button type="reset" class="btn btn-outline-secondary me-3">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane me-2"></i>Submit
                                </button>
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
        });

        document.getElementById('gst_paid').addEventListener('change', function() {
            document.getElementById('gst_paid_amount').disabled = !this.checked;
        });

        // Initialize disabled states
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('gst_tds_amount').disabled = true;
            document.getElementById('gst_paid_amount').disabled = true;
        });
    </script>
@endpush
