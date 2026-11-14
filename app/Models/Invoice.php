<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Models\Concerns\HasComments;
use App\Support\Money;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

#[Fillable([
    'customer_id', 'contact_id', 'project_id', 'issue_date', 'due_date', 'payment_terms_days',
    'title', 'intro', 'terms', 'internal_notes', 'customer_reference', 'currency',
])]
class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasComments, HasFactory;

    public const TYPE_INVOICE = 'invoice';

    public const TYPE_CREDIT_NOTE = 'credit_note';

    /** Mirrors the database defaults so a freshly built model is never half-initialised. */
    protected $attributes = [
        'type' => self::TYPE_INVOICE,
        'status' => 'draft',
        'currency' => 'SEK',
        'payment_terms_days' => 30,
        'subtotal' => 0,
        'discount_total' => 0,
        'vat_total' => 0,
        'total' => 0,
        'amount_paid' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'vat_total' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'issued_at' => 'datetime',
            'sent_at' => 'datetime',
            'viewed_at' => 'datetime',
            'last_reminder_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            $invoice->public_token ??= Str::random(48);
        });
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return BelongsTo<Contact, $this> */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /** @return BelongsTo<Quotation, $this> */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /** @return BelongsTo<Project, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return BelongsTo<Invoice, $this> */
    public function creditedInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'credits_invoice_id');
    }

    /** @return HasMany<Invoice, $this> */
    public function creditNotes(): HasMany
    {
        return $this->hasMany(Invoice::class, 'credits_invoice_id');
    }

    /** @return HasMany<InvoiceLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class)->orderBy('sort_order')->orderBy('id');
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderByDesc('paid_on');
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @param Builder<Invoice> $query */
    public function scopeForCompany(Builder $query, ?int $companyId): void
    {
        $query->where('company_id', $companyId);
    }

    /** @param Builder<Invoice> $query */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $query->where(fn (Builder $query) => $query
            ->where('number', 'like', "%{$term}%")
            ->orWhere('ocr_reference', 'like', "%{$term}%")
            ->orWhere('title', 'like', "%{$term}%")
            ->orWhereHas('customer', fn (Builder $customer) => $customer->where('name', 'like', "%{$term}%")));
    }

    /** @param Builder<Invoice> $query */
    public function scopeOverdue(Builder $query): void
    {
        $query->whereIn('status', [InvoiceStatus::Issued, InvoiceStatus::PartiallyPaid])
            ->whereDate('due_date', '<', now());
    }

    public function isCreditNote(): bool
    {
        return $this->type === self::TYPE_CREDIT_NOTE;
    }

    public function isEditable(): bool
    {
        return $this->status->isEditable();
    }

    public function isOverdue(): bool
    {
        return $this->status->acceptsPayment() && $this->due_date->endOfDay()->isPast();
    }

    public function amountDue(): string
    {
        return Money::sub((string) $this->total, (string) $this->amount_paid);
    }

    public function isFullyPaid(): bool
    {
        return bccomp((string) $this->amount_paid, (string) $this->total, Money::SCALE) >= 0;
    }

    public function daysOverdue(): int
    {
        return $this->isOverdue() ? (int) $this->due_date->endOfDay()->diffInDays(now()) : 0;
    }

    /**
     * @return Collection<string, array{rate: string, net: string, vat: string}>
     */
    public function vatBreakdown(): Collection
    {
        return $this->lines
            ->groupBy(fn (InvoiceLine $line) => (string) $line->vat_rate)
            ->map(fn (Collection $lines, string $rate) => [
                'rate' => $rate,
                'net' => $lines->reduce(
                    fn (string $carry, InvoiceLine $line) => Money::add($carry, (string) $line->line_subtotal),
                    Money::zero()
                ),
                'vat' => $lines->reduce(
                    fn (string $carry, InvoiceLine $line) => Money::add($carry, (string) $line->line_vat),
                    Money::zero()
                ),
            ]);
    }

    public function reference(): string
    {
        return $this->number ?? __('invoices.draft_reference');
    }
}
