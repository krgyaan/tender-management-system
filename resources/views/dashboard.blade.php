@extends('layouts.app')
@section('page-title', ($data['role'] ?? 'Admin') . ' Dashboard | Team ' . ($user->team ?? ''))

@section('content')
    <div class="row">
        <div class="col-lg-3 {{ $data['role'] == 'admin' ? 'd-none' : '' }}">
            <div class="card shining-card">
                <div class="card-body">
                    @if ($data['role'] == 'admin')
                        <a href="{{ route('admin/user/all') }}" class="stretched-link fw-bold fs-5 me-2">
                            Total Employees
                        </a>
                        <div class="pt-3">
                            <h4 class="counter text-success" style="visibility: visible;">{{ $data['userCount'] }}</h4>
                        </div>
                    @else
                        <div class="d-flex">
                            <span class="text-muted">Today</span>
                            <a href="javascript:void(0);" class="fw-bold fs-5">{{ date('l, F jS') }}</a>
                            <h4 class="fs-5" id="currentTime">{{ date('h:i:s A') }}</h4>
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

                    <ul class="list-unstyled d-flex flex-wrap">
                        @foreach ($teamColors as $member => $color)
                            @php
                                $user = \App\Models\User::where('name', $member)->first();
                            @endphp
                            <li class="me-4 mb-2 d-flex align-items-center">
                                <span class="rounded-circle me-2"
                                    style="display:inline-block;width:16px;height:16px;background-color:{{ $color }}"
                                    data-bs-toggle="tooltip" data-bs-html="true"
                                    title="{{ $user->email }}<br>{{ ucwords(str_replace(['-', '_'], ' ', $user->role)) }}">
                                </span>
                                {{ $member }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
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
                events: [
                    @foreach ($data['tender_info'] ?? [] as $data)
                        {
                            title: 'Tender due - {{ $data->tender_name }}',
                            start: '{{ $data->due_date }}',
                            color: '{{ $teamColors[$data->users->name ?? 'Unknown'] }}',
                            textColor: '#ffffff'
                        },
                    @endforeach
                    @foreach ($data['follow_ups'] ?? [] as $row)
                        {
                            title: 'Followup - {{ $row->party_name }}',
                            start: '{{ date('Y-m-d', strtotime($row->created_at)) }}',
                            color: '#0000ff',
                            textColor: '#ffffff'
                        },
                    @endforeach
                ]
            });
            calendar.render();
        });
    </script>
@endsection
