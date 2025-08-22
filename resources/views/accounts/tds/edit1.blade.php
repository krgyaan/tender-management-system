@extends('layouts.app')
@section('page-title', 'Edit TDS Form')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Edit TDS Record</h5>
                    </div>
                    <div class="card-body">
                        
                        <?php 
                        // echo"<pre>";
                        // print_r($tds);
                        // die;
                        ?>
                        
                        <form action="{{ route('tds.update', $tds_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- TDS Excel Sheet Section -->
                            <div class="mb-4">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-file-excel me-2"></i>TDS Excel Sheet
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="tds_excel" class="form-label">Update TDS Excel Sheet</label>
                                        <input type="file" class="form-control" id="tds_excel" name="tds_excel"
                                            accept=".xlsx,.xls">
                                        <div class="form-text text-muted">Accepted formats: XLS, XLSX</div>
                                        @if ($tds->tds_excel_path)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $tds->tds_excel_path) }}"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-download me-1"></i> Download Current File
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Tally Data Link -->
                            <div class="mb-4 border-top pt-3">
                                <label for="tally_data_link" class="form-label fw-bold">
                                    <i class="fas fa-link me-2"></i>Tally Data (Google Drive Link)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fab fa-google-drive"></i>
                                    </span>
                                    <input type="url" class="form-control" id="tally_data_link" name="tally_data_link"
                                        value="{{ old('tally_data_link', $tds->tally_data_link) }}"
                                        placeholder="https://drive.google.com/..." required>
                                </div>
                                <div class="form-text text-muted">Please provide a shareable Google Drive link</div>
                            </div>

                            <!-- TDS Documents Section -->
                            <div class="mb-4 border-top pt-3">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-file-alt me-2"></i>TDS Documents
                                </h5>
                                <div class="row">
                                    <!-- TDS Challan -->
                                    <div class="col-md-4 mb-3">
                                        <label for="tds_challan" class="form-label">TDS Challan</label>
                                        <input type="file" class="form-control" id="tds_challan" name="tds_challan">
                                        @if ($tds->tds_challan_path)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $tds->tds_challan_path) }}"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye me-1"></i> View Current
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- TDS Payment Challan -->
                                    <div class="col-md-4 mb-3">
                                        <label for="tds_payment_challan" class="form-label">TDS Payment Challan</label>
                                        <input type="file" class="form-control" id="tds_payment_challan"
                                            name="tds_payment_challan">
                                        @if ($tds->tds_payment_challan_path)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $tds->tds_payment_challan_path) }}"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye me-1"></i> View Current
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- TDS Return -->
                                    <div class="col-md-4 mb-3">
                                        <label for="tds_return" class="form-label">TDS Return</label>
                                        <input type="file" class="form-control" id="tds_return" name="tds_return">
                                        @if ($tds->tds_return_path)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $tds->tds_return_path) }}"
                                                    class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye me-1"></i> View Current
                                                </a>
                                            </div>
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
                                    <table class="table table-bordered" id="tds-payments-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Section</th>
                                                <th>Amount (₹)</th>
                                                <th>UTR Message</th>
                                                <th>Payment Date & Time</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tds->payments as $index => $payment)
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="payments[{{ $index }}][section]"
                                                            value="{{ old('payments.' . $index . '.section', $payment->section) }}"
                                                            required>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control"
                                                            name="payments[{{ $index }}][amount]"
                                                            value="{{ old('payments.' . $index . '.amount', $payment->amount) }}"
                                                            step="0.01" required>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="payments[{{ $index }}][utr_message]"
                                                            value="{{ old('payments.' . $index . '.utr_message', $payment->utr_message) }}"
                                                            required>
                                                    </td>
                                                    <td>
                                                        <input type="datetime-local" class="form-control"
                                                            name="payments[{{ $index }}][payment_date]"
                                                            value="{{ old('payments.' . $index . '.payment_date', $payment->payment_date ? $payment->payment_date->format('Y-m-d\TH:i') : '') }}"
                                                            required>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($index === 0)
                                                            <button type="button"
                                                                class="btn btn-sm btn-success add-row-btn">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        @else
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger remove-row-btn">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-text text-muted">Update TDS payments information</div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between mt-4 border-top pt-3">
                                <a href="{{ route('tds.show', $tds_id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-outline-secondary me-3">
                                        <i class="fas fa-undo me-2"></i>Reset
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
        }

        .form-label {
            font-weight: 500;
        }

        .border-top {
            border-top: 1px solid #dee2e6 !important;
        }

        #tds-payments-table input {
            min-width: 120px;
        }

        .add-row-btn,
        .remove-row-btn {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Add new row to TDS payments table
        $(document).on('click', '.add-row-btn', function() {
            const table = $('#tds-payments-table tbody');
            const rowCount = table.find('tr').length;
            const newRow = `
                <tr>
                    <td>
                        <input type="text" class="form-control" name="payments[${rowCount}][section]" required>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="payments[${rowCount}][amount]" step="0.01" required>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="payments[${rowCount}][utr_message]" required>
                    </td>
                    <td>
                        <input type="datetime-local" class="form-control" name="payments[${rowCount}][payment_date]" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-row-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            table.append(newRow);
        });

        // Remove row from TDS payments table
        $(document).on('click', '.remove-row-btn', function() {
            if ($('#tds-payments-table tbody tr').length > 1) {
                $(this).closest('tr').remove();
                // Renumber the rows
                $('#tds-payments-table tbody tr').each(function(index) {
                    $(this).find('input').each(function() {
                        const name = $(this).attr('name').replace(/\[\d+\]/, '[' + index + ']');
                        $(this).attr('name', name);
                    });
                });
            } else {
                alert('At least one payment entry is required.');
            }
        });
    </script>
@endpush
