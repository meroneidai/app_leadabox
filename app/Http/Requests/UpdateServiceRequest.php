<?php

namespace App\Http\Requests;

use App\Enums\BillingType;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('service'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Service $service */
        $service = $this->route('service');

        return ServiceRules::shared($service->company_id, $service->id);
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
