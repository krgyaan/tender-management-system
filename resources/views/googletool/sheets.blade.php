@extends('layouts.app')
@section('page-title', 'Tender Costing Sheets')
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
                </div>
                @include('partials.messages')
                <ul class="nav nav-pills justify-content-center" id="costingSheetTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                            Pending Costing Sheet
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#submitted" type="button">
                            Submitted Costing Sheet
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3">
                    @foreach (['pending', 'submitted'] as $type)
                        <div class="tab-pane fade {{ $type === 'pending' ? 'show active' : '' }}" id="{{ $type }}">
                            <div class="table-responsive">
                                <table class="table-hover" id="{{ $type }}Table">
                                    <thead>
                                        <tr>
                                            <th>Tender</th>
                                            <th>Member</th>
                                            <th>Due Date <br>Time</th>
                                            <th>EMD</th>
                                            <th>Tender Value</th>
                                            <th>Final Price</th>
                                            <th>Budget</th>
                                            <th>Gross<br>Margin</th>
                                            <th>Status</th>
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
        <div class="modal fade" id="submit_sheet" tabindex="-1" aria-labelledby="submit_sheetLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="submit_sheetLabel">Submit Sheet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('googletoolssubmitsheet') }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <input type="hidden" name="id" id="id">
                                <label for="final_price" class="form-label">Final Price (GST Inclusive)</label>
                                <input type="number" class="form-control" name="final_price" id="final_price"
                                    min="0" step="any" required>
                            </div>
                            <div class="mb-3">
                                <label for="receipt" class="form-label">Receipt (Pre GST)</label>
                                <input type="number" class="form-control" name="receipt" id="receipt" min="0"
                                    step="any" required>
                            </div>
                            <div class="mb-3">
                                <label for="budget" class="form-label">Budget (Pre GST)</label>
                                <input type="number" class="form-control" name="budget" id="budget" min="0"
                                    step="any" required>
                            </div>
                            <div class="mb-3">
                                <label for="gross_margin" class="form-label">Gross Margin %age</label>
                                <input type="number" class="form-control" name="gross_margin" id="gross_margin"
                                    min="0" max="100" step="any" readonly data-bs-toggle="tooltip"
                                    title="(Receipt - Budget)/Receipt">
                            </div>
                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="3"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // window.alert = function(msg) { console.log("Intercepted alert:", msg); }

        const tables = {};
        const tableTypes = ['pending', 'submitted'];

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
                    url: `/costing-sheet/data/${type}`,
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
                        data: 'emd',
                        name: 'emd'
                    },
                    {
                        data: 'tender_value',
                        name: 'gst_values'
                    },
                    {
                        data: 'final_price',
                        name: 'final_price'
                    },
                    {
                        data: 'budget',
                        name: 'budget'
                    },
                    {
                        data: 'gross_margin',
                        name: 'gross_margin'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
                const activeTab = $('#costingSheetTabs .nav-link.active').attr('data-bs-target').replace(
                    '#', '');

                if (tables[activeTab]) {
                    tables[activeTab].ajax.reload();
                }
            });

            initializeTable('pending');

            $('#costingSheetTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
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

@push('scripts')
    <script>
        $('#submit_sheet').on('show.bs.modal', function(event) {
            let button = $(event.relatedTarget);
            let id = button.data('id');
            $(this).find('input[name="id"]').val(id);

            $(this).find('#receipt, #budget').off('input').on('input', function() {
                let receipt = parseFloat($(this).closest('.modal').find('#receipt').val()) || 0;
                let budget = parseFloat($(this).closest('.modal').find('#budget').val()) || 0;
                let gross_margin = receipt > 0 ? ((receipt - budget) / receipt) * 100 : 0;
                $(this).closest('.modal').find('#gross_margin').val(gross_margin.toFixed(2));
            });
        });

        $('#submit_sheet').on('hidden.bs.modal', function() {
            $(this).find('form').trigger('reset');
        });
    </script>
@endpush
