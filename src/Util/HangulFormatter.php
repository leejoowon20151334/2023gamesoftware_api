<?php

namespace src\Util;

class HangulFormatter
{
    public static function formatPrice($price): string
    {
        if (empty($price)) {
            return '';
        }

        return self::numberToWon($price) . '원';
    }

    public static function numberToWon($num): string
    {
        if (!ctype_digit($num)) {
            $num = (string) $num;
        }

        $num = self::padNumForLengthToBeMultipleOfFour($num);
        $won_digits = self::getWonDigits($num);

        return implode(' ', $won_digits);
    }

    public static function formatDate(string $yyyy_mm_dd): string
    {
        if (empty($yyyy_mm_dd)) {
            return '';
        }

        return date('Y년 n월 j일', strtotime($yyyy_mm_dd));
    }

    private static function padNumForLengthToBeMultipleOfFour($num): string
    {
        $len = strlen($num);
        $mod = $len % 4;

        if ($mod) {
            $mod = 4 - $mod;
            $num = str_pad($num, $len + $mod, '0', STR_PAD_LEFT);
        }

        return $num;
    }

    private static function getWonDigits(string $num): array
    {
        $won_units = ['', '만', '억', '조', '경', '해'];

        $digits = str_split($num, 4);
        $digits_count = count($digits);

        $won_digits = [];

        foreach ($digits as $i => $digit) {
            $int_digit = (int) $digit;

            if (empty($int_digit)) {
                continue;
            }

            $won_digits[] = number_format($int_digit) . $won_units[$digits_count - $i - 1];
        }

        return $won_digits;
    }
}
