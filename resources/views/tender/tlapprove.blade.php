@extends('layouts.app')
@section('page-title', 'Tender Info Approve')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="bd-example">
                                    <nav>
                                        <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-home" type="button" role="tab"
                                                aria-controls="nav-home" aria-selected="true">Pending</button>
                                            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-profile" type="button" role="tab"
                                                aria-controls="nav-profile" aria-selected="false">Approved</button>
                                            <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-contact" type="button" role="tab"
                                                aria-controls="nav-contact" aria-selected="false">Rejected</button>
                                        </div>
                                    </nav>
                                    <div class="tab-content" id="nav-tabContent">
                                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                            aria-labelledby="nav-home-tab">
                                            <div class="table-responsive">
                                                <table class="table" id="allUsers">
                                                    <thead class="">
                                                        <tr>
                                                            <th>Tender No</th>
                                                            <th>Tender<br> Name</th>
                                                            <th>Team<br> Member</th>
                                                            <th>Due Date/Time</th>
                                                            <th>Tender Value</th>
                                                            <th>Items</th>
                                                            <th>Timer</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if ($pending && $pending->count() > 0)
                                                            @foreach ($pending as $info)
                                                                <tr>
                                                                    <td>{{ $info->tender->tender_no }}</td>
                                                                    <td>{{ $info->tender->tender_name }}</td>
                                                                    <td>{{ $info->tender->users->name }}</td>
                                                                    <td>
                                                                        {{ date('d-m-Y', strtotime($info->tender->due_date)) }}
                                                                        <br>
                                                                        {{ date('H:i', strtotime($info->tender->due_time)) }}
                                                                    </td>
                                                                    <td>{{ $info->tender->gst_values }}</td>
                                                                    <td>{{ $info->tender->itemName ? $info->tender->itemName->name : '' }}
                                                                    </td>
                                                                    <td>
                                                                        @php
                                                                            $timer = $info->tender->getTimer(
                                                                                'tender_approval',
                                                                            );
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end =
                                                                                    strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $info->tender->remainedTime(
                                                                                    'tender_approval',
                                                                                );
                                                                            }
                                                                        @endphp
                                                                        @if ($timer)
                                                                            <span class="timer"
                                                                                id="timer-{{ $info->tender->id }}"
                                                                                data-remaining="{{ $remaining }}"></span>
                                                                        @else
                                                                            {!! $remained !!}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <a href="{{ route('tender.show', $info->id) }}"
                                                                            class="btn btn-primary btn-xs">
                                                                            <i class="fa fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ route('tlApprovalForm', $info->id) }}"
                                                                            class="btn btn-{{ $info->tender->tlStatus == 1 ? 'success' : ($info->tender->tlStatus == 2 ? 'danger' : ($info->tender->tlStatus == 3 ? 'warning' : 'primary')) }} btn-xs">
                                                                            {{ $info->tender->tlStatus == 1 ? 'Approved' : ($info->tender->tlStatus == 2 ? 'Rejected' : ($info->tender->tlStatus == 3 ? 'Incomplete Sheet' : 'Pending')) }}
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                            aria-labelledby="nav-profile-tab">
                                            <div class="table-responsive">
                                                <table class="table" id="allUsers">
                                                    <thead class="">
                                                        <tr>
                                                            <th>Tender No</th>
                                                            <th>Tender<br> Name</th>
                                                            <th>Team<br> Member</th>
                                                            <th>Due Date/Time</th>
                                                            <th>Tender Value</th>
                                                            <th>Items</th>
                                                            <th>Timer</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if ($approved && $approved->count() > 0)
                                                            @foreach ($approved as $info)
                                                                <tr>
                                                                    <td>{{ $info->tender->tender_no }}</td>
                                                                    <td>{{ $info->tender->tender_name }}</td>
                                                                    <td>{{ $info->tender->users->name }}</td>
                                                                    <td>
                                                                        {{ date('d-m-Y', strtotime($info->tender->due_date)) }}
                                                                        <br>
                                                                        {{ date('H:i', strtotime($info->tender->due_time)) }}
                                                                    </td>
                                                                    <td>{{ $info->tender->gst_values }}</td>
                                                                    <td>{{ $info->tender->itemName ? $info->tender->itemName->name : '' }}
                                                                    </td>
                                                                    <td>
                                                                        @php
                                                                            $timer = $info->tender->getTimer(
                                                                                'tender_approval',
                                                                            );
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end =
                                                                                    strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $info->tender->remainedTime(
                                                                                    'tender_approval',
                                                                                );
                                                                            }
                                                                        @endphp
                                                                        @if ($timer)
                                                                            <span class="timer"
                                                                                id="timer-{{ $info->tender->id }}"
                                                                                data-remaining="{{ $remaining }}"></span>
                                                                        @else
                                                                            {!! $remained !!}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <a href="{{ route('tender.show', $info->id) }}"
                                                                            class="btn btn-primary btn-xs">
                                                                            <i class="fa fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ route('tlApprovalForm', $info->id) }}"
                                                                            class="btn btn-{{ $info->tender->tlStatus == 1 ? 'success' : ($info->tender->tlStatus == 2 ? 'danger' : ($info->tender->tlStatus == 3 ? 'warning' : 'primary')) }} btn-xs">
                                                                            {{ $info->tender->tlStatus == 1 ? 'Approved' : ($info->tender->tlStatus == 2 ? 'Rejected' : ($info->tender->tlStatus == 3 ? 'Incomplete Sheet' : 'Pending')) }}
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="nav-contact" role="tabpanel"
                                            aria-labelledby="nav-contact-tab">
                                            <div class="table-responsive">
                                                <table class="table" id="allUsers">
                                                    <thead class="">
                                                        <tr>
                                                            <th>Tender No</th>
                                                            <th>Tender<br> Name</th>
                                                            <th>Team<br> Member</th>
                                                            <th>Due Date/Time</th>
                                                            <th>Tender Value</th>
                                                            <th>Items</th>
                                                            <th>Timer</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if ($rejected && $rejected->count() > 0)
                                                            @foreach ($rejected as $info)
                                                                <tr>
                                                                    <td>{{ $info->tender->tender_no }}</td>
                                                                    <td>{{ $info->tender->tender_name }}</td>
                                                                    <td>{{ $info->tender->users->name }}</td>
                                                                    <td>
                                                                        {{ date('d-m-Y', strtotime($info->tender->due_date)) }}
                                                                        <br>
                                                                        {{ date('H:i', strtotime($info->tender->due_time)) }}
                                                                    </td>
                                                                    <td>{{ $info->tender->gst_values }}</td>
                                                                    <td>{{ $info->tender->itemName ? $info->tender->itemName->name : '' }}
                                                                    </td>
                                                                    <td>
                                                                        @php
                                                                            $timer = $info->tender->getTimer(
                                                                                'tender_approval',
                                                                            );
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end =
                                                                                    strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $info->tender->remainedTime(
                                                                                    'tender_approval',
                                                                                );
                                                                            }
                                                                        @endphp
                                                                        @if ($timer)
                                                                            <span class="timer"
                                                                                id="timer-{{ $info->tender->id }}"
                                                                                data-remaining="{{ $remaining }}"></span>
                                                                        @else
                                                                            {!! $remained !!}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <a href="{{ route('tender.show', $info->id) }}"
                                                                            class="btn btn-primary btn-xs">
                                                                            <i class="fa fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ route('tlApprovalForm', $info->id) }}"
                                                                            class="btn btn-{{ $info->tender->tlStatus == 1 ? 'success' : ($info->tender->tlStatus == 2 ? 'danger' : ($info->tender->tlStatus == 3 ? 'warning' : 'primary')) }} btn-xs">
                                                                            {{ $info->tender->tlStatus == 1 ? 'Approved' : ($info->tender->tlStatus == 2 ? 'Rejected' : ($info->tender->tlStatus == 3 ? 'Incomplete Sheet' : 'Pending')) }}
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
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
