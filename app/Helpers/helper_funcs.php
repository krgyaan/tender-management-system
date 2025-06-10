<?php
setlocale(LC_MONETARY, 'en_IN');

if (!function_exists('format_inr')) {
    function format_inr($amount)
    {
        $amount = explode('.', $amount)[0];
        $amount = (int)$amount;
        $len = strlen($amount);

        $locale = 'en_IN';
        $fmt = new NumberFormatter(
            $locale,
            NumberFormatter::DECIMAL,
        );

        $fmt->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
        $formattedAmount = $fmt->format($amount);

        if ($len <= 3) {
            return $amount;
        }

        return $formattedAmount;
    }
}
