@php
    if ($dd) {
        $timer = $dd->getTimer('dd_ac_form');
        if ($timer) {
            $start = $timer->start_time;
            $hrs = $timer->duration_hours;
            $end = strtotime($start) + $hrs * 60 * 60;
            $remaining = $end - time();
        } else {
            $remained = $dd->remainedTime('dd_ac_form');
        }
    }
@endphp
@if (isset($dd) && $dd && isset($timer) && $timer)
    <span class="timer" id="timer-{{ $dd->id }}" data-remaining="{{ $remaining }}"></span>
@elseif (isset($dd) && $dd && isset($remained))
    {!! $remained !!}
@endif
