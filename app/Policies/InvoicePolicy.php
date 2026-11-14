<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewInvoices->value);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can(Permission::ViewInvoices->value)
            && $user->company_id === $invoice->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageInvoices->value);
    }

    /** An issued invoice is a financial record and can never be edited. */
    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can(Permission::ManageInvoices->value)
            && $user->company_id === $invoice->company_id
            && $invoice->isEditable();
    }

    public function issue(User $user, Invoice $invoice): bool
    {
        return $this->update($user, $invoice) && $invoice->lines()->exists();
    }

    public function recordPayment(User $user, Invoice $invoice): bool
    {
        return $user->can(Permission::ManageInvoices->value)
            && $user->company_id === $invoice->company_id
            && $invoice->status->acceptsPayment();
    }

    public function credit(User $user, Invoice $invoice): bool
    {
        return $user->can(Permission::ManageInvoices->value)
            && $user->company_id === $invoice->company_id
            && $invoice->status->isIssued()
            && ! $invoice->isCreditNote();
    }

    /** Only an unissued draft may be deleted; numbering must stay gap-free. */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $this->update($user, $invoice);
    }
}
