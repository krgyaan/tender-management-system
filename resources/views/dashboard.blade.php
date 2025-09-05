@extends('layouts.app');
@section('page-title', ($data['role'] ?? 'Admin') . ' Dashboard | Team ' . ($user->team ?? ''))
@section('content')
    <div class="row">
        @if (!$data['google_oauth_connected'])
            <div class="my-3">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Google OAuth Not Connected!</strong> Please connect your Google account.
                    <a href="{{ route('google.connect') }}" class="btn btn-sm btn-outline-success ms-3">Connect Now</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        <div class="col-lg-3 {{ $data['role'] != 'Admin' ? 'd-none' : '' }}">
            <div class="card shining-card">
                <div class="card-body">
                    @if ($data['role'] == 'Admin')
                        <a href="{{ route('admin/user/all') }}" class="stretched-link fw-bold fs-5 me-2">
                            Total Employees
                        </a>
                        <div class="pt-3">
                            <h4 class="counter text-success" style="visibility: visible;">{{ $data['userCount'] }}</h4>
                        </div>
                    @else
                        <div class="d-flex flex-column">
                            <span class="text-muted">Today</span>
                            <a href="javascript:void(0);" class="fw-bold fs-5">{{ date('l, F jS') }}</a>
                            <h4 class="fs-5" id="currentTime"></h4>
                            <script>
                                function updateCurrentTime() {
                                    const now = new Date();
                                    let hours = now.getHours();
                                    const minutes = now.getMinutes().toString().padStart(2, '0');
                                    const seconds = now.getSeconds().toString().padStart(2, '0');
                                    const ampm = hours >= 12 ? 'PM' : 'AM';
                                    hours = hours % 12;
                                    hours = hours ? hours : 12; // the hour '0' should be '12'
                                    const timeString = `${hours.toString().padStart(2, '0')}:${minutes}:${seconds} ${ampm}`;
                                    document.getElementById('currentTime').textContent = timeString;
                                }
                                updateCurrentTime();
                                setInterval(updateCurrentTime, 1000);
                            </script>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shining-card">
                <div class="card-body">
                    <a href="{{ route('tender.index') }}" class="stretched-link fw-bold fs-5 me-2">
                        Total Tenders
                    </a>
                    <div class="progress-detail pt-3">
                        <h4 class="counter text-success" style="visibility: visible;">{{ $data['tenderInfoCount'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shining-card">
                <div class="card-body">
                    <a href="" class="stretched-link fw-bold fs-5 me-2">Total Bids</a>
                    <div class="progress-detail pt-3">
                        <h4 class="counter text-success" style="visibility: visible;">{{ $data['bided'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="mb-3" id="team-color-legend">
                    @php
                        $teamColors = [];
                        $index = 0;

                        foreach ($data['tender_info'] ?? [] as $tender) {
                            $member = $tender->users->name ?? 'Unknown';
                            if (!isset($teamColors[$member])) {
                                $hue = ($index * 137) % 360;
                                $teamColors[$member] = "hsl($hue, 70%, 50%)";
                                $index++;
                            }
                        }
                    @endphp

                    @if (($data['role'] ?? '') == 'Admin' && isset($activeUsers))
                        <div class="col-12 mb-3">
                            <form id="userFilterForm" class="d-flex align-items-center">
                                <select id="userFilter" class="form-select w-auto">
                                    <option value="all">All Users</option>
                                    @foreach ($activeUsers as $activeUser)
                                        <option value="{{ $activeUser->id }}">{{ $activeUser->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @endif
                </div>
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            // Prepare all events with userId
            var allEvents = [
                @foreach ($data['tender_info'] ?? [] as $data)
                    {
                        title: 'Tender due - {{ $data->tender_name }}',
                        start: '{{ $data->due_date }}',
                        color: '{{ $teamColors[$data->users->name ?? 'Unknown'] ?? '#888' }}',
                        textColor: '#ffffff',
                        userId: '{{ $data->team_member ?? '' }}',
                    },
                    @php
                        $tq = $data->tq_received->first();
                    @endphp
                    @if ($tq?->tq_submission_date)
                        {
                            title: 'TQ Date - {{ $data->tender_name }}',
                            start: '{{ $tq->tq_submission_date ?? '' }}',
                            color: '#ff9800', // Orange for TQ
                            textColor: '#fff',
                            userId: '{{ $data->team_member ?? '' }}',
                        },
                    @endif
                @endforeach
                @foreach ($data['follow_ups'] ?? [] as $row)
                    {
                        title: 'Followup - {{ $row->party_name }}',
                        start: '{{ date('Y-m-d', strtotime($row->created_at)) }}',
                        color: '#0000ff',
                        textColor: '#ffffff',
                        userId: '{{ $row->assigned_to ?? '' }}',
                    },
                @endforeach
            ];

            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth,listWeek'
                },
                initialView: 'dayGridMonth',
                initialDate: '{{ date('Y-m-d') }}',
                navLinks: true,
                selectable: true,
                nowIndicator: true,
                dayMaxEvents: true,
                editable: true,
                selectable: true,
                businessHours: true,
                dayMaxEvents: true,
                events: allEvents
            });
            calendar.render();

            // Filtering logic for admin
            var userFilter = document.getElementById('userFilter');
            if (userFilter) {
                userFilter.addEventListener('change', function() {
                    var selectedUser = this.value;
                    var filteredEvents = [];
                    if (selectedUser === 'all') {
                        filteredEvents = allEvents;
                    } else {
                        filteredEvents = allEvents.filter(function(event) {
                            return event.userId == selectedUser;
                        });
                    }
                    calendar.removeAllEvents();
                    filteredEvents.forEach(function(event) {
                        calendar.addEvent(event);
                    });
                });
            }
        });
    </script>
@endsection
