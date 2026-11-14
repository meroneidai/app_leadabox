<?php

namespace App\Models;

use App\Enums\QuotationStatus;
use App\Models\Concerns\HasComments;
use App\Support\Money;
use Database\Factories\QuotationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

#[Fillable([
    'customer_id', 'contact_id', 'issue_date', 'valid_until',
    'title', 'intro', 'terms', 'internal_notes', 'currency',
])]
class Quotation extends Model
{
    /** @use HasFactory<QuotationFactory> */
    use HasComments, HasFactory;

    /** Mirrors the database defaults so a freshly built model is never half-initialised. */
    protected $attributes = [
        'status' => 'draft',
        'currency' => 'SEK',
        'subtotal' => 0,
        'discount_total' => 0,
        'vat_total' => 0,
        'total' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => QuotationStatus::class,
            'issue_date' => 'date',
            'valid_until' => 'date',
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'vat_total' => 'decimal:2',
            'total' => 'decimal:2',
            'sent_at' => 'datetime',
            'viewed_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Quotation $quotation) {
            $quotation->public_token ??= Str::random(48);
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

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return HasMany<QuotationLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(QuotationLine::class)->orderBy('sort_order')->orderBy('id');
    }

    /** @param Builder<Quotation> $query */
    public function scopeForCompany(Builder $query, ?int $companyId): void
    {
        $query->where('company_id', $companyId);
    }

    /** @param Builder<Quotation> $query */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $query->where(fn (Builder $query) => $query
            ->where('number', 'like', "%{$term}%")
            ->orWhere('title', 'like', "%{$term}%")
            ->orWhereHas('customer', fn (Builder $customer) => $customer->where('name', 'like', "%{$term}%")));
    }

    public function isEditable(): bool
    {
        return $this->status->isEditable();
    }

    public function isExpired(): bool
    {
        return $this->status === QuotationStatus::Sent
            && $this->valid_until->endOfDay()->isPast();
    }

    public function canBeAnsweredByCustomer(): bool
    {
        return $this->status === QuotationStatus::Sent && ! $this->isExpired();
    }

    /**
     * VAT grouped by rate, because an invoice must show each rate separately.
     *
     * @return Collection<string, array{rate: string, net: string, vat: string}>
     */
    public function vatBreakdown(): Collection
    {
        return $this->lines
            ->groupBy(fn (QuotationLine $line) => (string) $line->vat_rate)
            ->map(fn (Collection $lines, string $rate) => [
                'rate' => $rate,
                'net' => $lines->reduce(
                    fn (string $carry, QuotationLine $line) => Money::add($carry, (string) $line->line_subtotal),
                    Money::zero()
                ),
                'vat' => $lines->reduce(
                    fn (string $carry, QuotationLine $line) => Money::add($carry, (string) $line->line_vat),
                    Money::zero()
                ),
            ]);
    }

    public function reference(): string
    {
        return $this->number ?? __('quotations.draft_reference');
    }
}
