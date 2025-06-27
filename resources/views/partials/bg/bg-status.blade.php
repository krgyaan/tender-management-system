@if ($bg->action)
    @switch($bg->action)
        @case(1)
            <span class="{{ $bg->bg_req == 'Accepted' ? 'text-success' : 'text-danger' }}">
                {{ $bg->bg_req == 'Accepted' ? 'Format Accepted' : 'Rejected' }}
            </span>
        @break

        @case(2)
            <span class="text-info">Created</span>
        @break

        @case(3)
            <span class="text-info">SFMS Submitted</span>
        @break

        @case(4)
            <span class="text-info">Followup Initiated</span>
        @break

        @case(5)
            <span class="text-info">Extension Request</span>
        @break

        @case(6)
            <span class="text-info">Returned via courier</span>
        @break

        @case(7)
            <span class="text-info">Cancellation Request</span>
        @break

        @case(8)
            <span class="text-info">BG Cancelled</span>
        @break

        @case(9)
            <span class="text-info">FDR released</span>
        @break

        @default
            <span class="text-info"></span>
    @endswitch
@else
    {{ $bg->emds->type }}
@endif
