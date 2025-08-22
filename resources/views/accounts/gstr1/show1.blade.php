@extends('layouts.app')
@section('page-title', 'GST R1 Upload Details')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">GST R1 Upload Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="text-primary mb-0">
                                <i class="fas fa-file-invoice me-2"></i> GST R1 Information
                            </h5>
                            <div>
                                <a href="{{ route('gst-r1.edit', $gstR1->id) }}" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <a href="{{ route('gst-r1.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-list me-1"></i> Back to List
                                </a>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Uploaded At</label>
                                    <p class="form-control-static">{{ $gstR1->created_at->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Updated</label>
                                    <p class="form-control-static">{{ $gstR1->updated_at->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">GST R1 Sheet</label>
                                @if ($gstR1->gst_r1_sheet_path)
                                    <a href="{{ asset('storage/' . $gstR1->gst_r1_sheet_path) }}"
                                        class="btn btn-outline-primary d-block" target="_blank">
                                        <i class="fas fa-download me-1"></i> Download GST R1 Sheet
                                    </a>
                                @else
                                    <p class="text-muted">No GST R1 sheet uploaded</p>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tally Data Link</label>
                                @if ($gstR1->tally_data_link)
                                    <a href="{{ $gstR1->tally_data_link }}" class="btn btn-outline-primary d-block"
                                        target="_blank">
                                        <i class="fab fa-google-drive me-1"></i> View Tally Data
                                    </a>
                                @else
                                    <p class="text-muted">No Tally data link provided</p>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Confirmation Status</label>
                                <p class="form-control-static">
                                    @if ($gstR1->confirmation)
                                        <span class="badge bg-success">Confirmed</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Not Confirmed</span>
                                    @endif
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">GST R1 Return</label>
                                @if ($gstR1->return_file_path)
                                    <a href="{{ asset('storage/' . $gstR1->return_file_path) }}"
                                        class="btn btn-outline-primary d-block" target="_blank">
                                        <i class="fas fa-download me-1"></i> Download Return File
                                    </a>
                                @else
                                    <p class="text-muted">No return file uploaded</p>
                                @endif
                            </div>
                        </div>

                        @if ($gstR1->activities->isNotEmpty())
                            <div class="border-top pt-3">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-history me-2"></i>Activity Log
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
                                            @foreach ($gstR1->activities as $activity)
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

        .changes div {
            margin-bottom: 3px;
            font-size: 0.875rem;
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }
    </style>
@endpush
