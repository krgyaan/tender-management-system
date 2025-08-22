@extends('layouts.app')
@section('page-title', 'Costing Approval')
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
                            Costing Approval Pending
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#submitted" type="button">
                            Costing Approved
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rejected" type="button">
                            Costing Rejected/Redo
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    @foreach (['pending', 'submitted', 'rejected'] as $type)
                        <div class="tab-pane fade {{ $type === 'pending' ? 'show active' : '' }}" id="{{ $type }}">
                            <div class="table-responsive">
                                <table class="table-hover" id="{{ $type }}Table">
                                    <thead>
                                        <tr>
                                            <th>Tender</th>
                                            <th>Member</th>
                                            <th>Due Date <br>Time</th>
                                            <th>EMD</th>
                                            <th>Estimated Cost</th>
                                            <th>Final Costing</th>
                                            <th>Budget</th>
                                            <th>Gross<br> Margin %</th>
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
    </section>

    {{-- Modal --}}
    <div class="modal fade" id="approveSheetModal" tabindex="-1" aria-labelledby="approveSheetModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveSheetModalLabel">Approve Costing Sheet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="mb-3 col-md-6">
                            <label for="costingStatus" class="form-label">Costing Sheet</label>
                            <select class="form-select" id="costingStatus" name="costing_status" required>
                                <option value="">Select Status</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected/Redo">Rejected/Redo</option>
                            </select>
                        </div>
                        <div class="if_approved row" style="display: none;">
                            <div class="mb-3 col-md-6">
                                <input type="hidden" name="id" id="id">
                                <label for="final_price" class="form-label">Final Price (GST Inclusive)</label>
                                <input type="number" class="form-control" name="final_price" id="final_price"
                                    min="0" step="any" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="receipt" class="form-label">Receipt (Pre GST)</label>
                                <input type="number" class="form-control" name="receipt" id="receipt" min="0"
                                    step="any" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="budget" class="form-label">Budget (Pre GST)</label>
                                <input type="number" class="form-control" name="budget" id="budget" min="0"
                                    step="any" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="gross_margin" class="form-label">Gross Margin %age</label>
                                <input type="number" class="form-control" name="gross_margin" id="gross_margin"
                                    min="0" max="100" step="any" readonly data-bs-toggle="tooltip"
                                    title="(Receipt - Budget) / Receipt">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="oem" class="form-label">OEM Name</label>
                                <select name="oem[]" class="form-select select2" id="oem" multiple>
                                    <option value="">Select OEM</option>
                                    @foreach ($oems as $oem)
                                        <option value="{{ $oem->id }}">{{ $oem->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="costingRemarks" class="form-label">Costing Remarks (if any)</label>
                            <textarea class="form-control" id="costingRemarks" name="costing_remarks" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
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
        const tableTypes = ['pending', 'submitted', 'rejected'];

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
                    url: `/costing-approval/data/${type}`,
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
        $(document).ready(function() {
            $('#approveSheetModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var tenderId = button.data('tender-id');
                var form = $(this).find('form');
                var $modal = $(this);
                form.attr('action', '/costing-approval/approve-sheet/' + tenderId);

                function toggleApprovedFields() {
                    if ($modal.find('#costingStatus').val() === 'Approved') {
                        $modal.find('.if_approved').show();
                        $modal.find('.if_approved input:not([readonly]), .if_approved select').attr(
                            'required', true);
                    } else {
                        $modal.find('.if_approved').hide();
                        $modal.find('.if_approved input, .if_approved select').removeAttr('required');
                    }
                }
                // Initial state
                toggleApprovedFields();

                // On status change
                $modal.find('#costingStatus').off('change').on('change', toggleApprovedFields);

                $modal.find('#receipt, #budget').off('input').on('input', function() {
                    let receipt = parseFloat($(this).closest('.modal').find('#receipt').val()) || 0;
                    let budget = parseFloat($(this).closest('.modal').find('#budget').val()) || 0;
                    let gross_margin = receipt > 0 ? ((receipt - budget) / receipt) * 100 : 0;
                    $(this).closest('.modal').find('#gross_margin').val(gross_margin.toFixed(2));
                });

                $modal.find('.select2').select2({
                    placeholder: 'Select OEM',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $modal
                });
            });
        });
    </script>
@endpush
