@extends('layouts.app')
@section('page-title', 'Customer Service')
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

    <div class="modal fade" id="allotEngineerModal" tabindex="-1" aria-labelledby="allotEngineerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="allotEngineerModalLabel">Allot Service Engineer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <form id="allotEngineerForm" action="/customer-service/allotServiceEngineer" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="engineerName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="engineerName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="engineerEmail" class="form-label">Email ID</label>
                            <input type="email" class="form-control" id="engineerEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="engineerPhone" class="form-label">Phone No</label>
                            <input type="text" class="form-control" id="engineerPhone" name="phone" required>
                        </div>

                        <input type="hidden" id="complaintId" value="" name="complaint_id">
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="allotEngineerForm" class="btn btn-primary btn-sm">Save</button>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {

            $('#complaints-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('customer_service.getData') }}", // Your route for getCustomerComplaintsData
                columns: [{
                        data: 'customer',
                        name: 'customer'
                    }, // Call No.
                    {
                        data: 'organization',
                        name: 'organization'
                    }, // Organization Name
                    {
                        data: 'project',
                        name: 'project'
                    }, // Site/Project Name
                    {
                        data: 'location',
                        name: 'site_location'
                    }, // match backend alias
                    {
                        data: 'issue',
                        name: 'issue_faced'
                    }, // Issue Faced
                    {
                        data: 'status',
                        name: 'status'
                    }, // Status
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }, // Actions
                    {
                        data: 'timer',
                        name: 'timer'
                    },
                ]
            });

            $('#complaints-table').on('click', '#allotServiceEngineerBtn', function() {
                let complaintId = $(this).data('complaint-id');

                console.log("Clicked Complaint ID:", complaintId);

                // Populate modal inputs
                $('#complaintId').val(complaintId);


                // Open modal (in case you want to manually trigger it)
                $('#allotEngineerModal').modal('show');
            });
        });
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            let addServiceEngineerBtns = document.querySelectorAll('#allotServiceEngineerBtn');
            console.log(addServiceEngineerBtns);
            addServiceEngineerBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    let complaintId = this.getAttribute('data-id');
                    console.log("Clicked Complaint ID:", complaintId);

                    // set complaintId inside hidden input of modal
                    document.getElementById('complaintId').value = complaintId;
                });
            });
        });
    </script> --}}
@endpush
