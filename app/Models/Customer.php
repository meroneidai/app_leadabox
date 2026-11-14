<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name', 'type', 'status', 'org_number', 'vat_number',
    'email', 'phone', 'website',
    'address_line1', 'address_line2', 'postal_code', 'city', 'country',
    'payment_terms_days', 'vat_rate', 'reverse_charge', 'notes',
])]
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => CustomerType::class,
            'status' => CustomerStatus::class,
            'reverse_charge' => 'boolean',
            'vat_rate' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            $customer->number ??= static::nextNumberFor($customer->company_id);
        });
    }

    /** Sequential per company; archived customers still reserve their number. */
    public static function nextNumberFor(?int $companyId): string
    {
        $last = static::withTrashed()->where('company_id', $companyId)->max('number');

        return str_pad((string) (((int) $last) + 1), 4, '0', STR_PAD_LEFT);
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** @return HasMany<Contact, $this> */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /** @return HasMany<Project, $this> */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /** @param Builder<Customer> $query */
    public function scopeForCompany(Builder $query, ?int $companyId): void
    {
        $query->where('company_id', $companyId);
    }

    /** @param Builder<Customer> $query */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $query->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('number', 'like', "%{$term}%")
                ->orWhere('org_number', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhereHas('contacts', fn (Builder $contacts) => $contacts
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"));
        });
    }

    public function primaryContact(): ?Contact
    {
        return $this->contacts->firstWhere('is_primary', true) ?? $this->contacts->first();
    }

    /** Customer-specific terms win; otherwise fall back to the company default. */
    public function paymentTermsDays(): int
    {
        return $this->payment_terms_days ?? $this->company?->payment_terms_days ?? 30;
    }

    public function vatRate(): string
    {
        if ($this->reverse_charge) {
            return '0.00';
        }

        return $this->vat_rate ?? $this->company?->default_vat_rate ?? '25.00';
    }

    /** @return array<int, string> */
    public function addressLines(): array
    {
        return array_values(array_filter([
            $this->address_line1,
            $this->address_line2,
            trim("{$this->postal_code} {$this->city}"),
            $this->country,
        ]));
    }
}
