@extends('layouts.app')

@section('page-title', 'Lead Details')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header pb-3 text-uppercase">
                <h3> {{ $lead->company_name }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Contact Person:</strong> {{ $lead->name }}</p>
                        <p><strong>Designation:</strong> {{ $lead->designation }}</p>
                        <p><strong>Phone:</strong> {{ $lead->phone }}</p>
                        <p><strong>Email:</strong> {{ $lead->email }}</p>
                        <p><strong>Address:</strong> {{ $lead->address }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>State:</strong> {{ $lead->state }}</p>
                        <p><strong>Type:</strong> {{ $lead->type }}</p>
                        <p><strong>Industry:</strong> {{ $lead->industry }}</p>
                        <p><strong>Team:</strong> {{ $lead->team }}</p>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Points Discussed</h5>
                            </div>
                            <div class="card-body">
                                {{ $lead->points_discussed }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>VE Responsibility</h5>
                            </div>
                            <div class="card-body">
                                {{ $lead->ve_responsibility }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 text-end">
                    <a href="{{ route('lead.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection
