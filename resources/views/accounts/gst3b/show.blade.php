@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>GST 3B Form Details</h1>
            <div>
                <a href="{{ route('gst3b.edit', $gst3b->id) }}" class="btn btn-sm btn-primary me-2">
                    Edit
                </a>
                <a href="{{ route('gst3b.index') }}" class="btn btn-sm btn-secondary">
                    Back
                </a>
            </div>
        </div>

        <div class="mb-4">

        </div>

        <table class="table table-bordered">
            <tr>
                <th>Status</th>
                <td>
                    @if ($gst3b->status === 'filed')
                        <span class="badge bg-success">Filed</span>
                    @elseif ($gst3b->status === 'pending')
                        <span class="badge bg-warning">Pending</span>
                    @else
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                </td>
            </tr>
            <tr>
            <tr>
                <th>Tally Data Link</th>
                <td>
                    @if ($gst3b->tally_data_link)
                        <a href="{{ $gst3b->tally_data_link }}" target="_blank">View Link</a>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>GST 2A File</th>
                <td>
                    @if ($gst3b->gst_2a_file_path)
                        <a href="{{ Storage::url($gst3b->gst_2a_file_path) }}" target="_blank">Download</a>
                    @else
                        No file uploaded
                    @endif
                </td>
            </tr>
            <tr>
                <th>GST TDS Accepted</th>
                <td>{{ $gst3b->gst_tds_accepted ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>GST TDS Amount</th>
                <td>₹{{ number_format($gst3b->gst_tds_amount, 2) }}</td>
            </tr>
            <tr>
                <th>GST TDS File</th>
                <td>
                    @if ($gst3b->gst_tds_file_path)
                        <a href="{{ Storage::url($gst3b->gst_tds_file_path) }}" target="_blank">Download</a>
                    @else
                        No file uploaded
                    @endif
                </td>
            </tr>
            <tr>
                <th>GST Paid</th>
                <td>{{ $gst3b->gst_paid ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>Amount</th>
                <td>₹{{ number_format($gst3b->amount, 2) }}</td>
            </tr>
            <tr>
                <th>Payment Challan</th>
                <td>
                    @if ($gst3b->payment_challan_path)
                        <a href="{{ Storage::url($gst3b->payment_challan_path) }}" target="_blank">Download</a>
                    @else
                        No file uploaded
                    @endif
                </td>
            </tr>
        </table>

        @can('approve', $gst3b)
            <div class="border-top pt-3 mt-4">
                <h5>Admin Actions</h5>
                <form action="{{ route('gst3b.approve', $gst3b->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success me-2">
                        Approve
                    </button>
                </form>

                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    Reject
                </button>
            </div>

            <!-- Rejection Modal -->
            <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('gst3b.reject', $gst3b->id) }}" method="POST">
                            @csrf
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="rejectModalLabel">Reject GST 3B Form</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="rejection_reason" class="form-label">Reason for Rejection</label>
                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection
