<?php

namespace App\Http\Requests;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Customer::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...CustomerRules::shared(),
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255', 'required_with:contact_name'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_job_title' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'reverse_charge' => $this->boolean('reverse_charge'),
            'type' => $this->input('type', CustomerType::Company->value),
            'status' => $this->input('status', CustomerStatus::Prospect->value),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        return collect(parent::validated())
            ->except(['contact_name', 'contact_email', 'contact_phone', 'contact_job_title'])
            ->all();
    }
}
