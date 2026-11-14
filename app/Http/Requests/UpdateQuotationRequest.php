<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('quotation'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return QuotationRules::shared($this->route('quotation')->company_id);
    }

    protected function prepareForValidation(): void
    {
        QuotationRules::prepare($this);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return QuotationRules::messages();
    }
}
