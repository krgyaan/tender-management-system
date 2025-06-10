@extends('layouts.app')
@section('page-title', 'Admin Dashboard')

@section('content')

    <div class="row">
        <div class="col-lg-3">
            <div class="card shining-card">
                <div class="card-body">
                    <a href="{{ route('admin/user/all') }}" class="stretched-link fw-bold text-white fs-5 me-2">Total
                        Employees</a>
                    <div class="pt-3">
                        <h4 class="counter text-success" style="visibility: visible;">{{ $userCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shining-card">
                <div class="card-body">
                    <a href="{{ route('tender.index') }}" class="stretched-link fw-bold text-white fs-5 me-2">Total
                        Tenders</a>
                    <div class="progress-detail pt-3">
                        <h4 class="counter text-success" style="visibility: visible;">{{ $tenderInfoCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shining-card">
                <div class="card-body">
                    <a href="" class="stretched-link fw-bold text-white fs-5 me-2">Total Bids</a>
                    <div class="progress-detail pt-3">
                        <h4 class="counter text-success" style="visibility: visible;">5</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
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
                    @foreach ($tender_info as $data)
                        {
                            title: 'Tender due - {{ $data->tender_name }}',
                            start: '{{ $data->due_date }}',
                            color: '#ff0000',
                            textColor: '#ffffff'
                        },
                    @endforeach
                    @foreach ($follow_ups as $row)
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
