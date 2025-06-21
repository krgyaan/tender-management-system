@extends('layouts.app')

@section('page-title', 'View Account Checklist')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('checklists.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
                <div class="card border-0">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-3">Checklist Details</h5>

                        <div class="px-3 py-2">
                            <div class="d-flex mb-3">
                                <span class="fw-bold me-2">Task Name:</span>
                                <div class="fw-medium">{{ $checklist->task_name }}</div>
                            </div>
                            <div class="d-flex mb-3">
                                <span class="fw-bold me-2">Frequency:</span>
                                <div class="fw-medium">{{ $checklist->frequency }}</div>
                            </div>
                            <div class="d-flex mb-3">
                                <span class="fw-bold me-2">Description:</span>
                                <div class="fw-medium">{{ $checklist->description ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
