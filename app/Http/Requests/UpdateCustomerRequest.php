<?php

namespace App\Http\Requests;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('customer'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return CustomerRules::shared();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'reverse_charge' => $this->boolean('reverse_charge'),
            'type' => $this->input('type', CustomerType::Company->value),
            'status' => $this->input('status', CustomerStatus::Prospect->value),
        ]);
    }
}
