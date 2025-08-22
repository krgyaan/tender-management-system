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
    <span class="timer" id="timer-{{ $row->id }}" data-remaining="{{ $remaining }}"></span>
@else
    <span class="d-none">0</span>
    {!! $remained !!}
@endif
