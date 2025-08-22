@extends('layouts.app')

@section('page-title', 'TDS Records')

@section('content')
    <section>
        <div class="row">
            <div class="col-md-10 m-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">All TDS Records</h5>
                        <a href="{{ route('tds.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tally Link</th>
                                        <th>Total Amount</th>
                                        <th>Payments</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tdsRecords as $record)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ $record->tally_data_link }}" target="_blank"
                                                    class="text-truncate" style="max-width: 150px; display: inline-block;">
                                                    {{ $record->tally_data_link }}
                                                </a>
                                            </td>
                                            <td>₹{{ number_format($record->payments->sum('amount'), 2) }}</td>
                                            <td>{{ $record->payments->count() }}</td>
                                            <td>{{ $record->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('tds.show', $record) }}"
                                                    class="btn btn-sm btn-info" title="View">
                                                    View
                                                </a>
                                                <a href="{{ route('tds.edit', $record) }}"
                                                    class="btn btn-sm btn-warning" title="Edit">
                                                    Edit
                                                </a>
                                                @if (Auth::user()->role == 'admin')
                                                    <form action="{{ route('tds.destroy', $record->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete"
                                                            onclick="return confirm('Are you sure?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $tdsRecords->links() }}
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

        .table th {
            background-color: #f8f9fa;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
@endpush
