@extends('layouts.app')
@section('page-title', 'All EMD Eligible Tenders')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <button title="No longer allowed by Admin" data-bs-toggle="tooltip" class="btn btn-primary btn-sm">
                        Request EMD (other than tms)
                    </button>
                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                        data-bs-target="#old-entries-modal">
                        EMD Old Entries
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                        data-bs-target="#without-tender-modal">
                        BI (Other Than EMD)
                    </button>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                        data-bs-target="#tender-fees-modal">
                        Tender Fees (other than TMS)
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="form-group" style="max-width: 200px">
                                @if (Auth::user()->role == 'admin')
                                    <select id="team-filter" class="form-select">
                                        <option value="">All Teams</option>
                                        <option value="AC">AC</option>
                                        <option value="DC">DC</option>
                                    </select>
                                @endif
                            </div>
                            <ul class="nav nav-pills justify-content-center" id="emdTabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending"
                                        type="button">
                                        EMD Request Pending
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sent" type="button">
                                        EMD Request Sent
                                    </button>
                                </li>
                            </ul>
                            <div></div>
                        </div>
                        <div class="tab-content mt-3">
                            @foreach (['pending', 'sent'] as $type)
                                <div class="tab-pane fade {{ $type === 'pending' ? 'show active' : '' }}"
                                    id="{{ $type }}">
                                    <div class="table-responsive">
                                        <table class="table-hover" id="{{ $type }}Table">
                                            <thead>
                                                <tr>
                                                    <th>Tender</th>
                                                    <th>Tender <br>Values</th>
                                                    <th>Tender EMD</th>
                                                    <th>Tender <br> Fees</th>
                                                    <th>Team <br> Member</th>
                                                    <th>Due date & time</th>
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

        <div class="modal fade" id="old-entries-modal" tabindex="-1" aria-labelledby="old-entries-modal-label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="old-entries-modal-label">Create Old Entries</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 py-3">
                                <h4 class="text-center">Select BI Type for Old Entries</h4>
                            </div>
                            <div class="col-md-12 d-flex justify-content-center flex-wrap gap-2">
                                <a href="{{ route('dd-old-entry') }}" class="btn btn-sm btn-light">
                                    Demand Draft (DD)
                                </a>
                                <a href="{{ route('bg-old-entry') }}" class="btn btn-sm btn-light">
                                    Bank Guarantee
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="without-tender-modal" tabindex="-1" aria-labelledby="without-tender-modal-label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="without-tender-modal-label">Create EMD for other than Tender</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 py-3">
                                <h4 class="text-center">Select BI Type for other than Tender</h4>
                            </div>
                            <div class="col-md-12 d-flex justify-content-center flex-wrap gap-2">
                                <a href="{{ route('cheque-ott-entry') }}" class="btn btn-sm btn-light">
                                    Cheque
                                </a>
                                <a href="{{ route('dd-ott-entry') }}" class="btn btn-sm btn-light">
                                    Demand Draft (DD)
                                </a>
                                <a href="{{ route('bg-ott-entry') }}" class="btn btn-sm btn-light">
                                    Bank Guarantee
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="tender-fees-modal" tabindex="-1" aria-labelledby="tender-fees-modal-label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tender-fees-modal-label">Tender Fees</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 py-3">
                                <h4 class="text-center">Select Tender Fees Format</h4>
                            </div>
                            <div class="col-md-12 d-flex justify-content-center flex-wrap gap-2">
                                <a href="{{ route('tender-fees.create', ['type' => '1']) }}" class="btn btn-sm btn-info">
                                    Demand Draft (DD)
                                </a>
                                <a href="{{ route('tender-fees.create', ['type' => '5']) }}" class="btn btn-sm btn-info">
                                    Bank Transfer
                                </a>
                                <a href="{{ route('tender-fees.create', ['type' => '6']) }}" class="btn btn-sm btn-info">
                                    Pay on Portal
                                </a>
                            </div>
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
                    url: `/emd/data/${type}`,
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
                        data: 'gst_values',
                        name: 'gst_values'
                    },
                    {
                        data: 'tender_emd',
                        name: 'tender_emd'
                    },
                    {
                        data: 'tender_fees',
                        name: 'tender_fees'
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
                const activeTab = $('#emdTabs .nav-link.active').attr('data-bs-target').replace('#', '');

                if (tables[activeTab]) {
                    tables[activeTab].ajax.reload();
                }
            });

            initializeTable('pending');

            $('#emdTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
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
