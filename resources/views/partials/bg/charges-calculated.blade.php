@php
    $bgValue = $bg->bg_amt ?? 0;
    $bgStampPaperValue = 300;
    $sfmsCharges = 590;
    $bgCreationDate = Carbon\Carbon::parse($bg->bg_date);
    $bgClaimDate = Carbon\Carbon::parse($bg->bg_claim);

    $dailyInterestRate = 0.01 / 365;
    $monthsDifference = $bgCreationDate->diffInMonths($bgClaimDate);
    $interestComponent = $bgValue * $dailyInterestRate * $monthsDifference;
    $interestWithGST = $interestComponent * 1.18;
@endphp
{{ format_inr($interestWithGST + $sfmsCharges + $bgStampPaperValue) }}
