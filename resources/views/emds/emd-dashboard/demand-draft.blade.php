@extends('layouts.app')
@section('page-title', 'Demand Draft Dashboard')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
                @include('partials.messages')
                <div class="d-flex justify-content-between align-items-center">
                    @if (Auth::user()->role == 'admin')
                        <div class="form-group" style="max-width: 200px">
                            <select id="team-filter" class="form-select">
                                <option value="">All Teams</option>
                                <option value="AC">AC</option>
                                <option value="DC">DC</option>
                            </select>
                        </div>
                    @endif
                    <a href="{{ route('dd-old-entry') }}" class="btn btn-info btn-sm">
                        Update Old Entries
                    </a>
                </div>

                <ul class="nav nav-pills justify-content-center" id="ddTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                            Pending
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#created" type="button">
                            Created
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rejected" type="button">
                            Rejected
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#returned" type="button">
                            Returned
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cancelled" type="button">
                            Cancelled
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3">
                    @foreach (['pending', 'created', 'rejected', 'returned', 'cancelled'] as $type)
                        <div class="tab-pane fade {{ $type === 'pending' ? 'show active' : '' }}" id="{{ $type }}">
                            <div class="table-responsive">
                                <table class="table-hover" id="{{ $type }}Table">
                                    <thead>
                                        <tr>
                                            <th style="white-space: nowrap; max-width: 150px;">DD Creation Date</th>
                                            <th>DD No</th>
                                            <th>Beneficiary name</th>
                                            <th>DD Amount</th>
                                            <th>Tender Name</th>
                                            <th>Tender Status</th>
                                            <th>Member</th>
                                            <th>Expiry</th>
                                            <th>DD Status</th>
                                            <th>Timer</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // window.alert = function(msg) { console.log("Intercepted alert:", msg); }

        const tables = {};
        const tableTypes = ['pending', 'created', 'rejected', 'returned', 'cancelled'];

        function initializeTable(type) {
            if (tables[type]) return;

            tables[type] = $(`#${type}Table`).DataTable({
                serverSide: true,
                orderCellsTop: true,
                processing: true,
                pageLength: 50,
                stateSave: true,
                stateLoadParams: function(settings, data) {
                    data.length = 50;
                },
                ajax: {
                    url: `/dd/data/${type}`,
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
                        data: 'dd_date',
                        name: 'dd_date'
                    },
                    {
                        data: 'dd_no',
                        name: 'dd_no'
                    },
                    {
                        data: 'beneficiary_name',
                        name: 'beneficiary_name'
                    },
                    {
                        data: 'dd_amt',
                        name: 'dd_amt'
                    },
                    {
                        data: 'tender_name',
                        name: 'emd.project_name'
                    },
                    {
                        data: 'tender_status',
                        name: 'emd.tenders.status'
                    },
                    {
                        data: 'team_member',
                        name: 'emd.requested_by'
                    },
                    {
                        data: 'dd_expiry',
                        name: 'dd_expiry'
                    },
                    {
                        data: 'dd_status',
                        name: 'dd_status'
                    },
                    {
                        data: 'timer',
                        name: 'timer',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                search: {
                    return: true,
                },
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
                drawCallback: function() {
                    handleTimers();
                }
            });
        }

        $(document).ready(function() {
            const savedTeam = localStorage.getItem('selectedTeam');
            if (savedTeam) {
                $('#team-filter').val(savedTeam);
            }
            $('#team-filter').on('change', function() {
                const selectedTeam = $(this).val();
                localStorage.setItem('selectedTeam', selectedTeam);

                // Refresh only the active tab
                const activeTab = $('#ddTabs .nav-link.active').attr('data-bs-target').replace('#', '');

                if (tables[activeTab]) {
                    tables[activeTab].ajax.reload();
                }
            });

            initializeTable('pending');

            $('#ddTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const type = $(e.target).data('bs-target').replace('#', '');
                initializeTable(type);
            });

            setInterval(function() {
                tableTypes.forEach(type => {
                    if (tables[type]) {
                        tables[type].ajax.reload(null, false);
                    }
                });
            }, 300000);
        });

        function handleTimers() {
            document.querySelectorAll('.timer').forEach(startCountdown);
        }
    </script>
@endpush
