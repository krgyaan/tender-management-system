@extends('layouts.app')
@section('page-title', 'GST 3B Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-semibold">GST 3B Forms</h5>
                    <a href="{{ route('gst3b.create') }}" class="btn btn-primary mt-2 mb-3">+ Add
                        Expense</a>

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>GST TDS Amount</th>
                                    <th>Gst Paid</th>
                                    <th>Status</th>
                                    <th>Filed Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gst3bs as $gst3b)
                                    <tr class="border-top">
                                        <td>{{ $gst3b->amount }}</td>
                                        <td>{{ $gst3b->gst_tds_amount }}</td>
                                        <td>
                                            @if ($gst3b->gst_paid)
                                                Yes
                                            @else
                                                No
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $gst3b->status === 'filed' ? 'success' : ($gst3b->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($gst3b->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $gst3b->filed_date ? \Carbon\Carbon::parse($gst3b->filed_date)->format('d M Y') : 'Not Filed' }}
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <a href="{{ route('gst3b.show', $gst3b->id) }}"
                                                class="btn btn-sm btn-info mb-1">View</a>
                                            <a href="{{ route('gst3b.edit', $gst3b->id) }}"
                                                class="btn btn-sm btn-warning mb-1">Edit</a>
                                            <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal"
                                                data-bs-target="#uploadModal{{ $gst3b->id }}">Upload</button>
                                        </td>
                                    </tr>

                                    <!-- Upload Modal -->
                                    <div class="modal fade" id="uploadModal{{ $gst3b->id }}" tabindex="-1"
                                        aria-labelledby="uploadModalLabel{{ $gst3b->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="POST"
                                                action="{{ route('gst3b.uploadPaymentChallan', $gst3b->id) }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="uploadModalLabel{{ $gst3b->id }}">
                                                            Upload Payment
                                                            Challan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="payment_challan" class="form-label">Select
                                                                File</label>
                                                            <input type="file" name="payment_challan"
                                                                class="form-control" required>
                                                            <small class="text-muted">Accepted formats: PDF, JPG,
                                                                PNG</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Upload</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <tr class="border-top">
                                        <td colspan="5" class="text-center py-4">No GST 3B records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($gst3bs->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            <nav aria-label="Page navigation">
                                <ul class="pagination mb-0">
                                    @if ($gst3bs->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">Prev</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $gst3bs->previousPageUrl() }}">Prev</a>
                                        </li>
                                    @endif

                                    <li class="page-item active">
                                        <span class="page-link">{{ $gst3bs->currentPage() }}</span>
                                    </li>

                                    @if ($gst3bs->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $gst3bs->nextPageUrl() }}">Next</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">Next</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </section>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Auto-dismiss alerts after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut('slow');
                }, 5000);
            });
        </script>
    @endpush
@endsection
