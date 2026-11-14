<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Issued = 'issued';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Credited = 'credited';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('invoices.status_draft'),
            self::Issued => __('invoices.status_issued'),
            self::PartiallyPaid => __('invoices.status_partially_paid'),
            self::Paid => __('invoices.status_paid'),
            self::Credited => __('invoices.status_credited'),
            self::Cancelled => __('invoices.status_cancelled'),
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-100 text-slate-700 ring-slate-500/20 dark:bg-slate-500/10 dark:text-slate-300 dark:ring-slate-400/30',
            self::Issued => 'bg-navy-50 text-navy-700 ring-navy-600/20 dark:bg-navy-500/10 dark:text-navy-200 dark:ring-navy-400/30',
            self::PartiallyPaid => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-400/30',
            self::Paid => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/30',
            self::Credited => 'bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-500/10 dark:text-violet-300 dark:ring-violet-400/30',
            self::Cancelled => 'bg-slate-100 text-slate-500 ring-slate-400/20 dark:bg-slate-500/10 dark:text-slate-400 dark:ring-slate-500/30',
        };
    }

    /** Only drafts may be edited; an issued invoice is a legal record. */
    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    public function isIssued(): bool
    {
        return ! in_array($this, [self::Draft, self::Cancelled], true);
    }

    public function acceptsPayment(): bool
    {
        return in_array($this, [self::Issued, self::PartiallyPaid], true);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
