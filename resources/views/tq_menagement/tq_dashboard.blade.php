@extends('layouts.app')
@section('page-title', 'TQ Dashboard')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-pendingTQ-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-pendingTQ" type="button" role="tab"
                                        aria-controls="nav-pendingTQ" aria-selected="true">TQ Pending</button>
                                    <button class="nav-link" id="nav-submittedTQ-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-submittedTQ" type="button" role="tab"
                                        aria-controls="nav-submittedTQ" aria-selected="false">TQ Submitted</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-pendingTQ" role="tabpanel"
                                    aria-labelledby="nav-pendingTQ-tab">
                                    <div class="table-responsive">
                                        <table class="table dataTable" id="allUsers">
                                            <thead>
                                                <tr>
                                                    <th>Tender No.</th>
                                                    <th>Tender Name</th>
                                                    <th>Bid Submission Date</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tqPending as $key => $row)
                                                    <tr>
                                                        <td>{{ $row->tender_no }}</td>
                                                        <td>{{ $row->tender_name }}</td>
                                                        <td>
                                                            {{ $row->bs ? date('d-m-Y H:i A', strtotime($row->bs->bid_submissions_date)) : '' }}
                                                        </td>
                                                        <td></td>
                                                        <td>
                                                            @php
                                                                $timer = $row->getTimer('bid_submission');
                                                                if ($timer) {
                                                                    $start = $timer->start_time;
                                                                    $hrs = $timer->duration_hours;
                                                                    $end = strtotime($start) + $hrs * 60 * 60;
                                                                    $remaining = $end - time();
                                                                } else {
                                                                    $remained = $row->remainedTime('bid_submission');
                                                                }
                                                            @endphp
                                                            @if ($timer)
                                                                {{-- Sortable timer --}}
                                                                <span class="d-none">{{ $remaining }}</span>
                                                                <span class="timer" id="timer-{{ $row->id }}"
                                                                    data-remaining="{{ $remaining }}"></span>
                                                            @else
                                                                <span class="d-none">0</span>
                                                                {!! $remained !!}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ asset('admin/view_butten/' . Crypt::encrypt($row->id)) }}"
                                                                class="btn btn-info btn-sm">
                                                                View
                                                            </a>
                                                            <a href="{{ asset('admin/tq_received_form/' . Crypt::encrypt($row->id)) }}"
                                                                class="btn btn-primary btn-sm">
                                                                TQ Received
                                                            </a>
                                                            <a href="{{ asset('admin/tq_replied_form/' . Crypt::encrypt($row->id)) }}"
                                                                class="btn btn-secondary btn-sm">
                                                                TQ Replied
                                                            </a>
                                                            <br>
                                                            <a href="{{ asset('admin/tq_missed_form/' . Crypt::encrypt($row->id)) }}"
                                                                class=" mt-1 btn btn-danger btn-sm">
                                                                TQ Missed
                                                            </a>
                                                            <a href=""
                                                                class=" mt-1 btn btn-warning btn-sm">Qualified</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-submittedTQ" role="tabpanel"
                                    aria-labelledby="nav-submittedTQ-tab">
                                    <div class="table-responsive">
                                        <table class="table dataTable" id="allUsers">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No.</th>
                                                    <th>Tender No.</th>
                                                    <th>Tender Name</th>
                                                    <th>Bid Submission Date</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tqSubmitted as $key => $row)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $row->tender_no }}</td>
                                                        <td>{{ $row->tender_name }}</td>
                                                        <td>
                                                            {{ $row->bs ? date('d-m-Y H:i A', strtotime($row->bs->bid_submissions_date)) : '' }}
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>
                                                            <a href="{{ asset('admin/view_butten/' . Crypt::encrypt($row->id)) }}"
                                                                class="btn btn-info btn-sm">
                                                                View
                                                            </a>
                                                            <a href="{{ asset('admin/tq_received_form/' . Crypt::encrypt($row->id)) }}"
                                                                class="btn btn-primary btn-sm">
                                                                TQ Received
                                                            </a>
                                                            <a href="{{ asset('admin/tq_replied_form/' . Crypt::encrypt($row->id)) }}"
                                                                class="btn btn-secondary btn-sm">
                                                                TQ Replied
                                                            </a>
                                                            <br>
                                                            <a href="{{ asset('admin/tq_missed_form/' . Crypt::encrypt($row->id)) }}"
                                                                class=" mt-1 btn btn-danger btn-sm">
                                                                TQ Missed
                                                            </a>
                                                            <a href=""
                                                                class=" mt-1 btn btn-warning btn-sm">Qualified</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
    </script>
@endpush
