<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Validation shared by invoice create and update.
 */
class InvoiceRules
{
    /**
     * @return array<string, mixed>
     */
    public static function shared(?int $companyId): array
    {
        return [
            'customer_id' => [
                'required',
                Rule::exists('customers', 'id')->where('company_id', $companyId)->whereNull('deleted_at'),
            ],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'project_id' => [
                'nullable',
                Rule::exists('projects', 'id')->where('company_id', $companyId),
            ],

            'issue_date' => ['required', 'date'],
            'payment_terms_days' => ['required', 'integer', 'min:0', 'max:365'],

            'title' => ['nullable', 'string', 'max:255'],
            'customer_reference' => ['nullable', 'string', 'max:255'],
            'intro' => ['nullable', 'string', 'max:5000'],
            'terms' => ['nullable', 'string', 'max:5000'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],

            'lines' => ['required', 'array', 'min:1'],
            'lines.*.service_id' => [
                'nullable',
                Rule::exists('services', 'id')->where('company_id', $companyId),
            ],
            'lines.*.name' => ['required', 'string', 'max:255'],
            'lines.*.description' => ['nullable', 'string', 'max:2000'],
            'lines.*.unit' => ['required', 'string', 'max:30'],
            'lines.*.quantity' => ['required', 'numeric', 'not_in:0', 'min:-99999.99', 'max:99999.99'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'lines.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lines.*.vat_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public static function prepare(FormRequest|Request $request): void
    {
        $lines = collect($request->input('lines', []))
            ->filter(fn ($line) => filled($line['name'] ?? null))
            ->values()
            ->all();

        $request->merge(['lines' => $lines]);
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'lines.required' => __('invoices.lines_required'),
            'lines.min' => __('invoices.lines_required'),
        ];
    }
}
