<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Invoice::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return InvoiceRules::shared($this->user()->company_id);
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
