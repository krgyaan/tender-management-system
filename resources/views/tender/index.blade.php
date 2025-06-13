@extends('layouts.app')
@section('page-title', 'All Tenders Info')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills justify-content-center" id="tenderTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#prep" type="button">
                                Under Preparation
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dnb" type="button">
                                Do Not Bid
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bid" type="button">
                                Bidding
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
@endsection

@push('scripts')
    <script>
        const tables = {};
        const tableTypes = ['prep', 'dnb', 'bid', 'won', 'lost'];

        function initializeTable(type) {
            if (tables[type]) return;

            tables[type] = $(`#${type}Table`).DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: `/tender/data/${type}`,
                    method: 'GET',
                    data: function(d) {
                        d.search = {
                            value: d.search.value
                        };
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
                        name: 'gst_values',
                        render: function(data) {
                            return data ? parseFloat(data).toLocaleString('en-IN', {
                                style: 'currency',
                                currency: 'INR'
                            }) : 'N/A';
                        }
                    },
                    {
                        data: 'tender_fees',
                        name: 'tender_fees',
                        render: function(data) {
                            return data ? parseFloat(data).toLocaleString('en-IN', {
                                style: 'currency',
                                currency: 'INR'
                            }) : 'N/A';
                        }
                    },
                    {
                        data: 'emd',
                        name: 'emd',
                        render: function(data) {
                            return data ? parseFloat(data).toLocaleString('en-IN', {
                                style: 'currency',
                                currency: 'INR'
                            }) : 'N/A';
                        }
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
                    return: true
                },
                pageLength: 25,
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
                responsive: true,
                stateSave: true,
                drawCallback: function() {
                    const timers = document.querySelectorAll('.timer');
                    timers.forEach(startCountdown);

                    $('#exampleModal').on('show.bs.modal', function(event) {
                        var button = $(event.relatedTarget);
                        var id = button.data('id');
                        var name = button.data('name');
                        var modal = $(this);
                        modal.find('.modal-body #id').val(id);
                        // select the option with a matching value
                        modal.find('.modal-body #status option[value="' + name + '"]').prop('selected',
                            true);
                    });
                }
            });
        }

        $(document).ready(function() {
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
    </script>
@endpush
