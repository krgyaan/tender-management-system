@extends('layouts.app')
@section('page-title', 'RA Management')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
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
                        <ul class="nav nav-pills justify-content-center" id="raTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending"
                                    type="button">
                                    RA Applicable
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#completed" type="button">
                                    RA Completed
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content mt-3">
                            @foreach (['pending', 'completed'] as $type)
                                <div class="tab-pane fade {{ $type === 'pending' ? 'show active' : '' }}"
                                    id="{{ $type }}">
                                    <div class="table-responsive">
                                        <table class="table-hover" id="{{ $type }}Table">
                                            <thead>
                                                <tr>
                                                    <th>Tender</th>
                                                    <th>Team Member</th>
                                                    <th>Tender Value</th>
                                                    <th>Item</th>
                                                    <th>Tender Status</th>
                                                    <th>Bid Submission Date</th>
                                                    <th>RA Status</th>
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

    <!-- Schedule RA Modal -->
    <div class="modal fade" id="scheduleRAModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule RA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="scheduleRAForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Technically Qualified</label>
                            <div class="form-check">
                                <input type="radio" name="technically_qualified" value="yes" class="form-check-input"
                                    id="qualified-yes">
                                <label class="form-check-label" for="qualified-yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="technically_qualified" value="no" class="form-check-input"
                                    id="qualified-no">
                                <label class="form-check-label" for="qualified-no">No</label>
                            </div>
                        </div>

                        <div class="mb-3 disqualification-section" style="display: none;">
                            <label class="form-label">Reason for
                                Disqualification</label>
                            <textarea name="disqualification_reason" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="qualification-section" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">No. of Qualified
                                    Parties</label>
                                <input type="number" name="qualified_parties_count" class="form-control">
                                <div class="form-check">
                                    <input type="checkbox" name="parties_count_unknown" class="form-check-input">
                                    <label class="form-check-label">Not Known</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Name of Qualified
                                    Parties</label>
                                <div id="qualified-parties-container">
                                    <div class="input-group mb-2">
                                        <input type="text" name="qualified_parties[]" class="form-control">
                                        <button type="button" class="btn btn-success add-party">+</button>
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="parties_names_unknown" class="form-check-input">
                                    <label class="form-check-label">Not Known</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">RA Start Time</label>
                                <input type="datetime-local" name="start_time" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">RA End Time</label>
                                <input type="datetime-local" name="end_time" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload RA Result Modal -->
    <div class="modal fade" id="uploadRAModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload RA Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="uploadRAForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">RA Result</label>
                            <select name="ra_result" class="form-select">
                                <option value="won">Won</option>
                                <option value="lost">Lost</option>
                                <option value="h1_elimination">H1 Elimination</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">VE L1 at start of RA</label>
                            <div class="form-check">
                                <input type="radio" name="ve_l1_start" value="yes" class="form-check-input">
                                <label class="form-check-label">Yes</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="ve_l1_start" value="no" class="form-check-input">
                                <label class="form-check-label">No</label>
                            </div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">RA Start Price</label>
                            <input type="number" name="ra_start_price" class="form-control" step="0.01">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">RA Close Price</label>
                            <input type="number" name="ra_close_price" class="form-control" step="0.01">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">RA Close Time</label>
                            <input type="datetime-local" name="ra_close_time" class="form-control">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Upload Screenshot of Qualified
                                Parties</label>
                            <input type="file" name="qualified_parties_screenshot" class="form-control">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Upload Screenshot of
                                Decrements</label>
                            <input type="file" name="decrements_screenshot" class="form-control">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Upload Final Result</label>
                            <input type="file" name="final_result" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
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
        const tableTypes = ['pending', 'completed'];

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
                    url: `/ra/data/${type}`,
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
                        data: 'gst_values',
                        name: 'gst_values'
                    },
                    {
                        data: 'item_name.name',
                        name: 'item_name.name',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'tender_status',
                        name: 'tender_status'
                    },
                    {
                        data: 'bid_submissions_date',
                        name: 'bid_submissions_date'
                    },
                    {
                        data: 'ra_status',
                        name: 'ra_status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [5, 'desc']
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
                const activeTab = $('#raTabs .nav-link.active').attr('data-bs-target').replace('#', '');

                if (tables[activeTab]) {
                    tables[activeTab].ajax.reload();
                }
            });

            initializeTable('pending');

            $('#raTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
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
            // Update form action when modal is opened
            $(document).on('click', '.schedule-ra', function () {
                const raId = $(this).data('ra-id');
                const tender = $(this).data('tender');
            
                console.log("Schedule RA clicked. raId:", raId); // Confirm data
            
                const form = $('#scheduleRAForm');
                form.attr('action', `/ra-management/schedule/${raId}`);
            
                form.find('input[name="tender_no"]').remove(); // Avoid duplicates
                form.append(`<input type="hidden" name="tender_no" value="${tender}">`);
            
                form[0].reset();
                $('.qualification-section').hide();
                $('.disqualification-section').hide();
            });

            $('#scheduleRAForm').submit(function (e) {
                console.log('Submitting to:', $(this).attr('action'));
            });

            // Upload RA modal
            $(document).on('click', '.upload-ra', function () {
                const raId = $(this).data('ra-id');
                const tender = $(this).data('tender');
                const form = $('#uploadRAForm');
            
                form.attr('action', `/ra-management/upload-result/${raId}`);
                form.find('input[name="tender_no"]').remove();
                form.append(`<input type="hidden" name="tender_no" value="${tender}">`);
            
                form[0].reset();
            });

            // Handle technically qualified radio buttons
            $('input[name="technically_qualified"]').change(function() {
                if ($(this).val() === 'yes') {
                    $('.qualification-section').show();
                    $('.disqualification-section').hide();
                } else {
                    $('.qualification-section').hide();
                    $('.disqualification-section').show();
                }
            });

            // Add more qualified parties
            $('.add-party').click(function() {
                const container = $('#qualified-parties-container');
                const newRow = `
                <div class="input-group mb-2">
                    <input type="text" name="qualified_parties[]" class="form-control">
                    <button type="button" class="btn btn-danger remove-party">-</button>
                </div>
            `;
                container.append(newRow);
            });

            // Remove qualified party
            $(document).on('click', '.remove-party', function() {
                $(this).closest('.input-group').remove();
            });

            // Handle "Not Known" checkboxes
            $('input[name="parties_count_unknown"]').change(function() {
                const input = $(this).closest('.mb-3').find('input[name="qualified_parties_count"]');
                input.prop('disabled', this.checked);
            });

            $('input[name="parties_names_unknown"]').change(function() {
                const container = $('#qualified-parties-container');
                container.toggle(!this.checked);
            });
        });
    </script>
@endpush
