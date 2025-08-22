@extends('layouts.app')
@section('page-title', 'View GST 3B Form')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">GST 3B Form Details</h4>
                                <small class="text-white-50">Submitted on:
                                    {{ $form->created_at->format('d M Y, h:i A') }}</small>
                            </div>
                            <div>
                                <a href="{{ route('gst3b.edit', $form->id) }}" class="btn btn-sm btn-light me-2">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <a href="{{ route('gst3b.index') }}" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-arrow-left me-1"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Status Badge -->
                        <div class="mb-4">
                            <span
                                class="badge bg-{{ $form->status === 'approved' ? 'success' : ($form->status === 'rejected' ? 'danger' : 'warning') }}">
                                Status: {{ ucfirst($form->status) }}
                            </span>
                            @if ($form->status === 'rejected' && $form->rejection_reason)
                                <div class="alert alert-danger mt-2">
                                    <strong>Rejection Reason:</strong> {{ $form->rejection_reason }}
                                </div>
                            @endif
                        </div>

                        <!-- Tally Data Section -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-link me-2"></i>Tally Data
                            </h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark me-3">
                                            <i class="fab fa-google-drive"></i>
                                        </span>
                                        <div>
                                            <a href="{{ $form->tally_data_link }}" target="_blank" class="text-break">
                                                {{ $form->tally_data_link }}
                                            </a>
                                            <div class="text-muted small">Google Drive Link</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- GST 2A Section -->
                        <div class="mb-4 border-top pt-3">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-file-excel me-2"></i>GST 2A Details
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    @if ($form->gst_2a_file_path)
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-light text-dark me-3">
                                                <i class="fas fa-file-excel"></i>
                                            </span>
                                            <div>
                                                <a href="{{ Storage::url($form->gst_2a_file_path) }}" target="_blank"
                                                    class="d-block">
                                                    Download GST 2A File
                                                </a>
                                                <small class="text-muted">
                                                    Uploaded: {{ $form->updated_at->format('d M Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-muted">No file uploaded</div>
                                    @endif
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
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge bg-light text-dark me-3">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <div>
                                            <strong>GST TDS Accepted:</strong>
                                            <span class="{{ $form->gst_tds_accepted ? 'text-success' : 'text-danger' }}">
                                                {{ $form->gst_tds_accepted ? 'Yes' : 'No' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if ($form->gst_tds_accepted)
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-light text-dark me-3">
                                                <i class="fas fa-rupee-sign"></i>
                                            </span>
                                            <div>
                                                <strong>Amount:</strong>
                                                ₹{{ number_format($form->gst_tds_amount, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details Section -->
                        <div class="mb-4 border-top pt-3">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-receipt me-2"></i>Payment Details
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    @if ($form->payment_challan_path)
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge bg-light text-dark me-3">
                                                <i class="fas fa-file-invoice"></i>
                                            </span>
                                            <div>
                                                <a href="{{ Storage::url($form->payment_challan_path) }}" target="_blank"
                                                    class="d-block">
                                                    Download Payment Challan
                                                </a>
                                                <small class="text-muted">
                                                    Uploaded: {{ $form->updated_at->format('d M Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-muted mb-3">No payment challan uploaded</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge bg-light text-dark me-3">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <div>
                                            <strong>GST Paid:</strong>
                                            <span class="{{ $form->gst_paid ? 'text-success' : 'text-danger' }}">
                                                {{ $form->gst_paid ? 'Yes' : 'No' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if ($form->gst_paid)
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-light text-dark me-3">
                                                <i class="fas fa-rupee-sign"></i>
                                            </span>
                                            <div>
                                                <strong>Amount:</strong>
                                                ₹{{ number_format($form->gst_paid_amount, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- UTR Message Section -->
                        <div class="mb-4 border-top pt-3">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-comment-alt me-2"></i>UTR Message
                            </h5>
                            <div class="bg-light p-3 rounded">
                                @if ($form->utr_message)
                                    {{ $form->utr_message }}
                                @else
                                    <span class="text-muted">No UTR message provided</span>
                                @endif
                            </div>
                        </div>

                        <!-- Admin Actions (if applicable) -->
                        @can('approve', $form)
                            <div class="border-top pt-3 mt-4">
                                <h5 class="text-primary mb-3">Admin Actions</h5>
                                <form action="{{ route('gst3b.approve', $form->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success me-2">
                                        <i class="fas fa-check-circle me-1"></i> Approve
                                    </button>
                                </form>

                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="fas fa-times-circle me-1"></i> Reject
                                </button>
                            </div>

                            <!-- Rejection Modal -->
                            <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('gst3b.reject', $form->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="rejectModalLabel">Reject GST 3B Form</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="rejection_reason" class="form-label">Reason for
                                                        Rejection</label>
                                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endcan
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

        .border-top {
            border-top: 1px solid #dee2e6 !important;
        }

        .badge.bg-light {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.1rem;
        }
    </style>
@endpush
