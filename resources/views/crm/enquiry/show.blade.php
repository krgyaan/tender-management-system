@extends('layouts.app')
@section('page-title', 'Enquiries Details')
@section('content')
    <section>
        <div class="mb-3">
            <a href="{{ route('enquiries.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <!-- Main Enquiry Information Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table-bordered w-100 table-hover" id="prepTable">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <td>{{ $enquiry->lead?->company_name }}</td>
                                <th>Organization Name</th>
                                <td>{{ $enquiry->organisation?->name }}</td>
                            </tr>
                            <tr>
                                <th>BD Lead</th>
                                <td>{{ $enquiry->lead?->bd_lead?->name ?? $enquiry->creator->name }}</td>
                                <th>Enquiry Name</th>
                                <td>{{ $enquiry->enq_name }}</td>
                            </tr>
                            <tr>
                                <th>Item</th>
                                <td>{{ $enquiry->item?->name }}</td>
                                <th>Location</th>
                                <td>{{ $enquiry->location?->address }}</td>
                            </tr>
                            <tr>
                                <th>Approx Value</th>
                                <td>{{ format_inr($enquiry->approx_value) }}</td>
                                <th>Document</th>
                                <td>{!! $enquiry->document_path
                                    ? '<a target="_blank" href="' . asset("uploads/enquiries/{$enquiry->document_path}") . '">View</a>'
                                    : 'N/A' !!}</td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- Contact Persons Section -->
        <div class="card">
            <div class="card-header text-center">
                <h5 class="mb-3">Contact Persons</h5>
            </div>
            <div class="card-body p-0 px-4 pb-4">
                @if ($enquiry->lead?->contacts->count() > 0)
                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead class="">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Source</th>
                                <th>Added On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($enquiry->lead?->contacts->sortByDesc('created_at') as $index => $contact)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $contact->name }}</td>
                                    <td>{{ $contact->designation ?? 'N/A' }}</td>
                                    <td>{{ $contact->phone ?? 'N/A' }}</td>
                                    <td>{{ $contact->email ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $contact->source == 'call_followup' ? 'info' : 'warning' }}">
                                            {{ $contact->source == 'call_followup' ? 'Call' : 'Visit' }}
                                        </span>
                                    </td>
                                    <td>{{ $contact->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info m-3">No contact persons recorded yet.</div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header text-center">
                <h5>Site Visit Details</h5>
            </div>
            <div class="card-body">
                <!-- Visit Followups -->
                @if ($enquiry->siteVisits->count() > 0)
                    <table class="w-100 table-bordered table-striped table-hover">
                        @foreach ($enquiry->siteVisits->sortByDesc('created_at') as $followup)
                            <tbody>
                                <tr>
                                    <th>Assigned To</th>
                                    <td>{{ $followup->assignee->name }}</td>
                                    <th>Scheduled At</th>
                                    <td>
                                        {{ $followup->scheduled_at ? \Carbon\Carbon::parse($followup->scheduled_at)->format('d M Y H:i') : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Conducted At</th>
                                    <td>
                                        {{ $followup->conducted_at ? \Carbon\Carbon::parse($followup->conducted_at)->format('d M Y H:i') : 'N/A' }}
                                    </td>
                                    <th>Additional Notes</th>
                                    <td>{{ $followup->additional_notes ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Information</th>
                                    <td>{{ $followup->information ?? 'N/A' }}</td>
                                    <th>Documents</th>
                                    <td>
                                        <ul>
                                            @forelse (json_decode($followup->documents ?? '[]') as $document)
                                                <li>
                                                    <a>{{ $document }}</a>
                                                </li>
                                            @empty
                                                <li>No documents available</li>
                                            @endforelse
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                @else
                    <div class="alert alert-info">No visit follow-ups recorded yet.</div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        table th,
        table td {
            font-size: 14px;
            padding: 5px;
        }

        table th {
            white-space: nowrap;
            font-weight: bold;
            text-transform: uppercase;
        }

        .nav-tabs .nav-link {
            font-weight: 500;
            border-bottom: 2px solid transparent;
        }
    </style>
@endpush
