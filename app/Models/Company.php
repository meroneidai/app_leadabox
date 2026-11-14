<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'legal_name', 'org_number', 'vat_number', 'has_f_skatt',
    'email', 'phone', 'website',
    'address_line1', 'address_line2', 'postal_code', 'city', 'country',
    'logo_path', 'currency', 'locale',
    'default_vat_rate', 'payment_terms_days',
    'quotation_prefix', 'quotation_next_number', 'quotation_valid_days',
    'invoice_prefix', 'invoice_next_number',
    'bankgiro', 'plusgiro', 'payment_qr_enabled', 'iban', 'bic', 'bank_name', 'invoice_footer',
])]
class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

    /** Resolved once per request; settings rarely change mid-request. */
    protected static ?self $current = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_f_skatt' => 'boolean',
            'payment_qr_enabled' => 'boolean',
            'default_vat_rate' => 'decimal:2',
        ];
    }

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** @return HasMany<Project, $this> */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /** The operating company. Multi-tenancy will replace this lookup, not the call sites. */
    public static function current(): ?self
    {
        return static::$current ??= static::query()->orderBy('id')->first();
    }

    public static function forgetCurrent(): void
    {
        static::$current = null;
    }

    /** @return array<int, string> */
    public function addressLines(): array
    {
        return array_values(array_filter([
            $this->legal_name ?: $this->name,
            $this->address_line1,
            $this->address_line2,
            trim("{$this->postal_code} {$this->city}"),
            $this->country,
        ]));
    }
}
