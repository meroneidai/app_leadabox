<?php

namespace App\Enums;

enum QuotationStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('quotations.status_draft'),
            self::Sent => __('quotations.status_sent'),
            self::Accepted => __('quotations.status_accepted'),
            self::Rejected => __('quotations.status_rejected'),
            self::Expired => __('quotations.status_expired'),
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-100 text-slate-700 ring-slate-500/20 dark:bg-slate-500/10 dark:text-slate-300 dark:ring-slate-400/30',
            self::Sent => 'bg-navy-50 text-navy-700 ring-navy-600/20 dark:bg-navy-500/10 dark:text-navy-200 dark:ring-navy-400/30',
            self::Accepted => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/30',
            self::Rejected => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-400/30',
            self::Expired => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-400/30',
        };
    }

    /** Only drafts may be edited; sent quotations are a record of what the customer saw. */
    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    public function isDecided(): bool
    {
        return in_array($this, [self::Accepted, self::Rejected], true);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
