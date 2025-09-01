@extends('layouts.app')
@section('page-title', 'Service Complaint Details')

@section('content')
    <section class="py-4">
        <div class="card border-0 shadow-lg rounded-3 overflow-hidden">

            {{-- Card Body --}}
            <div class="card-body p-4">
                {{-- Complaint Details --}}
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">Name</label>
                        <div class="form-control bg-light border-0 shadow-sm">{{ $complaint->name }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">Designation</label>
                        <div class="form-control bg-light border-0 shadow-sm">{{ $complaint->designation }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">Email</label>
                        <div class="form-control bg-light border-0 shadow-sm">{{ $complaint->email }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">Phone No.</label>
                        <div class="form-control bg-light border-0 shadow-sm">{{ $complaint->phone }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">PO. No.</label>
                        <div class="form-control bg-light border-0 shadow-sm">{{ $complaint->po_number ?? '-' }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">Photo / Video Uploaded</label>
                        <div class="p-3 bg-light border rounded-3 shadow-sm text-center">
                            @if ($complaint->attachment)
                                @if (Str::endsWith($complaint->attachment, ['.mp4', '.mov', '.avi']))
                                    <video src="{{ asset('storage/complaint/' . $complaint->attachment) }}" controls
                                        class="img-fluid rounded-3"></video>
                                @else
                                    <img src="{{ asset('storage/complaint/' . $complaint->attachment) }}"
                                        alt="Uploaded Media" class="img-fluid rounded-3">
                                @endif
                            @else
                                <span class="badge bg-secondary px-3 py-2">No file uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Service Engineer Section --}}
                <hr class="my-5">

                @if ($complaint->serviceEngineer)
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-person-badge fs-4 text-success me-2"></i>
                        <h5 class="fw-bold text-success mb-0">Allotted Service Engineer</h5>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="text-muted small fw-semibold">Engineer Name</label>
                            <div class="form-control bg-light border-0 shadow-sm">{{ $complaint->serviceEngineer['name'] }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-semibold">Email</label>
                            <div class="form-control bg-light border-0 shadow-sm">{{ $complaint->serviceEngineer['email'] }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-semibold">Phone</label>
                            <div class="form-control bg-light border-0 shadow-sm">{{ $complaint->serviceEngineer['phone'] }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-person-badge fs-4 text-danger me-2"></i>
                        <h5 class="fw-bold text-danger mb-0">No Service Engineer Allotted</h5>
                    </div>
                @endif

                {{-- Back Button --}}
                <div class="mt-5 text-end">
                    <a href="{{ route('customer_service.index') }}"
                        class="btn btn-outline-primary px-4 rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
