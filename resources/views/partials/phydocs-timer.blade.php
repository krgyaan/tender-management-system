@php
    $timer = $info->getTimer('physical_docs');
    if ($timer) {
        $start = $timer->start_time;
        $hrs = $timer->duration_hours;
        $end = strtotime($start) + $hrs * 60 * 60;
        $remaining = $end - time();
    } else {
        $remained = $info->remainedTime('physical_docs');
    }
@endphp
@if ($timer)
    <span class="d-none">{{ $remaining }}</span>
    <span class="timer" id="timer-{{ $info->id }}" data-remaining="{{ $remaining }}"></span>
@else
    <span class="d-none">0</span>
    {!! $remained !!}
@endif
