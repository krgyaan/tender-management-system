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
                        <div class="table-responsive">
                            <table class="table" id="allUsers">
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
                                    @if ($couriers->count() > 0)
                                        @foreach ($couriers as $courier)
                                            @if ($courier->emp_from == Auth::user()->id || Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                <tr>
                                                    <td>{{ $courier->id }}</td>
                                                    <td>{{ $courier->created_at->format('d-m-Y H:i A') }}</td>
                                                    <td>{{ $courier->to_name }}</td>
                                                    <td>{{ $courier->courier_from->name }}</td>
                                                    <td>
                                                        {{ $courier->del_date ? Carbon::parse($courier->del_date)->format('d-m-Y') : '' }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $timer = $courier->getTimer('courier_created');
                                                            if ($timer) {
                                                                $start = $timer->start_time;
                                                                $hrs = $timer->duration_hours;
                                                                $end = strtotime($start) + $hrs * 60 * 60;
                                                                $remaining = $end - time();
                                                            } else {
                                                                $remained = $courier->remainedTime('courier_created');
                                                            }
                                                        @endphp
                                                        @if ($timer)
                                                            <span class="timer" id="timer-{{ $courier->id }}"
                                                                data-remaining="{{ $remaining }}"></span>
                                                        @else
                                                            {!! $remained !!}
                                                        @endif
                                                    </td>
                                                    <td>{{ $courier->courier_provider }}</td>
                                                    <td>
                                                        {{ $courier->pickup_date ? date('d-m-Y h:1 A', strtotime($courier->pickup_date)) : '' }}
                                                    </td>
                                                    <td>{{ $courier->docket_no }}</td>
                                                    <td>
                                                        @if ($courier->status == 4)
                                                            {{ date('d-m-Y', strtotime($courier->delivery_date)) }}
                                                        @else
                                                            {{ $courier->status ? $status[$courier->status] : '' }}
                                                        @endif
                                                    </td>
                                                    <td class="d-flex gap-2 flex-wrap">
                                                        <a type="button" data-id="{{ $courier->id }}"
                                                            data-status="{{ $courier->status }}" data-bs-toggle="modal"
                                                            data-bs-target="#statusModal"
                                                            class="btn btn-primary btn-xs status-btn">
                                                            Status
                                                        </a>
                                                        <a href="{{ route('courier.despatch', $courier->id) }}"
                                                            class="btn btn-success btn-xs">
                                                            Dispatch
                                                        </a>
                                                        <a href="{{ route('courier.show', $courier->id) }}"
                                                            class="btn btn-info btn-xs">
                                                            View
                                                        </a>
                                                        @if ($courier->emp_from == Auth::user()->id || in_array(Auth::user()->role, ['coordinator', 'admin']))
                                                            <form action="{{ route('courier.destroy', $courier->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-xs"
                                                                    onclick="return confirm('Are you sure you want to delete this courier?')">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
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
                                    <option {{ old('status') == $key ? 'selected' : '' }} value="{{ $key }}">
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
            $(document).on('show.bs.modal', function() {
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
            });

            $('.status-btn').click(function() {
                const id = $(this).data('id');
                const status = $(this).data('status');
                $('#id').val(id);
            });

            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
    </script>
@endpush
