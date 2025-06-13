@extends('layouts.app')
@section('page-title', 'TDS Details')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">TDS Record Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Basic Information -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-primary mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h5>
                                <div>
                                    <a href="{{ route('tds.edit', $tds->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <a href="{{ route('tds.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Back to List
                                    </a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Created At</label>
                                        <p class="form-control-static">{{ $tds->created_at->format('d M Y, h:i A') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Updated</label>
                                        <p class="form-control-static">{{ $tds->updated_at->format('d M Y, h:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TDS Excel Sheet Section -->
                        <div class="mb-4 border-top pt-3">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-file-excel me-2"></i>TDS Excel Sheet
                            </h5>
                            @if ($tds->tds_excel_path)
                                <a href="{{ asset('storage/' . $tds->tds_excel_path) }}" class="btn btn-outline-primary"
                                    target="_blank">
                                    <i class="fas fa-download me-1"></i> Download Excel Sheet
                                </a>
                            @else
                                <p class="text-muted">No Excel sheet uploaded</p>
                            @endif
                        </div>

                        <!-- Tally Data Link -->
                        <div class="mb-4 border-top pt-3">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-link me-2"></i>Tally Data
                            </h5>
                            @if ($tds->tally_data_link)
                                <a href="{{ $tds->tally_data_link }}" class="btn btn-outline-primary" target="_blank">
                                    <i class="fab fa-google-drive me-1"></i> View Tally Data on Google Drive
                                </a>
                            @else
                                <p class="text-muted">No Tally data link provided</p>
                            @endif
                        </div>

                        <!-- TDS Documents Section -->
                        <div class="mb-4 border-top pt-3">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-file-alt me-2"></i>TDS Documents
                            </h5>
                            <div class="row">
                                <!-- TDS Challan -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">TDS Challan</label>
                                    @if ($tds->tds_challan_path)
                                        <a href="{{ asset('storage/' . $tds->tds_challan_path) }}"
                                            class="btn btn-outline-primary d-block" target="_blank">
                                            <i class="fas fa-eye me-1"></i> View Document
                                        </a>
                                    @else
                                        <p class="text-muted">No document uploaded</p>
                                    @endif
                                </div>

                                <!-- TDS Payment Challan -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">TDS Payment Challan</label>
                                    @if ($tds->tds_payment_challan_path)
                                        <a href="{{ asset('storage/' . $tds->tds_payment_challan_path) }}"
                                            class="btn btn-outline-primary d-block" target="_blank">
                                            <i class="fas fa-eye me-1"></i> View Document
                                        </a>
                                    @else
                                        <p class="text-muted">No document uploaded</p>
                                    @endif
                                </div>

                                <!-- TDS Return -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">TDS Return</label>
                                    @if ($tds->tds_return_path)
                                        <a href="{{ asset('storage/' . $tds->tds_return_path) }}"
                                            class="btn btn-outline-primary d-block" target="_blank">
                                            <i class="fas fa-eye me-1"></i> View Document
                                        </a>
                                    @else
                                        <p class="text-muted">No document uploaded</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- TDS Paid Table -->
                        <div class="mb-4 border-top pt-3">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-rupee-sign me-2"></i>TDS Payments
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Section</th>
                                            <th>Amount (₹)</th>
                                            <th>UTR Message</th>
                                            <th>Payment Date & Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tds->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->section }}</td>
                                                <td>₹{{ number_format($payment->amount, 2) }}</td>
                                                <td>{{ $payment->utr_message }}</td>
                                                <td>{{ $payment->payment_date->format('d M Y, h:i A') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No payment records found</td>
                                            </tr>
                                        @endforelse
                                        @if ($tds->payments->isNotEmpty())
                                            <tr class="table-active">
                                                <td><strong>Total</strong></td>
                                                <td><strong>₹{{ number_format($tds->payments->sum('amount'), 2) }}</strong>
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Audit Trail -->
                        @if ($tds->activities->isNotEmpty())
                            <div class="border-top pt-3">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-history me-2"></i>Audit Trail
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date/Time</th>
                                                <th>User</th>
                                                <th>Action</th>
                                                <th>Changes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tds->activities as $activity)
                                                <tr>
                                                    <td>{{ $activity->created_at->format('d M Y, h:i A') }}</td>
                                                    <td>{{ $activity->causer->name ?? 'System' }}</td>
                                                    <td>{{ $activity->description }}</td>
                                                    <td>
                                                        @if ($activity->properties->has('attributes'))
                                                            <div class="changes">
                                                                @foreach ($activity->properties->get('attributes') as $key => $value)
                                                                    @if (!in_array($key, ['updated_at', 'created_at']))
                                                                        <div><strong>{{ $key }}:</strong>
                                                                            {{ $value }}</div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
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

        .form-control-static {
            padding: 0.375rem 0;
            margin-bottom: 0;
            line-height: 1.5;
            background-color: transparent;
            border: solid transparent;
            border-width: 1px 0;
        }

        .border-top {
            border-top: 1px solid #dee2e6 !important;
        }

        .changes div {
            margin-bottom: 3px;
            font-size: 0.875rem;
        }
    </style>
@endpush
