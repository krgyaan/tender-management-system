@extends('layouts.app')
@section('page-title', 'All Tenders Info')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                     @if (Auth::user()->role == 'admin')
                        <div class="form-group"  style="max-width: 200px">
                            <select id="team-filter" class="form-select">
                                <option value="">All Teams</option>
                                <option value="AC">AC</option>
                                <option value="DC">DC</option>
                            </select>
                        </div>
                    @endif
                    @if (in_array('tender-create', $permissions))
                        <div class="">
                            <a href="{{ route('tender.create') }}" class="btn btn-primary">Create New Tender</a>
                        </div>
                    @endif
                    </div>
                    <ul class="nav nav-pills justify-content-center" id="tenderTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#prep" type="button">
                                Under Preparation
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dnb" type="button">
                                Did Not Bid
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bid" type="button">
                                Bid Submitted
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#won" type="button">
                                Won
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#lost" type="button">
                                Lost
                            </button>
                        </li>
                    </ul>
                    @include('partials.messages')
                    <div class="tab-content mt-3">
                        @foreach (['prep', 'dnb', 'bid', 'won', 'lost'] as $type)
                            <div class="tab-pane fade {{ $type === 'prep' ? 'show active' : '' }}" id="{{ $type }}">
                                <div class="table-responsive">
                                    <table class="table-hover" id="{{ $type }}Table">
                                        <thead>
                                            <tr>
                                                <th>Tender No</th>
                                                <th>Organization</th>
                                                <th>Tender Name</th>
                                                <th>Items</th>
                                                <th>Values <br> Incl. GST</th>
                                                <th>Tender Fees</th>
                                                <th>Tender EMD</th>
                                                <th>Team Member</th>
                                                <th>Due Date</th>
                                                <th>Status</th>
                                                <th>Timer</th>
                                                <th>Action</th>
                                            </tr>
                                            @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                <tr class="filter-row">
                                                    <th><input type="text" class="form-control form-control-sm"
                                                            placeholder="Search Tender No" /></th>
                                                    <th><input type="text" class="form-control form-control-sm"
                                                            placeholder="Search Org" /></th>
                                                    <th><input type="text" class="form-control form-control-sm"
                                                            placeholder="Search Name" /></th>
                                                    <th><input type="text" class="form-control form-control-sm"
                                                            placeholder="Search Item" /></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th><select class="form-select form-select-sm">
                                                            <option value="">TE</option>
                                                        </select></th> <!-- Team Member -->
                                                    <th></th>
                                                    <th><select class="form-select form-select-sm">
                                                            <option value="">Status</option>
                                                        </select></th> <!-- Status -->
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            @endif
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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Status</h5>
                </div>
                <form action="{{ route('tender.updateStatus') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" @class(['form-conttol'])>
                        <select name="status" id="status" @class(['form-control'])>
                            @php
                                $statuses = App\Models\Status::all();
                            @endphp
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}">
                                    {{ $status->name }}
                                </option>
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
    </div>
@endsection

@push('scripts')
    <script>
        // window.alert = function(msg) { console.log("Intercepted alert:", msg); }

        const tables = {};
        const tableTypes = ['prep', 'dnb', 'bid', 'won', 'lost'];

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
                    url: `/tender/data/${type}`,
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
                        data: 'tender_no',
                        name: 'tender_no'
                    },
                    {
                        data: 'organizations.name',
                        name: 'organizations.name',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'tender_name',
                        name: 'tender_name'
                    },
                    {
                        data: 'item_name.name',
                        name: 'item_name.name',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'gst_values',
                        name: 'gst_values'
                    },
                    {
                        data: 'tender_fees',
                        name: 'tender_fees'
                    },
                    {
                        data: 'emd',
                        name: 'emd'
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
                        data: 'statuses.name',
                        name: 'statuses.name',
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
                    [8, 'desc']
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
                    handleModalEvents();
                },
                initComplete: function() {
                    const api = this.api();

                    api.columns().every(function(index) {
                        const column = this;
                        const $th = $('.filter-row th').eq(index);
                        const $input = $th.find('input');
                        const $select = $th.find('select');

                        if ($input.length) {
                            $input.on('keyup change clear', function() {
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });
                        }

                        if ($select.length) {
                            const uniqueValues = new Set();

                            column.data().each(function(d) {
                                // Clean HTML tags if any (like <br>, etc.)
                                const text = $('<div>').html(d).text().trim();
                                if (text && text !== 'N/A') {
                                    uniqueValues.add(text);
                                }
                            });

                            // Append to select
                            Array.from(uniqueValues).sort().forEach(val => {
                                $select.append(`<option value="${val}">${val}</option>`);
                            });

                            $select.on('change', function() {
                                const val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                            });
                        }
                    });
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

            initializeTable('prep');

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
