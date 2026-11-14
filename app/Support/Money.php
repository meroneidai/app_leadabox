<?php

namespace App\Support;

/**
 * Decimal-string arithmetic for money. Floating point is never used for currency.
 */
final class Money
{
    public const SCALE = 2;

    private const WORKING_SCALE = 6;

    public static function add(string $a, string $b): string
    {
        return self::round(bcadd($a, $b, self::WORKING_SCALE));
    }

    public static function sub(string $a, string $b): string
    {
        return self::round(bcsub($a, $b, self::WORKING_SCALE));
    }

    /** Multiplication keeps extra precision until the final rounding step. */
    public static function mul(string $a, string $b): string
    {
        return self::round(bcmul($a, $b, self::WORKING_SCALE));
    }

    public static function percentOf(string $amount, string $percent): string
    {
        return self::round(bcdiv(bcmul($amount, $percent, self::WORKING_SCALE), '100', self::WORKING_SCALE));
    }

    public static function zero(): string
    {
        return self::round('0');
    }

    public static function isZero(string $amount): bool
    {
        return bccomp($amount, '0', self::SCALE) === 0;
    }

    /** Half-up rounding, matching how invoices are rounded in practice. */
    public static function round(string $amount): string
    {
        $negative = str_starts_with($amount, '-');
        $value = $negative ? substr($amount, 1) : $amount;

        $rounded = bcadd($value, '0.'.str_repeat('0', self::SCALE).'5', self::SCALE + 1);
        $rounded = bcadd($rounded, '0', self::SCALE);

        return $negative && bccomp($rounded, '0', self::SCALE) !== 0 ? '-'.$rounded : $rounded;
    }

    public static function format(string $amount, ?string $currency = null): string
    {
        $formatted = number_format((float) $amount, self::SCALE, ',', "\u{00A0}");

        return $currency ? $formatted.' '.$currency : $formatted;
    }
}
