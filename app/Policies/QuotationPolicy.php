<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewQuotations->value);
    }

    public function view(User $user, Quotation $quotation): bool
    {
        return $user->can(Permission::ViewQuotations->value)
            && $user->company_id === $quotation->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageQuotations->value);
    }

    /** Sent quotations are a record of what the customer received. */
    public function update(User $user, Quotation $quotation): bool
    {
        return $user->can(Permission::ManageQuotations->value)
            && $user->company_id === $quotation->company_id
            && $quotation->isEditable();
    }

    public function send(User $user, Quotation $quotation): bool
    {
        return $user->can(Permission::ManageQuotations->value)
            && $user->company_id === $quotation->company_id
            && $quotation->isEditable()
            && $quotation->lines()->exists();
    }

    public function decide(User $user, Quotation $quotation): bool
    {
        return $user->can(Permission::ManageQuotations->value)
            && $user->company_id === $quotation->company_id
            && ! $quotation->status->isDecided();
    }

    public function delete(User $user, Quotation $quotation): bool
    {
        return $user->can(Permission::ManageQuotations->value)
            && $user->company_id === $quotation->company_id
            && $quotation->isEditable();
    }
}
