<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageSettings', Company::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $current = Company::current();

        return [
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'org_number' => ['nullable', 'string', 'max:20'],
            'vat_number' => ['nullable', 'string', 'max:30'],
            'has_f_skatt' => ['boolean'],

            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],

            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'size:2'],

            'currency' => ['required', 'string', 'size:3'],
            'locale' => ['required', Rule::in(array_keys(config('app.supported_locales')))],
            'default_vat_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'payment_terms_days' => ['required', 'integer', 'min:0', 'max:365'],

            'quotation_prefix' => ['required', 'string', 'max:10'],
            'quotation_valid_days' => ['required', 'integer', 'min:1', 'max:365'],
            'invoice_prefix' => ['required', 'string', 'max:10'],
            // Sequential numbering is a legal requirement, so counters may only move forward.
            'invoice_next_number' => ['required', 'integer', 'min:'.($current?->invoice_next_number ?? 1)],
            'quotation_next_number' => ['required', 'integer', 'min:'.($current?->quotation_next_number ?? 1)],

            'bankgiro' => ['nullable', 'string', 'max:30'],
            'plusgiro' => ['nullable', 'string', 'max:30'],
            'payment_qr_enabled' => ['boolean'],
            'iban' => ['nullable', 'string', 'max:40'],
            'bic' => ['nullable', 'string', 'max:20'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'invoice_footer' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'has_f_skatt' => $this->boolean('has_f_skatt'),
            'payment_qr_enabled' => $this->boolean('payment_qr_enabled'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'invoice_next_number.min' => __('settings.invoice_next_number_hint'),
            'quotation_next_number.min' => __('settings.invoice_next_number_hint'),
        ];
    }
}
