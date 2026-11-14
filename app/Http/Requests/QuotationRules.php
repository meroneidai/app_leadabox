<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Validation shared by quotation create and update.
 */
class QuotationRules
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

            'issue_date' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after_or_equal:issue_date'],

            'title' => ['nullable', 'string', 'max:255'],
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
            'lines.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'lines.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lines.*.vat_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /** Strips rows the user added then left empty. */
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
            'lines.required' => __('quotations.lines_required'),
            'lines.min' => __('quotations.lines_required'),
        ];
    }
}
