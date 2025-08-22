@extends('layouts.app')
@section('page-title', 'Bid Submission')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center m-2">
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
                <ul class="nav nav-pills justify-content-center" id="bidSubmissionTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                            Bid Submission Pending
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#submitted" type="button">
                            Bid Submission Submitted
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
                                            <th>Estimated Cost</th>
                                            <th>Final Costing</th>
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

    <!-- Bid Submitted Modal -->
    <div class="modal fade" id="bidSubmittedModal" tabindex="-1" role="dialog" aria-labelledby="bidSubmittedModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bidSubmittedModalLabel">Bid Submission Details</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="bidSubmittedForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tender_id" id="tenderId">
                        <div class="form-group">
                            <label>Bid Submission Date and Time</label>
                            <input type="datetime-local" class="form-control" name="submission_datetime" required>
                        </div>
                        <div class="form-group">
                            <label>Upload Bid Documents</label>
                            <input type="file" class="form-control" name="bid_documents">
                        </div>
                        <div class="form-group">
                            <label>Upload Proof of Submission</label>
                            <input type="file" class="form-control" name="submission_proof" required>
                        </div>
                        <div class="form-group">
                            <label>Upload Final Bidding Price</label>
                            <input type="file" class="form-control" name="final_price" required>
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

    <!-- Tender Missed Modal -->
    <div class="modal fade" id="tenderMissedModal" tabindex="-1" role="dialog" aria-labelledby="tenderMissedModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenderMissedModalLabel">Tender Missed Report</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="tenderMissedForm" method="POST">
                        @csrf
                        <input type="hidden" name="tender_id" id="missedTenderId">
                        <div class="form-group">
                            <label>Reason for missing the tender</label>
                            <textarea class="form-control" name="reason" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>What would you do to ensure this is not repeated?</label>
                            <textarea class="form-control" name="prevention_steps" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Any improvements needed in the TMS system?</label>
                            <textarea class="form-control" name="system_improvements" rows="3" required></textarea>
                            <small class="text-muted">to help you avoid making same mistake again</small>
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
                    url: `/bid-submission/data/${type}`,
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
                const activeTab = $('#bidSubmissionTabs .nav-link.active').attr('data-bs-target').replace(
                    '#', '');

                if (tables[activeTab]) {
                    tables[activeTab].ajax.reload();
                }
            });

            initializeTable('pending');

            $('#bidSubmissionTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
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
            $('#bidSubmittedModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var tenderId = button.data('tender-id');
                $('#tenderId').val(tenderId);
                $('#bidSubmittedForm').attr('action', '/bid-submission/submit-bid/' + tenderId);
            });

            $('#tenderMissedModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var tenderId = button.data('tender-id');
                $('#missedTenderId').val(tenderId);
                $('#tenderMissedForm').attr('action', '/bid-submission/mark-missed/' + tenderId);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
    </script>
@endpush
