<?php

namespace App\Http\Requests;

use App\Enums\BillingInterval;
use App\Enums\BillingType;
use Illuminate\Validation\Rule;

/**
 * Validation shared by service create and update.
 */
class ServiceRules
{
    /**
     * @return array<string, mixed>
     */
    public static function shared(?int $companyId, ?int $ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable', 'string', 'max:50',
                Rule::unique('services', 'code')
                    ->where(fn ($query) => $query->where('company_id', $companyId))
                    ->ignore($ignoreId),
            ],
            'service_category_id' => [
                'nullable',
                Rule::exists('service_categories', 'id')->where('company_id', $companyId),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'unit' => ['required', 'string', 'max:30'],

            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'billing_type' => ['required', Rule::in(BillingType::values())],
            // A recurring service without an interval could never be renewed.
            'billing_interval' => [
                'nullable',
                'required_if:billing_type,'.BillingType::Recurring->value,
                Rule::in(BillingInterval::values()),
            ],
            'tracks_expiry' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
