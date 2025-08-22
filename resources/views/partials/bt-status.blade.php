@if ($bt->action != null)
    @switch($bt->action)
        @case(1)
            @if ($bt->status == 'Accepted')
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
