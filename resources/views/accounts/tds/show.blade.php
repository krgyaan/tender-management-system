@extends('layouts.app')

@section('page-title', 'View TDS Record')

@section('content')
    <section>
        <div class="row">
            <div class="col-md-10 m-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <h6 class="text-dark">Tally Data Link</h6>
                            </div>
                            <div class="col-md-8">
                                <a href="{{ $tds->tally_data_link }}" target="_blank">{{ $tds->tally_data_link }}</a>
                            </div>
                        </div>

                        <div class="border-top pt-3 mb-4">
                            <h5 class="text-primary mb-3">Documents</h5>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <h6>TDS Excel Sheet</h6>
                                    @if ($tds->tds_excel_path)
                                        <a href="{{ Storage::url($tds->tds_excel_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                                <div class="col-md-3 mb-3">
                                    <h6>TDS Challan</h6>
                                    @if ($tds->tds_challan_path)
                                        <a href="{{ Storage::url($tds->tds_challan_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                                <div class="col-md-3 mb-3">
                                    <h6>TDS Payment Challan</h6>
                                    @if ($tds->tds_payment_challan_path)
                                        <a href="{{ Storage::url($tds->tds_payment_challan_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                                <div class="col-md-3 mb-3">
                                    <h6>TDS Return</h6>
                                    @if ($tds->tds_return_path)
                                        <a href="{{ Storage::url($tds->tds_return_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <h5 class="text-primary mb-3">TDS Payments</h5>
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
                                        @foreach ($tds->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->section }}</td>
                                                <td>{{ $payment->utr_message }}</td>
                                                <td>{{ $payment->payment_date->format('d M Y h:i A') }}</td>
                                                <td>₹{{ number_format($payment->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active text-dark">
                                            <th colspan="2"></th>
                                            <th>Total</th>
                                            <th>₹{{ number_format($tds->payments->sum('amount'), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <a href="{{ route('tds.index') }}" class="btn btn-sm btn-secondary me-2">
                                Back
                            </a>
                            <a href="{{ route('tds.edit', $tds->id) }}" class="btn btn-sm btn-primary me-2">
                                Edit
                            </a>
                            <form action="{{ route('tds.destroy', $tds->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this record?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
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
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        h5.text-primary,
        h6.text-primary {
            color: #0d6efd;
        }

        .table th {
            background-color: #f8f9fa;
        }
    </style>
@endpush
