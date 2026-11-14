<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Customer $customer */
        $customer = $this->route('customer');

        return $this->user()->can('update', $customer);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return ContactRules::shared();
    }

    protected function prepareForValidation(): void
    {
        $this->merge(ContactRules::booleans($this));
    }
}
