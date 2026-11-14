<?php

namespace App\Http\Requests;

use App\Models\Quotation;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Quotation::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return QuotationRules::shared($this->user()->company_id);
    }

    protected function prepareForValidation(): void
    {
        QuotationRules::prepare($this);
    }
}
