<?php

namespace App\Enums;

use Carbon\CarbonInterface;

enum BillingInterval: string
{
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
    case SemiAnnual = 'semi_annual';
    case Yearly = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::Monthly => __('services.interval_monthly'),
            self::Quarterly => __('services.interval_quarterly'),
            self::SemiAnnual => __('services.interval_semi_annual'),
            self::Yearly => __('services.interval_yearly'),
        };
    }

    public function months(): int
    {
        return match ($this) {
            self::Monthly => 1,
            self::Quarterly => 3,
            self::SemiAnnual => 6,
            self::Yearly => 12,
        };
    }

    /** Next renewal date for a period that started on the given date. */
    public function nextDateFrom(CarbonInterface $date): CarbonInterface
    {
        return $date->copy()->addMonthsNoOverflow($this->months());
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
