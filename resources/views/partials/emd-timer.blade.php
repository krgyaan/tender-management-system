@php
    $timer = $tender->getTimer('emd_request');
    if ($timer) {
        $start = $timer->start_time;
        $hrs = $timer->duration_hours;
        $end = strtotime($start) + $hrs * 60 * 60;
        $remaining = $end - time();
    } else {
        $remained = $tender->remainedTime('emd_request');
    }
@endphp
@if ($timer)
    <span class="timer" id="timer-{{ $tender->id }}" data-remaining="{{ $remaining }}"></span>
@else
    {!! $remained !!}
@endif
