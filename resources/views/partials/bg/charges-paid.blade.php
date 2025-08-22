@php
    $bgc = $bg->bg_charge_deducted ?? 0;
    $sfms = $bg->sfms_charge_deducted ?? 0;
    $stamp = $bg->stamp_charge_deducted ?? 0;
    $other = $bg->other_charge_deducted ?? 0;
    $total = $bgc + $sfms + $stamp + $other;
@endphp
{{ format_inr($total) }}
