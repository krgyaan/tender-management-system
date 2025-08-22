@php
    $timer = $bg ? $bg->getTimer('bg_acc_form') : '';
    if ($timer) {
        $start = $timer->start_time;
        $hrs = $timer->duration_hours;
        $end = strtotime($start) + $hrs * 60 * 60;
        $remaining = $end - time();
    } else {
        $remained = $bg ? $bg->remainedTime('bg_acc_form') : '';
    }
@endphp
@if ($timer)
    <span class="timer" id="timer-{{ $bg->id }}" data-remaining="{{ $remaining }}"></span>
@else
    {!! $remained !!}
@endif
