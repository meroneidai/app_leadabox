<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request, Customer $customer): RedirectResponse
    {
        DB::transaction(function () use ($request, $customer) {
            $contact = $customer->contacts()->create($request->validated());

            $this->enforceSinglePrimary($customer, $contact);
        });

        return back()->with('status', __('customers.contacts.created'));
    }

    public function update(UpdateContactRequest $request, Contact $contact): RedirectResponse
    {
        DB::transaction(function () use ($request, $contact) {
            $contact->update($request->validated());

            $this->enforceSinglePrimary($contact->customer, $contact);
        });

        return back()->with('status', __('customers.contacts.updated'));
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $this->authorize('update', $contact->customer);

        $contact->delete();

        return back()->with('status', __('customers.contacts.deleted'));
    }

    private function enforceSinglePrimary(Customer $customer, Contact $contact): void
    {
        if (! $contact->is_primary) {
            return;
        }

        $customer->contacts()
            ->whereKeyNot($contact->id)
            ->update(['is_primary' => false]);
    }
}
