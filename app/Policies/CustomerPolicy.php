<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewCustomers->value);
    }

    public function view(User $user, Customer $customer): bool
    {
        return ($user->can(Permission::ViewCustomers->value)
                || $user->can(Permission::ManageCustomers->value))
            && $this->belongsToCompany($user, $customer);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageCustomers->value);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->can(Permission::ManageCustomers->value)
            && $this->belongsToCompany($user, $customer);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $this->update($user, $customer);
    }

    private function belongsToCompany(User $user, Customer $customer): bool
    {
        $companyId = $user->company_id ?? Company::current()?->id;

        return $companyId !== null
            && $customer->company_id !== null
            && (int) $companyId === (int) $customer->company_id;
    }
}
