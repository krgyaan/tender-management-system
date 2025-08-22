@php
    use Carbon\Carbon;
@endphp
@extends('layouts.app')
@section('page-title', 'Courier Dashboard')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('courier.create') }}" class="btn btn-primary btn-sm">Create Courier</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <ul class="nav nav-pills justify-content-center" id="courierTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending"
                                    type="button">
                                    Pending
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dispatched" type="button">
                                    Dispatched
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#not_delivered"
                                    type="button">
                                    Not Delivered
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#delivered" type="button">
                                    Delivered
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rejected" type="button">
                                    Rejected
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content mt-3">
                            @foreach (['pending', 'dispatched', 'not_delivered', 'delivered', 'rejected'] as $type)
                                <div class="tab-pane fade {{ $type === 'pending' ? 'show active' : '' }}"
                                    id="{{ $type }}">
                                    <div class="table-responsive">
                                        <table class="table-hover" id="{{ $type }}Table">
                                            <thead class="">
                                                <tr>
                                                    <th>Request No</th>
                                                    <th>Request Date</th>
                                                    <th>To (Name)</th>
                                                    <th>From (Name)</th>
                                                    <th>Expected <br>Delivery Date</th>
                                                    <th>Timer</th>
                                                    <th>Courier <br>Provider</th>
                                                    <th>Pickup <br>Date & Time</th>
                                                    <th>Docket No</th>
                                                    <th>Delivery <br>Date & Time</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
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

    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Change Status</h5>
                </div>
                <form action="{{ route('courier.updateStatus') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" @class(['form-conttol'])>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Select Status</option>
                                @foreach ($status as $key => $value)
                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="delivery" style="display: none;">
                            <div class="form-group">
                                <label for="delivery_date">Delivery Date & Time</label>
                                <input type="datetime-local" name="delivery_date" id="delivery_date" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="delivery_pod">Delivery POD Upload</label>
                                <input type="file" name="delivery_pod" id="delivery_pod" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="within_time">Delivery within Expected Time:</label>
                                <select name="within_time" id="within_time" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
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
        $(document).ready(function() {
            let courierTables = {};
            let types = ['pending', 'dispatched', 'not_delivered', 'delivered', 'rejected'];

            function initDataTable(type) {
                if (courierTables[type]) {
                    courierTables[type].ajax.reload();
                    return;
                }
                courierTables[type] = $(`#${type}Table`).DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 50,
                    stateLoadParams: function(settings, data) {
                        data.length = 50;
                    },
                    ajax: {
                        url: '{{ route('courier.getCourierData', ':type') }}'.replace(':type', type),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
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
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'to_name',
                            name: 'to_name'
                        },
                        {
                            data: 'courier_from.name',
                            name: 'courier_from.name'
                        },
                        {
                            data: 'del_date',
                            name: 'del_date'
                        },
                        {
                            data: 'timer',
                            name: 'timer',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'courier_provider',
                            name: 'courier_provider'
                        },
                        {
                            data: 'pickup_date',
                            name: 'pickup_date'
                        },
                        {
                            data: 'docket_no',
                            name: 'docket_no'
                        },
                        {
                            data: 'delivery_date',
                            name: 'delivery_date'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [0, 'asc']
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
                    responsive: true,
                    stateSave: true,
                    drawCallback: function() {
                        handleTimers();
                        handleModalEvents();
                    },
                });
            }
            initDataTable('pending');

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                let type = $(e.target).data('bs-target').replace('#', '');
                initDataTable(type);
            });

            function handleModalEvents() {
                $('.status-btn').click(function() {
                    const id = $(this).data('id');
                    const status = $(this).data('status');
                    $('#id').val(id);
                });
            }
            
            const status = $(this).find('#status');
            status.on('change', function() {
                if (status.val() == '4') {
                    $('.delivery').show();
                    $('#delivery_date').attr('required', true);
                    $('#delivery_pod').attr('required', true);
                    $('#within_time').attr('required', true);
                } else {
                    $('.delivery').hide();
                    $('#delivery_date').removeAttr('required');
                    $('#delivery_pod').removeAttr('required');
                    $('#within_time').removeAttr('required');
                }
            });

            function handleTimers() {
                document.querySelectorAll('.timer').forEach(startCountdown);
            }
        });
    </script>
@endpush
