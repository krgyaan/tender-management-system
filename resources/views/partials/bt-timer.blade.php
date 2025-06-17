@php
    if ($bt) {
        $timer = $bt->getTimer('bt_acc_form');
        if ($timer) {
            $start = $timer->start_time;
            $hrs = $timer->duration_hours;
            $end = strtotime($start) + $hrs * 60 * 60;
            $remaining = $end - time();
        } else {
            $remained = $bt->remainedTime('bt_acc_form');
        }
    }
@endphp
@if (isset($bt) && $timer)
    <span class="timer" id="timer-{{ $bt->id }}" data-remaining="{{ $remaining }}"></span>
@elseif (isset($bt) && isset($remained))
    {!! $remained !!}
@endif
