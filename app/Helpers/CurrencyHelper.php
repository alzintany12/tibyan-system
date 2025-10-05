<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format amount with currency
     *
     * @param float $amount
     * @param bool $showSymbol
     * @return string
     */
    public static function format($amount, $showSymbol = true)
    {
        $symbol = config('currency.symbol', 'د.ل');
        $position = config('currency.position', 'after');
        $decimalPlaces = config('currency.decimal_places', 2);
        $thousandsSeparator = config('currency.thousands_separator', ',');
        $decimalSeparator = config('currency.decimal_separator', '.');

        $formattedAmount = number_format($amount, $decimalPlaces, $decimalSeparator, $thousandsSeparator);

        if (!$showSymbol) {
            return $formattedAmount;
        }

        if ($position === 'before') {
            return $symbol . ' ' . $formattedAmount;
        } else {
            return $formattedAmount . ' ' . $symbol;
        }
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public static function symbol()
    {
        return config('currency.symbol', 'د.ل');
    }

    /**
     * Get currency name
     *
     * @return string
     */
    public static function name()
    {
        return config('currency.name', 'دينار ليبي');
    }

    /**
     * Convert amount to words (Arabic)
     *
     * @param float $amount
     * @return string
     */
    public static function toWords($amount)
    {
        // تحويل المبلغ إلى كلمات باللغة العربية
        $ones = [
            '', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة',
            'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر',
            'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'
        ];

        $tens = [
            '', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'
        ];

        $hundreds = [
            '', 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'
        ];

        if ($amount == 0) {
            return 'صفر ' . self::name();
        }

        $integer = (int) $amount;
        $decimal = round(($amount - $integer) * 100);

        $result = self::convertNumberToWords($integer, $ones, $tens, $hundreds);

        if ($decimal > 0) {
            $result .= ' و ' . self::convertNumberToWords($decimal, $ones, $tens, $hundreds) . ' قرش';
        }

        return $result . ' ' . self::name();
    }

    /**
     * Convert number to words helper
     *
     * @param int $number
     * @param array $ones
     * @param array $tens
     * @param array $hundreds
     * @return string
     */
    private static function convertNumberToWords($number, $ones, $tens, $hundreds)
    {
        if ($number < 20) {
            return $ones[$number];
        } elseif ($number < 100) {
            return $tens[intval($number / 10)] . ($number % 10 != 0 ? ' ' . $ones[$number % 10] : '');
        } elseif ($number < 1000) {
            return $hundreds[intval($number / 100)] . ($number % 100 != 0 ? ' ' . self::convertNumberToWords($number % 100, $ones, $tens, $hundreds) : '');
        } elseif ($number < 1000000) {
            $thousands = intval($number / 1000);
            $remainder = $number % 1000;
            $result = self::convertNumberToWords($thousands, $ones, $tens, $hundreds) . ' ألف';
            if ($remainder != 0) {
                $result .= ' ' . self::convertNumberToWords($remainder, $ones, $tens, $hundreds);
            }
            return $result;
        } else {
            return 'العدد كبير جداً';
        }
    }
}