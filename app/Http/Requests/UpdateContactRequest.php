<?php

namespace App\Http\Requests;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Contact $contact */
        $contact = $this->route('contact');

        return $this->user()->can('update', $contact->customer);
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
