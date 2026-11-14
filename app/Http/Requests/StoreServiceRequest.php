<?php

namespace App\Http\Requests;

use App\Enums\BillingType;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Service::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return ServiceRules::shared($this->user()->company_id);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tracks_expiry' => $this->boolean('tracks_expiry'),
            'is_active' => $this->boolean('is_active'),
            'billing_interval' => $this->input('billing_type') === BillingType::Recurring->value
                ? $this->input('billing_interval')
                : null,
        ]);
    }
}
