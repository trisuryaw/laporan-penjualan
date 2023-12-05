<?php

if (! function_exists('rupiahFormat')) {
    /**
     * Convert to Rupiah currency format
     * @param $numbers
     * @return string
     */
    function rupiahFormat($numbers): string
    {
        return number_format($numbers, 0, '', ',');
    }
}