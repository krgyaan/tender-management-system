@if ($bg->bg_expiry && $bg->bg_claim)
    @if (now()->lte($bg->bg_expiry))
        Valid
    @elseif (now()->lte($bg->bg_claim))
        Claim Period
    @else
        Expired
    @endif
@else
    N/A
@endif
