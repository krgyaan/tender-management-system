@extends('layouts.app')
@section('page-title', 'Tender Info Approve')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
                @if (Auth::user()->role == 'admin')
                    <div class="form-group" style="max-width: 200px">
                        <select id="team-filter" class="form-select">
                            <option value="">All Teams</option>
                            <option value="AC">AC</option>
                            <option value="DC">DC</option>
                        </select>
                    </div>
                @endif
                @include('partials.messages')
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-pills justify-content-center" id="tenderTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending"
                                    type="button">
                                    Pending
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#approved" type="button">
                                    Approved
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rejected" type="button">
                                    Rejected
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content mt-3" id="tenderTabsContent">
                            @foreach (['pending', 'approved', 'rejected'] as $type)
                                <div class="tab-pane fade {{ $type === 'pending' ? 'show active' : '' }}"
                                    id="{{ $type }}" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table-hover" id="{{ $type }}Table">
                                            <thead>
                                                <tr>
                                                    <th>Tender</th>
                                                    <th>Team<br> Member</th>
                                                    <th>Due Date/Time</th>
                                                    <th>Tender Value</th>
                                                    <th>Items</th>
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
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        window.alert = function(msg) {
            console.log("Intercepted alert:", msg);
        }

        const tables = {};
        const tableTypes = ['pending', 'approved', 'rejected'];

        function initializeTable(type) {
            if (tables[type]) return;

            tables[type] = $(`#${type}Table`).DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                stateSave: true,
                ordering: true,
                stateLoadParams: function(settings, data) {
                    data.length = 50;
                },
                ajax: {
                    url: `/approve-tender/data/${type}`,
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
                        name: 'users.name',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'due_date',
                        name: 'due_date'
                    },
                    {
                        data: 'gst_values',
                        name: 'gst_values'
                    },
                    {
                        data: 'item_name.name',
                        name: 'item_name.name',
                        defaultContent: 'N/A'
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
                    [2, 'desc']
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
                const activeTab = $('#tenderTabs .nav-link.active').attr('data-bs-target').replace('#', '');

                if (tables[activeTab]) {
                    tables[activeTab].ajax.reload();
                }
            });

            initializeTable('pending');

            $('#tenderTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
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

        function handleModalEvents() {
            $('#exampleModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const name = button.data('name');
                const modal = $(this);
                modal.find('.modal-body #id').val(id);
                modal.find('.modal-body #status option').prop('selected', false);
                modal.find(`.modal-body #status option[value="${name}"]`).prop('selected', true);
            });
        }
    </script>
@endpush
