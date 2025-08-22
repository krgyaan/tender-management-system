@extends('layouts.app')
@section('page-title', 'Lead Details')
@section('content')
    <section>
        <div class="mb-3">
            <a href="{{ route('lead.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <!-- Main Lead Information Table -->
        <div class="card mb-4">
            <div class="card-header text-center">
                <h5 class="mb-3">Lead Information</h5>
            </div>
            <div class="card-body p-0 px-4 pb-4">
                <table class="w-100 table-bordered mb-0">
                    <colgroup>
                        <col style="width: 25%">
                        <col style="width: 25%">
                        <col style="width: 25%">
                        <col style="width: 25%">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>Company Name</th>
                            <td>{{ $lead->company_name }}</td>
                            <th>Contact Person</th>
                            <td>{{ $lead->name }}</td>
                        </tr>
                        <tr>
                            <th>Designation</th>
                            <td>{{ $lead->designation }}</td>
                            <th>Phone</th>
                            <td>{{ $lead->phone }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $lead->email }}</td>
                            <th>Address</th>
                            <td>{{ $lead->address }}</td>
                        </tr>
                        <tr>
                            <th>Country</th>
                            <td>{{ $lead->country }}</td>
                            <th>State</th>
                            <td>{{ $lead->state }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>{{ $lead->type }}</td>
                            <th>Industry</th>
                            <td>{{ $lead->industry }}</td>
                        </tr>
                        <tr>
                            <th>Team</th>
                            <td>{{ $lead->team }}</td>
                            <th>Enquiry Received</th>
                            <td>{{ $lead->enquiry_received_at ? \Carbon\Carbon::parse($lead->enquiry_received_at)->format('d M Y H:i') : 'Not recorded' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Points and Responsibility Table -->
        <div class="card mb-4">
            <div class="card-header text-center">
                <h5 class="mb-3">Starting Discussion Details</h5>
            </div>
            <div class="card-body p-0 px-4 pb-4">
                <table class="w-100 table-bordered mb-0">
                    <colgroup>
                        <col style="width: 50%">
                        <col style="width: 50%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Points Discussed</th>
                            <th>VE Responsibility</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $lead->points_discussed }}</td>
                            <td>{{ $lead->ve_responsibility }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Follow-up Summary Table -->
        <div class="card mb-4">
            <div class="card-header text-center">
                <h5 class="mb-3">Follow-up Summary</h5>
            </div>
            <div class="card-body p-0 px-4 pb-4">
                <table class="w-100 table-bordered mb-0">
                    <colgroup>
                        <col style="width: 20%">
                        <col style="width: 20%">
                        <col style="width: 20%">
                        <col style="width: 20%">
                        <col style="width: 20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Count</th>
                            <th>Last Activity</th>
                            <th>Next Follow-up</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mail</td>
                            <td>{{ $lead->mail_followup_count }}</td>
                            <td>{{ $lead->last_mail_sent_at ? \Carbon\Carbon::parse($lead->last_mail_sent_at)->format('d M Y H:i') : 'Never' }}
                            </td>
                            <td>
                                {{ $lead->next_followup_date ? \Carbon\Carbon::parse($lead->next_followup_date)->format('d M Y') : 'N/A' }}
                            </td>
                            <td>{{ $lead->mail_followup_count > 0 ? 'Active' : 'Inactive' }}</td>
                        </tr>
                        <tr>
                            <td>Calls</td>
                            <td>{{ $lead->call_followup_count }}</td>
                            <td>
                                {{ $lead->last_call_at ? \Carbon\Carbon::parse($lead->last_call_at)->format('d M Y H:i') : 'Never' }}
                            </td>
                            <td>
                                {{ $lead?->callFollowups?->last()?->next_followup_date ? \Carbon\Carbon::parse($lead?->callFollowups?->last()?->next_followup_date)->format('d M Y') : 'N/A' }}
                            </td>
                            <td>{{ $lead->call_followup_count > 0 ? 'Active' : 'Inactive' }}</td>
                        </tr>
                        <tr>
                            <td>Visits</td>
                            <td>{{ $lead->visit_followup_count }}</td>
                            <td>{{ $lead->last_visit_at ? \Carbon\Carbon::parse($lead->last_visit_at)->format('d M Y H:i') : 'Never' }}
                            </td>
                            <td>
                                {{ $lead?->visitFollowups?->last()?->next_followup_date ? \Carbon\Carbon::parse($lead?->visitFollowups?->last()?->next_followup_date)->format('d M Y') : 'N/A' }}    
                            </td>
                            <td>{{ $lead->visit_followup_count > 0 ? 'Active' : 'Inactive' }}</td>
                        </tr>
                        <tr>
                            <td>Letters</td>
                            <td>{{ $lead->letter_sent_count }}</td>
                            <td>{{ $lead->last_letter_sent_at ? \Carbon\Carbon::parse($lead->last_letter_sent_at)->format('d M Y H:i') : 'Never' }}
                            </td>
                            <td>
                                {{ "NA" }}    
                            </td>
                            <td>{{ $lead->letter_sent_count > 0 ? 'Active' : 'Inactive' }}</td>
                        </tr>
                        <tr>
                            <td>WhatsApp</td>
                            <td>{{ $lead->whatsapp_followup_count }}</td>
                            <td>
                                {{ $lead->last_whatsapp_sent_at ? \Carbon\Carbon::parse($lead->last_whatsapp_sent_at)->format('d M Y H:i') : 'Never' }}
                            </td>
                            <td>
                                {{ "NA" }}
                            </td>
                            <td>{{ $lead->whatsapp_followup_count > 0 ? 'Active' : 'Inactive' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detailed Follow-up Activities -->
        <div class="card">
            <div class="card-header text-center">
                <h5 class="mb-3">Follow-up Activities</h5>
            </div>
            <div class="card-body p-0 px-4 pb-4">
                <ul class="nav nav-tabs" id="followupTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="mail-tab" data-bs-toggle="tab" data-bs-target="#mail"
                            type="button" role="tab">Mail ({{ $lead->mailFollowups->count() }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="call-tab" data-bs-toggle="tab" data-bs-target="#call" type="button"
                            role="tab">Calls ({{ $lead->callFollowups->count() }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="visit-tab" data-bs-toggle="tab" data-bs-target="#visit" type="button"
                            role="tab">Visits ({{ $lead->visitFollowups->count() }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="whatsapp-tab" data-bs-toggle="tab" data-bs-target="#whatsapp"
                            type="button" role="tab">WhatsApp ({{ $lead->whatsappFollowups->count() }})</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="letter-tab" data-bs-toggle="tab" data-bs-target="#letter"
                            type="button" role="tab">Letters ({{ $lead->letterFollowups->count() }})</button>
                    </li>
                </ul>

                <div class="tab-content p-3" id="followupTabContent">
                    <!-- Mail Followups -->
                    <div class="tab-pane fade show active" id="mail" role="tabpanel">
                        @if ($lead->mailFollowups->count() > 0)
                            <table class="w-100 table-bordered table-striped table-hover">
                                <thead class="">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Mail Body</th>
                                        <th>Attachment</th>
                                        <th>Frequency</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lead->mailFollowups->sortByDesc('created_at') as $followup)
                                        <tr>
                                            <td>{{ $followup->sequence_number }}</td>
                                            <td>{{ $followup->created_at->format('d M Y H:i') }}</td>
                                            <td>{!! Str::limit($followup->mail_body, 100) !!}</td>
                                            <td>
                                                @if ($followup->attachment_path)
                                                    <a href="{{ Storage::url($followup->attachment_path) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>
                                            <td>{{ $followup->frequency }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info">No mail follow-ups recorded yet.</div>
                        @endif
                    </div>

                    <!-- Call Followups -->
                    <div class="tab-pane fade" id="call" role="tabpanel">
                        @if ($lead->callFollowups->count() > 0)
                            <table class="w-100 table-bordered table-striped table-hover">
                                <thead class="">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Points Discussed</th>
                                        <th>VE Responsibility</th>
                                        <th>Next Follow-up</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lead->callFollowups->sortByDesc('created_at') as $followup)
                                        <tr>
                                            <td>{{ $followup->sequence_number }}</td>
                                            <td>{{ $followup->created_at->format('d M Y H:i') }}</td>
                                            <td>{!! Str::limit($followup->points_discussed, 100) !!}</td>
                                            <td>{!! Str::limit($followup->responsibility, 100) !!}</td>
                                            <td>{{ $followup->next_followup_date ? \Carbon\Carbon::parse($followup->next_followup_date)->format('d M Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info">No call follow-ups recorded yet.</div>
                        @endif
                    </div>

                    <!-- Visit Followups -->
                    <div class="tab-pane fade" id="visit" role="tabpanel">
                        @if ($lead->visitFollowups->count() > 0)
                            <table class="w-100 table-bordered table-striped table-hover">
                                <thead class="">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Points Discussed</th>
                                        <th>VE Responsibility</th>
                                        <th>Next Follow-up</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lead->visitFollowups->sortByDesc('created_at') as $followup)
                                        <tr>
                                            <td>{{ $followup->sequence_number }}</td>
                                            <td>{{ $followup->created_at->format('d M Y H:i') }}</td>
                                            <td>{!! Str::limit($followup->points_discussed, 100) !!}</td>
                                            <td>{!! Str::limit($followup->responsibility, 100) !!}</td>
                                            <td>{{ $followup->next_followup_date ? \Carbon\Carbon::parse($followup->next_followup_date)->format('d M Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info">No visit follow-ups recorded yet.</div>
                        @endif
                    </div>

                    <!-- WhatsApp Followups -->
                    <div class="tab-pane fade" id="whatsapp" role="tabpanel">
                        @if ($lead->whatsappFollowups->count() > 0)
                            <table class="w-100 table-bordered table-striped table-hover">
                                <thead class="">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lead->whatsappFollowups->sortByDesc('created_at') as $followup)
                                        <tr>
                                            <td>{{ $followup->sequence_number }}</td>
                                            <td>{{ $followup->created_at->format('d M Y H:i') }}</td>
                                            <td>{!! Str::limit($followup->message, 150) !!}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info">No WhatsApp messages recorded yet.</div>
                        @endif
                    </div>

                    <!-- Letter Followups -->
                    <div class="tab-pane fade" id="letter" role="tabpanel">
                        @if ($lead->letterFollowups->count() > 0)
                            <table class="w-100 table-bordered table-striped table-hover">
                                <thead class="">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Courier Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lead->letterFollowups->sortByDesc('created_at') as $followup)
                                        <tr>
                                            <td>{{ $followup->sequence_number }}</td>
                                            <td>{{ $followup->created_at->format('d M Y H:i') }}</td>
                                            <td>{{ $followup->courier_number }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info">No letters recorded yet.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact Persons Section -->
        <div class="card">
            <div class="card-header text-center">
                <h5 class="mb-3">Contact Persons</h5>
            </div>
            <div class="card-body p-0 px-4 pb-4">
                @if ($lead->contacts->count() > 0)
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
                            @foreach ($lead->contacts->sortByDesc('created_at') as $index => $contact)
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
