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
    <span class="timer" id="timer-{{ $courier->id }}" data-remaining="{{ $remaining }}"></span>
@else
    {!! $remained !!}
@endif
