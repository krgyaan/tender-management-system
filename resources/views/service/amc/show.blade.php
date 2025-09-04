@extends('layouts.app')
@section('page-title', 'AMC Details')

@section('content')
    <div class="container">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">AMC Details</h5>
                <div>
                    <a href="{{ route('amc.edit', $amc->id) }}" class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('amc.delete', $amc->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this AMC?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <!-- Basic Information -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6>Team Name</h6>
                        <p>{{ strtoupper($amc->team_name) }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6>Project</h6>
                        <p>{{ $amc->project->project_name }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6>Service Frequency</h6>
                        <p>{{ ucfirst($amc->service_frequency) }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6>Start Date</h6>
                        <p>{{ $amc->amc_start_date->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6>End Date</h6>
                        <p>{{ $amc->amc_end_date->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6>Status</h6>
                        <p>
                            <span class="badge bg-{{ $amc->amc_end_date->isFuture() ? 'success' : 'danger' }}">
                                {{ $amc->amc_end_date->isFuture() ? 'Active' : 'Expired' }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Billing Information -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6>Bill Frequency</h6>
                        <p>{{ ucfirst($amc->bill_frequency) }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6>Bill Type</h6>
                        <p>{{ ucfirst($amc->bill_type) }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6>Bill Value</h6>
                        <p>
                            @if ($amc->bill_type == 'constant')
                                ₹{{ number_format($amc->bill_value, 2) }}
                            @else
                                Variable (Total: ₹{{ number_format(collect($amc->variable_bills)->sum('amount'), 2) }})
                            @endif
                        </p>
                    </div>
                </div>

                @if ($amc->bill_type == 'variable')
                    <div class="mb-4">
                        <h5>Variable Bill Schedule</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bill Date</th>
                                        <th>Amount (₹)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($amc->variable_bills as $bill)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($bill['date'])->format('d M Y') }}</td>
                                            <td>{{ number_format($bill['amount'], 2) }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ \Carbon\Carbon::parse($bill['date'])->isPast() ? 'success' : 'warning' }}">
                                                    {{ \Carbon\Carbon::parse($bill['date'])->isPast() ? 'Paid' : 'Pending' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- AMC PO Document -->
                <div class="mb-4">
                    <h5>AMC PO Document</h5>
                    @if ($amc->amc_po_path)
                        <a href="{{ Storage::url($amc->amc_po_path) }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-file-pdf"></i> View PO Document
                        </a>
                    @else
                        <p class="text-muted">No PO document uploaded</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sites Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Sites Information</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="sitesAccordion">
                    @foreach ($amc->sites as $site)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $site->id }}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $site->id }}" aria-expanded="true"
                                    aria-controls="collapse{{ $site->id }}">
                                    {{ $site->name }} - {{ $site->address }}
                                </button>
                            </h2>
                            <div id="collapse{{ $site->id }}" class="accordion-collapse collapse show"
                                aria-labelledby="heading{{ $site->id }}" data-bs-parent="#sitesAccordion">
                                <div class="accordion-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h6>Site Address</h6>
                                            <p>{{ $site->address }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @if ($site->map_link)
                                                <h6>Map Location</h6>
                                                <a href="{{ $site->map_link }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-map-marker-alt"></i> View on Map
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <h6 class="mt-4">Site Contacts</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Organization</th>
                                                    <th>Mobile</th>
                                                    <th>Email</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($site->contacts as $contact)
                                                    <tr>
                                                        <td>{{ $contact->name }}</td>
                                                        <td>{{ $contact->organization ?? 'N/A' }}</td>
                                                        <td>{{ $contact->mobile }}</td>
                                                        <td>{{ $contact->email ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Service Engineers -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Service Engineers</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Organization</th>
                                <th>Mobile</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($amc->engineers as $engineer)
                                <tr>
                                    <td>{{ $engineer->name }}</td>
                                    <td>{{ $engineer->organization ?? 'N/A' }}</td>
                                    <td>{{ $engineer->mobile }}</td>
                                    <td>{{ $engineer->email ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Products Under AMC -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Products Under AMC</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Serial No.</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($amc->products as $product)
                                <tr>
                                    <td>{{ $product->item->name }}</td>
                                    <td>{{ $product->description ?? 'N/A' }}</td>
                                    <td>{{ $product->make ?? 'N/A' }}</td>
                                    <td>{{ $product->model ?? 'N/A' }}</td>
                                    <td>{{ $product->serial_no ?? 'N/A' }}</td>
                                    <td>{{ $product->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #212529;
        }

        .badge {
            font-size: 0.85em;
            padding: 0.35em 0.65em;
        }

        .table th {
            white-space: nowrap;
        }
    </style>
@endsection
