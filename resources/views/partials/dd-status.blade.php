@if ($dd->action != null)
    @switch($dd->action)
        @case(1)
            @if ($dd->status == 'Accepted')
                {{ 'DD Created' }}
            @else
                {{ 'DD Rejected' }}
            @endif
        @break

        @case(2)
            {{ 'Followup Initiated' }}
        @break

        @case(3)
            {{ 'Returned via courier' }}
        @break

        @case(4)
            {{ 'Returned via Bank Transfer' }}
        @break

        @case(5)
            {{ 'Settled with Project Account' }}
        @break

        @case(6)
            {{ 'DD Cancellation request sent to branch' }}
        @break

        @case(7)
            {{ 'DD Cancelled at Branch' }}
        @break

        @default
            {{ 'NA' }}
    @endswitch
@endif
