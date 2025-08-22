<?php
setlocale(LC_MONETARY, 'en_IN');

if (!function_exists('format_inr')) {
    function format_inr($amount)
    {
        $locale = 'en_IN';
        $fmt = new NumberFormatter($locale, NumberFormatter::DECIMAL);

        // Keep up to 2 digits after the decimal point
        $fmt->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2);

        return $fmt->format((float)$amount);
    }
}



if (!function_exists('inr_to_words')) {
    function inr_to_words($number)
    {
        $number = (float) $number;
        $no = floor($number);
        $point = round($number - $no, 2) * 100;

        $hundred = null;
        $digits_1 = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 
                     'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 
                     'Eighteen', 'Nineteen'];
        $digits_2 = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
        $str = [];

        $i = 0;
        while ($no > 0) {
            $divider = ($i == 2) ? 10 : 100;
            $number = $no % $divider;
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;

            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                if ($number < 21) {
                    $str[] = $digits_1[$number] . ' ' . ($digits[$counter] ?? '');
                } else {
                    $str[] = $digits_2[floor($number / 10)] . ' ' . $digits_1[$number % 10] . ' ' . ($digits[$counter] ?? '');
                }
            } else {
                $str[] = null;
            }
        }

        $str = array_reverse(array_filter($str));
        $rupees = implode(' ', $str);

        $paise = '';
        if ($point) {
            $paise .= ($point < 21) 
                ? $digits_1[$point] 
                : $digits_2[floor($point / 10)] . ' ' . $digits_1[$point % 10];
            $paise .= ' Paise';
        }

        return ucfirst(trim($rupees)) . ' Rupees' . ($paise ? ' and ' . $paise : '') . ' Only';
    }
}
