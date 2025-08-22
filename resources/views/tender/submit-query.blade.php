@extends('layouts.app')
@section('page-title', 'Submit Query for Tender ' . $tender->tender_no)
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('tender.index') }}" class="btn btn-danger btn-sm">back</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <table class="table-bordered table-hover w-100 mb-4">
                            <tr>
                                <td><strong>Tender No:</strong></td>
                                <td>{{ $tender->tender_no }}</td>
                                <td><strong>Tender Name:</strong></td>
                                <td>{{ $tender->tender_name }}</td>
                            </tr>
                        </table>
                        <form action="{{ route('submit_query.store', ['id' => $tender->id]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tender_id" value="{{ $tender->id }}">
                            <!-- Query Table -->
                            <div class="table-responsive">
                                <div class="col-md-12 text-end">
                                    <button type="button" class="btn btn-info btn-sm" id="addRow">Add Row</button>
                                </div>
                                <table class="table-borderless table-hover w-100" id="queryTable">
                                    <thead class="border border-dark">
                                        <tr class="p-2">
                                            <th style="width: 10%">Page No.</th>
                                            <th style="width: 10%">Clause No.</th>
                                            <th style="width: 20%">Type of Query</th>
                                            <th style="width: 30%">Current Statement</th>
                                            <th style="width: 30%">Requested Statement</th>
                                            <th style="width: 10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width: 10%">
                                                <input type="text" class="form-control" name="queries[0][page_no]"
                                                    required>
                                            </td>
                                            <td style="width: 10%">
                                                <input type="text" class="form-control" name="queries[0][clause_no]"
                                                    required>
                                            </td>
                                            <td style="width: 20%">
                                                <select class="form-control" name="queries[0][query_type]" required>
                                                    <option value="">Select Type</option>
                                                    <option value="technical">Technical</option>
                                                    <option value="commercial">Commercial</option>
                                                    <option value="bec">BEC</option>
                                                    <option value="price_bid">Price Bid Format</option>
                                                </select>
                                            </td>
                                            <td style="width: 30%">
                                                <textarea class="form-control" name="queries[0][current_statement]" rows="2" required></textarea>
                                            </td>
                                            <td style="width: 30%">
                                                <textarea class="form-control" name="queries[0][requested_statement]" rows="2" required></textarea>
                                            </td>
                                            <td style="width: 10%">
                                                <button type="button" class="btn btn-danger btn-sm removeRow">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Organisation</label>
                                        <input type="text" class="form-control" name="client_org" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Contact Person Name</label>
                                        <input type="text" class="form-control" name="client_name" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Contact Email</label>
                                        <input type="email" class="form-control" name="client_email" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Contact Phone</label>
                                        <input type="text" class="form-control" name="client_phone" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Submit Query Request</button>
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
        $(document).ready(function() {
            let rowCount = 1;

            // Add new row
            $('#addRow').click(function() {
                const newRow = `
                <tr>
                    <td><input type="text" class="form-control" name="queries[${rowCount}][page_no]" required></td>
                    <td><input type="text" class="form-control" name="queries[${rowCount}][clause_no]" required></td>
                    <td>
                        <select class="form-control" name="queries[${rowCount}][query_type]" required>
                            <option value="">Select Type</option>
                            <option value="technical">Technical</option>
                            <option value="commercial">Commercial</option>
                            <option value="bec">BEC</option>
                            <option value="price_bid">Price Bid Format</option>
                        </select>
                    </td>
                    <td><textarea class="form-control" name="queries[${rowCount}][current_statement]" rows="2" required></textarea></td>
                    <td><textarea class="form-control" name="queries[${rowCount}][requested_statement]" rows="2" required></textarea></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `;
                $('#queryTable tbody').append(newRow);
                rowCount++;
            });

            // Remove row
            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endpush
