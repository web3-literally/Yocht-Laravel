<?php

namespace App\Helpers;

use App\Currency;
use Cache;

/**
 * Class Plan
 * @package App\Helpers
 */
class Plan
{
    /**
     * @param string $code
     * @return string
     */
    public static function getCurrencyLabel($code)
    {
        $currency = Cache::remember('Currency_' . $code, 1440, function () use ($code) {
            return Currency::where('code', $code)->limit(1)->first();
        });
        return ($currency ?? $code) . '';
    }

    /**
     * @return array
     */
    public static function getFrequencyLabel($frequency)
    {
        if ($frequency == 1) {
            return "Month";
        } elseif ($frequency == 12) {
            return "Year";
        } elseif (!($frequency % 12)) {
            $years = round($frequency / 12);
            return "{$years} Years";
        }

        return "{$frequency} Months";
    }
}