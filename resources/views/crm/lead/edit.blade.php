@extends('layouts.app')

@section('page-title', 'Edit Lead')

@section('content')
    <div class="container">

        <div class="card shadow-lg">
            <div class="card-body">

                <form action="{{ route('lead.update', $lead->id) }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT')

                    @foreach ([
            'company_name' => 'Company Name',
            'name' => 'Name',
            'designation' => 'Designation',
            'phone' => 'Phone',
            'email' => 'Email',
            'address' => 'Address',
        ] as $field => $label)
                        <div class="col-md-6">
                            <label class="form-label">{{ $label }}</label>
                            <input type="text" name="{{ $field }}" class="form-control"
                                value="{{ old($field, $lead->$field) }}" required>
                        </div>
                    @endforeach
                    <div class="col-md-6">
                        <label class="form-label">State</label>
                        <select name="state" class="form-select" required>
                            <option value="">Select State</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->name }}"
                                    {{ old('state', $lead->state) == $state->name ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->name }}"
                                    {{ old('type', $lead->type) == $type->name ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Industry</label>
                        <select name="industry" class="form-select" required>
                            <option value="">Select Industry</option>
                            @foreach ($industries as $industry)
                                <option value="{{ $industry->name }}"
                                    {{ old('industry', $lead->industry) == $industry->name ? 'selected' : '' }}>
                                    {{ $industry->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Team</label>
                        <select name="team" class="form-select" required>
                            <option value="">Select Team</option>
                            @foreach (['AC', 'DC', 'IB'] as $team)
                                <option value="{{ $team }}"
                                    {{ old('team', $lead->team) == $team ? 'selected' : '' }}>
                                    {{ $team }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Points Discussed</label>
                        <textarea name="points_discussed" class="form-control" rows="3" placeholder="Enter discussion points...">{{ old('points_discussed', $lead->points_discussed) }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">VE Responsibility</label>
                        <textarea name="ve_responsibility" class="form-control" rows="3" placeholder="Enter VE responsibilities...">{{ old('ve_responsibility', $lead->ve_responsibility) }}</textarea>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-6 text-start">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteConfirmModal">
                                Delete Lead
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="submit" class="btn btn-primary">Update Lead</button>
                            <a href="{{ route('lead.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this lead?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('lead.destroy', $lead->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
