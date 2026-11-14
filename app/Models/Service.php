<?php

namespace App\Models;

use App\Enums\BillingInterval;
use App\Enums\BillingType;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'service_category_id', 'name', 'code', 'description', 'unit',
    'price', 'cost', 'vat_rate',
    'billing_type', 'billing_interval', 'tracks_expiry',
    'is_active', 'sort_order',
])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'billing_type' => BillingType::class,
            'billing_interval' => BillingInterval::class,
            'tracks_expiry' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** @return BelongsTo<ServiceCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /** @param Builder<Service> $query */
    public function scopeForCompany(Builder $query, ?int $companyId): void
    {
        $query->where('company_id', $companyId);
    }

    /** @param Builder<Service> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** @param Builder<Service> $query */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $query->where(fn (Builder $query) => $query
            ->where('name', 'like', "%{$term}%")
            ->orWhere('code', 'like', "%{$term}%"));
    }

    public function isRecurring(): bool
    {
        return $this->billing_type === BillingType::Recurring;
    }

    /** Falls back to the company default so VAT is never hardcoded per service. */
    public function vatRate(): string
    {
        return $this->vat_rate ?? $this->company?->default_vat_rate ?? '25.00';
    }

    public function marginPercent(): ?float
    {
        if ($this->cost === null || (float) $this->price <= 0) {
            return null;
        }

        return round(((float) $this->price - (float) $this->cost) / (float) $this->price * 100, 1);
    }
}
