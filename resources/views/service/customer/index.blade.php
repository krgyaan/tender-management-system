@extends('layouts.app')
@section('page-title', 'Leads Management')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('customer_service.create') }}" class="btn btn-sm btn-primary">Register New Complaint</a>
                </div>
                @include('partials.messages')
                <div class="table-responsive">
                    <table class="table-hover" id="complaints-table">
                        <thead>
                            <tr>
                                <th>Call No.</th>
                                <th>Organization Name</th>
                                <th>Site/Project Name</th>
                                <th>Site Location</th>
                                <th>Issue Faced</th>
                                <th>Status</th>
                                <th>Actions</th>
                                <th>Timer</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#complaints-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('customer_service.getData') }}", // Your route for getCustomerComplaintsData
            columns: [
                { data: 'customer', name: 'customer' },                 // Call No.
                { data: 'organization', name: 'organization' },         // Organization Name
                { data: 'project', name: 'project' },                   // Site/Project Name
                { data: 'site_location', name: 'site_location' },       // Site Location
                { data: 'issue_faced', name: 'issue_faced' },           // Issue Faced
                { data: 'status', name: 'status' },                     // Status
                { data: 'action', name: 'action', orderable: false, searchable: false }, // Actions
                { data: 'timer', name: 'timer' },                  
            ]
        });
    });
</script>
@endpush
