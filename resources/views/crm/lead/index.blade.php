@extends('layouts.app')
@section('page-title', 'Leads Management')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('lead.create') }}" class="btn btn-sm btn-primary">Add New Lead</a>
                </div>
                @include('partials.messages')
                <div class="table-responsive">
                    <table class="table-hover" id="prepTable">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Contact Person</th>
                                <th>Industry</th>
                                <th>State</th>
                                <th>Lead Type</th>
                                <th>Team</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const tables = {};
        const tableTypes = ['prep']; // Add more if you have additional tabs (e.g., 'review', 'approved')

        const initializeTable = (type) => {
            const tableId = `#${type}Table`;
            if (tables[type]) return;

            tables[type] = $(tableId).DataTable({
                serverSide: true,
                orderCellsTop: true,
                processing: true,
                pageLength: 50,
                stateSave: true,
                stateLoadParams: function(settings, data) {
                    data.length = 50;
                },
                ajax: {
                    url: `/leads/data/${type}`,
                    method: 'GET',
                    data: d => {
                        d.team = $('#team-filter').val();
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: (xhr, error, thrown) => {
                        console.error('DataTables error:', error, thrown);
                        if (xhr.responseJSON?.message) {
                            console.error(xhr.responseJSON.message);
                        } else {
                            console.error('Error loading data. Please try again.');
                        }
                    }
                },
                columns: [{
                        data: 'company',
                        name: 'company'
                    },
                    {
                        data: 'contact',
                        name: 'contact'
                    },
                    {
                        data: 'industry',
                        name: 'industry'
                    },
                    {
                        data: 'state',
                        name: 'state'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'team',
                        name: 'team'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    zeroRecords: 'No matching records found',
                    emptyTable: 'No data available in table',
                    paginate: {
                        first: 'First',
                        previous: 'Previous',
                        next: 'Next',
                        last: 'Last'
                    }
                }
            });
        };

        $(document).ready(() => {
            initializeTable('prep');

            setInterval(() => {
                tableTypes.forEach(type => {
                    if (tables[type]) tables[type].ajax.reload(null, false);
                });
            }, 300000); // every 5 minutes
        });
    </script>
@endpush
