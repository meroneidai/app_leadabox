<?php

namespace App\Enums;

/**
 * Decides whether selling a service creates a one-off job or an ongoing subscription.
 */
enum BillingType: string
{
    case OneTime = 'one_time';
    case Recurring = 'recurring';

    public function label(): string
    {
        return match ($this) {
            self::OneTime => __('services.billing_one_time'),
            self::Recurring => __('services.billing_recurring'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::OneTime => __('services.billing_one_time_hint'),
            self::Recurring => __('services.billing_recurring_hint'),
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::OneTime => 'bg-slate-100 text-slate-700 ring-slate-500/20 dark:bg-slate-500/10 dark:text-slate-300 dark:ring-slate-400/30',
            self::Recurring => 'bg-navy-50 text-navy-700 ring-navy-600/20 dark:bg-navy-500/10 dark:text-navy-200 dark:ring-navy-400/30',
        };
    }

    public function createsSubscription(): bool
    {
        return $this === self::Recurring;
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
