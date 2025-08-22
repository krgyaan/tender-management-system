@php
    $timer = $tdr->getTimer('costing_sheet_approval');
    if ($timer) {
        $start = $timer->start_time;
        $hrs = $timer->duration_hours;
        $end = strtotime($start) + $hrs * 60 * 60;
        $remaining = $end - time();
    } else {
        $remained = $tdr->remainedTime('costing_sheet_approval');
    }
@endphp
@if ($timer)
    {{-- Sortable timer --}}
    <span class="d-none">{{ $remaining }}</span>
    <span class="timer" id="timer-{{ $tdr->id }}" data-remaining="{{ $remaining }}"></span>
@else
    <span class="d-none">0</span>
    {!! $remained !!}
@endif
