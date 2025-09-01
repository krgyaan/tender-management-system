@extends('layouts.app')
@section('page-title', 'AMC Dashboard')


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body ">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Team Filter -->
                        <div class="mb-3 d-flex justify-content-center" style="max-width: 200px">
                            <select id="team-filter" class="form-select">
                                <option value="">All Teams</option>
                                <option value="AC">AC</option>
                                <option value="DC">DC</option>
                            </select>
                        </div>
                        <a href="{{ route('amc.create') }}" class="btn btn-sm btn-primary">New AMC Entry</a>
                    </div>
                    <!-- Tab Navigation -->
                    <ul class="nav nav-pills justify-content-center" id="tenderTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#serviceDue"
                                type="button">
                                Service Due
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#serviceDone" type="button">
                                Service Done
                            </button>
                        </li>
                    </ul>

                    @include('partials.messages')

                    <!-- Tab Content -->
                    <div class="tab-content mt-3">
                        @foreach (['serviceDue', 'serviceDone'] as $type)
                            <div class="tab-pane fade {{ $type === 'serviceDue' ? 'show active' : '' }}"
                                id="{{ $type }}">
                                <div class="table-responsive">
                                    <table class="table-hover" id="{{ $type }}Table">
                                        <thead>
                                            <tr>
                                                <th>Project Name</th>
                                                <th>Site Name</th>
                                                <th>Contact Details</th>
                                                <th>Next Service Due</th>
                                                <th>Service Engineer Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Status</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" class="form-control">
                    <select name="status" id="status" class="form-control">
                        @php $statuses = App\Models\Status::all(); @endphp
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Upload Filled Service Report Modal -->
    <div class="modal fade" id="uploadFilledReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('amc.service-report.upload') }}" enctype="multipart/form-data"
                class="modal-content">
                @csrf
                <input type="hidden" name="amc_id" id="filledAmcId">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Filled Service Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="file" name="service_report" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Signed Service Report Modal -->
    <div class="modal fade" id="uploadSignedReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('amc.signed-service-report.upload') }}" enctype="multipart/form-data"
                class="modal-content">
                @csrf
                <input type="hidden" name="amc_id" id="signedAmcId">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Signed Service Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="file" name="signed_service_report" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        const tables = {};
        const tableTypes = ['serviceDue', 'serviceDone'];

        function initializeTable(type) {
            if (tables[type]) return;

            tables[type] = $(`#${type}Table`).DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: `/services/amc/getData/${type}`,
                    method: 'GET',
                    data: function(d) {
                        d.team = $('#team-filter').val();
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            console.error(xhr.responseJSON.message);
                        } else {
                            console.error('Error loading data. Please try again.');
                        }
                    }
                },
                columns: [{
                        data: 'project_name',
                        name: 'project_name'
                    },
                    {
                        data: 'site_name',
                        name: 'site_name'
                    },
                    {
                        data: 'contact_details',
                        name: 'contact_details',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'next_service_due',
                        name: 'next_service_due'
                    },
                    {
                        data: 'engineer_name',
                        name: 'engineer_name'
                    },
                    {
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                pageLength: 50,
                language: {
                    zeroRecords: 'No matching records found',
                    emptyTable: 'No data available in table',
                    paginate: {
                        first: 'First',
                        previous: 'Previous',
                        next: 'Next',
                        last: 'Last'
                    }
                },
                responsive: true,
                stateSave: true,
                drawCallback: function() {
                    handleModalEvents();
                }
            });
        }

        function handleModalEvents() {
            // Unbind previous events to prevent duplicate triggers
            $('#uploadFilledReportModal').off('show.bs.modal');
            $('#uploadSignedReportModal').off('show.bs.modal');
            $('#editReportModal').off('show.bs.modal');

            // Filled Report Modal
            $('#uploadFilledReportModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const amcId = button.data('amc-id');
                $(this).find('#filledAmcId').val(amcId);
            });

            // Signed Report Modal
            $('#uploadSignedReportModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const amcId = button.data('amc-id');
                $(this).find('#signedAmcId').val(amcId);
            });

            // Edit Report Modal
            $('#editReportModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const amcId = button.data('amc-id');
                $(this).find('#editAmcId').val(amcId);
            });
        }

        $(document).ready(function() {
            const savedTeam = localStorage.getItem('selectedTeam');
            if (savedTeam) $('#team-filter').val(savedTeam);

            $('#team-filter').on('change', function() {
                const selected = $(this).val();
                localStorage.setItem('selectedTeam', selected);
                const activeTab = $('#tenderTabs .nav-link.active').data('bs-target').replace('#', '');
                if (tables[activeTab]) tables[activeTab].ajax.reload();
            });

            initializeTable('serviceDue');

            $('#tenderTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const type = $(e.target).data('bs-target').replace('#', '');
                initializeTable(type);
            });

            setInterval(() => {
                tableTypes.forEach(type => {
                    if (tables[type]) tables[type].ajax.reload(null, false);
                });
            }, 300000); // every 5 minutes
        });
    </script>
@endpush
