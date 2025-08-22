@php
    $timer = $tender->getTimer('document_checklist');
    if ($timer) {
        $start = $timer->start_time;
        $hrs = $timer->duration_hours;
        $end = strtotime($start) + $hrs * 60 * 60;
        $remaining = $end - time();
    } else {
        $remained = $tender->remainedTime(
            'document_checklist',
        );
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
