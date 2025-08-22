@extends('layouts.app')
@section('page-title', 'All Physical Documnents')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
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
                    <a href="{{ route('phydocs.create') }}" class="btn btn-primary btn-sm">Courier Physical
                        Documents</a>
                </div>
                @include('partials.messages')
                <ul class="nav nav-pills justify-content-center" id="phydocTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                            Pending Physical Docs
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sent" type="button">
                            Sent Physical Docs
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    @foreach (['pending', 'sent'] as $type)
                        <div class="tab-pane fade {{ $type === 'pending' ? 'show active' : '' }}" id="{{ $type }}">
                            <div class="table-responsive">
                                <table class="table-hover" id="{{ $type }}Table">
                                    <thead>
                                        <tr>
                                            <th>Tender</th>
                                            <th>Team Member</th>
                                            <th>Due Date/Time</th>
                                            <th>Courier Date</th>
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
        const tableTypes = ['pending', 'sent'];

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
                    url: `/phydocs/data/${type}`,
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
                        data: 'tender_name',
                        name: 'tender_name'
                    },
                    {
                        data: 'users.name',
                        name: 'users.name'
                    },
                    {
                        data: 'due_date',
                        name: 'due_date'
                    },
                    {
                        data: 'courier_date',
                        name: 'courier_date'
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
                order: [],
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
                const activeTab = $('#phydocTabs .nav-link.active').attr('data-bs-target').replace('#', '');

                if (tables[activeTab]) {
                    tables[activeTab].ajax.reload();
                }
            });

            initializeTable('pending');

            $('#phydocTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
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
