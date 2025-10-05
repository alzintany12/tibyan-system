<?php

use App\Helpers\CurrencyHelper;

if (!function_exists('currency')) {
    /**
     * Format amount with currency
     *
     * @param float $amount
     * @param bool $showSymbol
     * @return string
     */
    function currency($amount, $showSymbol = true)
    {
        return CurrencyHelper::format($amount, $showSymbol);
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get currency symbol
     *
     * @return string
     */
    function currency_symbol()
    {
        return CurrencyHelper::symbol();
    }
}

if (!function_exists('currency_name')) {
    /**
     * Get currency name
     *
     * @return string
     */
    function currency_name()
    {
        return CurrencyHelper::name();
    }
}

if (!function_exists('currency_to_words')) {
    /**
     * Convert amount to words
     *
     * @param float $amount
     * @return string
     */
    function currency_to_words($amount)
    {
        return CurrencyHelper::toWords($amount);
    }
}