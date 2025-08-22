@extends('layouts.app')
@section('page-title', 'Bank Transfer Dashboard')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between">
                    @if (Auth::user()->role == 'admin')
                        <a href="{{ route('emds.export.bt') }}" class="btn btn-outline-success btn-sm">Export</a>
                    @endif
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-pending-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-pending" type="button" role="tab">Pending</button>
                                    <button class="nav-link" id="nav-accepted-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-accepted" type="button" role="tab">Accepted</button>
                                    <button class="nav-link" id="nav-rejected-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-rejected" type="button" role="tab">Rejected</button>
                                    <button class="nav-link" id="nav-returned-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-returned" type="button" role="tab">Returned</button>
                                    <button class="nav-link" id="nav-setteled-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-setteled" type="button" role="tab">Settled</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                @foreach (['pending', 'accepted', 'rejected', 'returned', 'setteled'] as $status)
                                    <div class="tab-pane fade {{ $status === 'pending' ? 'show active' : '' }}"
                                        id="nav-{{ $status }}" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table-hover" id="bt-{{ $status }}-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 100px;">Date</th>
                                                        <th>Team Member</th>
                                                        <th>UTR No</th>
                                                        <th>Account Name</th>
                                                        <th>Tender Name</th>
                                                        <th>Tender Status</th>
                                                        <th>Amount</th>
                                                        <th>BT Status</th>
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
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const tables = {};
            const statuses = ['pending', 'accepted', 'rejected', 'returned', 'setteled'];

            function initializeTable(status) {
                if (tables[status]) return;

                tables[status] = $(`#bt-${status}-table`).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('emds-dashboard.bt.data') }}",
                        data: function (d) {
                            d.status = status; 
                        },
                        error: function (xhr, error, thrown) {
                            console.error('DataTables error:', error, thrown);
                        }
                    },
                    columns: [
                        {
                            data: 'date',
                            name: 'created_at'
                        },
                        {
                            data: 'team_member',
                            name: 'emd.requested_by'
                        },
                        {
                            data: 'utr',
                            name: 'utr'
                        },
                        {
                            data: 'bt_acc_name',
                            name: 'bt_acc_name'
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
                            data: 'amount',
                            name: 'bt_amount'
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
                    pageLength: 25,
                    drawCallback: function () {
                        handleTimers();
                    }
                });
            }

            // Initialize pending table on page load
            initializeTable('pending');

            // Initialize tables on tab change
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const status = $(e.target).attr('id').replace('nav-', '').replace('-tab', '');
                initializeTable(status);
            });

            // Refresh tables periodically
            setInterval(function () {
                statuses.forEach(status => {
                    if (tables[status]) {
                        tables[status].ajax.reload(null, false);
                    }
                });
            }, 300000); // Every 5 minutes

            function handleTimers() {
                document.querySelectorAll('.timer').forEach(startCountdown);
            }
        });
    </script>
@endpush
