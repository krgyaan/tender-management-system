@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Leads Management</h2>
            <a href="{{ route('lead.create') }}" class="btn btn-primary">Add New Lead</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Industry</th>
                                <th>State</th>
                                <th>Type</th>
                                <th>Team</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($leads as $lead)
                                <tr>
                                    <td>{{ $lead->company_name }}</td>
                                    <td>{{ $lead->name }}</td>
                                    <td>{{ $lead->industry }}</td>
                                    <td>{{ $lead->state }}</td>
                                    <td>{{ $lead->type }}</td>
                                    <td>{{ $lead->team }}</td>
                                    <td>
                                        <a href="{{ route('lead.show', $lead->id) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('lead.edit', $lead->id) }}"
                                            class="btn btn-sm btn-warning">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $leads->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
