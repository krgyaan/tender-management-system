@php
    if ($tender) {
        $timer = $tender->getTimer('costing_sheet');
        if ($timer) {
            $start = $timer->start_time;
            $hrs = $timer->duration_hours;
            $end = strtotime($start) + $hrs * 60 * 60;
            $remaining = $end - time();
        } else {
            $remained = $tender->remainedTime('costing_sheet');
        }
    }
@endphp
@if (isset($tender) && $timer)
    <span class="d-none">{{ $remaining }}</span>
    <span class="timer" id="timer-{{ $tender->id }}" data-remaining="{{ $remaining }}"></span>
@elseif (isset($tender) && isset($remained))
    <span class="d-none">0</span>
    {!! $remained !!}
@endif
