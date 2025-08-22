@php
    if ($pop) {
        $timer = $pop->getTimer('pop_acc_form');
        if ($timer) {
            $start = $timer->start_time;
            $hrs = $timer->duration_hours;
            $end = strtotime($start) + $hrs * 60 * 60;
            $remaining = $end - time();
        } else {
            $remained = $pop->remainedTime('pop_acc_form');
        }
    }
@endphp
@if (isset($pop) && $timer)
    <span class="timer" id="timer-{{ $pop->id }}" data-remaining="{{ $remaining }}"></span>
@elseif (isset($pop) && isset($remained))
    {!! $remained !!}
@endif
