@extends('layouts.app')
@section('page-title', 'TDS Form')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('tds.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- TDS Excel Sheet Section -->
                            <div class="mb-4">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-file-excel me-2"></i>TDS Excel Sheet
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="tds_excel" class="form-label">Upload TDS Excel Sheet</label>
                                        <input type="file" class="form-control" id="tds_excel" name="tds_excel"
                                            accept=".xlsx,.xls" required>
                                        <div class="form-text text-muted">Accepted formats: XLS, XLSX</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">View Uploaded Sheet</label>
                                        <div class="input-group">
                                            <button type="button" class="btn btn-outline-secondary" disabled
                                                id="view-sheet-btn">
                                                <i class="fas fa-eye me-2"></i>View
                                            </button>
                                        </div>
                                        <div class="form-text text-muted">Available after upload</div>
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
                                        <div class="form-text text-muted">Upload TDS challan copy</div>
                                    </div>

                                    <!-- TDS Payment Challan -->
                                    <div class="col-md-4 mb-3">
                                        <label for="tds_payment_challan" class="form-label">TDS Payment Challan</label>
                                        <input type="file" class="form-control" id="tds_payment_challan"
                                            name="tds_payment_challan">
                                        <div class="form-text text-muted">Upload payment challan copy</div>
                                    </div>

                                    <!-- TDS Return -->
                                    <div class="col-md-4 mb-3">
                                        <label for="tds_return" class="form-label">TDS Return</label>
                                        <input type="file" class="form-control" id="tds_return" name="tds_return">
                                        <div class="form-text text-muted">Upload TDS return file</div>
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
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" name="payments[0][section]"
                                                        required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="payments[0][amount]"
                                                        step="0.01" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="payments[0][utr_message]" required>
                                                </td>
                                                <td>
                                                    <input type="datetime-local" class="form-control"
                                                        name="payments[0][payment_date]" required>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-success add-row-btn">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-text text-muted">Add all TDS payments made</div>
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

        #tds-payments-table input {
            min-width: 120px;
        }

        .add-row-btn {
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

        // Enable view button after file upload
        $('#tds_excel').change(function() {
            if (this.files && this.files[0]) {
                $('#view-sheet-btn').removeClass('btn-outline-secondary').addClass('btn-outline-primary').prop(
                    'disabled', false);
            }
        });

        // View uploaded sheet (placeholder functionality)
        $('#view-sheet-btn').click(function() {
            alert('This would open a preview of the uploaded Excel sheet in a new window.');
        });
    </script>
@endpush
