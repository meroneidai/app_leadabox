<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('invoice'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return InvoiceRules::shared($this->route('invoice')->company_id);
    }

    protected function prepareForValidation(): void
    {
        InvoiceRules::prepare($this);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return InvoiceRules::messages();
    }
}
