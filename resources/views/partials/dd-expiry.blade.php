@php
    $duedate = $dd->duedate ?? '';
    if ($duedate) {
        $currentDate = now();
        $duedate = \Carbon\Carbon::parse($duedate);
        $threeMonthsLater = $duedate->copy()->addMonths(3);

        if ($currentDate->lte($threeMonthsLater)) {
            echo 'Valid';
        } else {
            echo 'Expired';
        }
    } else {
        echo 'No date';
    }
@endphp
