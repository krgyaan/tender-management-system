@php
    $timer = $tender->getTimer('rfq_received');
    if ($timer) {
        $start = $timer->start_time;
        $hrs = $timer->duration_hours;
        $end = strtotime($start) + $hrs * 3600;
        $remaining = $end - time(); // in seconds
    } else {
        $remained = $tender->remainedTime('rfq_received');
    }
@endphp

@if ($timer)
    {{-- Sortable timer --}}
    <span class="d-none">{{ $remaining }}</span>
    <span class="timer" id="timer-{{ $tender->id }}" data-remaining="{{ $remaining }}"></span>
@else
    <span class="d-none">0</span>
    {!! $remained !!}
@endif
