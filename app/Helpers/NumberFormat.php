<?php
namespace App\Helpers;

class NumberFormat
{
    public static function formatPrice(string $text)
    {
        $text = str_replace(' ', '', $text);
        $text = str_replace(',', '.', $text);
        $text = preg_replace('/[^\-0-9.]/', '', $text);
        $text = number_format((float)$text, 2, '.', '');
        return $text;
    }
}