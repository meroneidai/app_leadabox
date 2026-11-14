<?php

namespace App\Support;

/**
 * Swedish OCR payment reference: the digits plus a Luhn check digit.
 */
final class OcrReference
{
    public static function fromNumber(string $invoiceNumber): string
    {
        $digits = preg_replace('/\D/', '', $invoiceNumber) ?: '0';

        return $digits.self::luhnCheckDigit($digits);
    }

    public static function isValid(string $reference): bool
    {
        $digits = preg_replace('/\D/', '', $reference) ?: '';

        if (strlen($digits) < 2) {
            return false;
        }

        $body = substr($digits, 0, -1);
        $check = substr($digits, -1);

        return self::luhnCheckDigit($body) === $check;
    }

    private static function luhnCheckDigit(string $digits): string
    {
        $sum = 0;
        $double = true;

        foreach (array_reverse(str_split($digits)) as $digit) {
            $value = (int) $digit;

            if ($double) {
                $value *= 2;
                if ($value > 9) {
                    $value -= 9;
                }
            }

            $sum += $value;
            $double = ! $double;
        }

        return (string) ((10 - ($sum % 10)) % 10);
    }
}
