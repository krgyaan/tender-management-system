@if ($pop->action != null)
    @switch($pop->action)
        @case(1)
            @if ($pop->status == 'Accepted')
                {{ 'Accepted' }}
            @else
                {{ 'Rejected' }}
            @endif
        @break

        @case(2)
            {{ 'Followup Initiated' }}
        @break

        @case(3)
            {{ 'Returned via Bank Transfer' }}
        @break

        @case(4)
            {{ 'Settled with Project Account' }}
        @break
        
        @default
            {{ 'Pending' }}
    @endswitch
@endif
