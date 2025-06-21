@extends('layouts.app')


@section('page-title', 'Create New Lead')

@section('content')
    <div class="container mt-2">
        <div class="card shadow-lg">
            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('lead.store') }}" method="POST" class="row g-3">
                    @csrf

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
                            <input type="text" name="{{ $field }}" class="form-control" required>
                        </div>
                    @endforeach

                    <div class="col-md-6">
                        <label class="form-label">State</label>
                        <select name="state" class="form-select" required>
                            <option value="">Select State</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->name }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->name }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Industry</label>
                        <select name="industry" class="form-select" required>
                            <option value="">Select Industry</option>
                            @foreach ($industries as $industry)
                                <option value="{{ $industry->name }}">{{ $industry->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Team</label>
                        <select name="team" class="form-select" required>
                            <option value="">Select Team</option>
                            @foreach (['AC', 'DC', 'IB'] as $team)
                                <option value="{{ $team }}">{{ $team }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Points Discussed</label>
                        <textarea name="points_discussed" class="form-control" rows="3" placeholder="Enter discussion points..."></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">VE Responsibility</label>
                        <textarea name="ve_responsibility" class="form-control" rows="3" placeholder="Enter VE responsibilities..."></textarea>
                    </div>

                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary px-4">Submit Lead</button>
                        <a href="{{ route('lead.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
