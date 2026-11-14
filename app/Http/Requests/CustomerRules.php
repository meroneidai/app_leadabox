<?php

namespace App\Http\Requests;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use Illuminate\Validation\Rule;

/**
 * Validation shared by customer create and update.
 */
class CustomerRules
{
    /**
     * @return array<string, mixed>
     */
    public static function shared(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(CustomerType::values())],
            'status' => ['required', Rule::in(CustomerStatus::values())],

            // Swedish organisation numbers are 10 digits, usually written NNNNNN-NNNN.
            'org_number' => ['nullable', 'string', 'max:20', 'regex:/^\d{6,8}[-\s]?\d{4}$/'],
            'vat_number' => ['nullable', 'string', 'max:30'],

            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],

            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'size:2'],

            'payment_terms_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'reverse_charge' => ['boolean'],

            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
