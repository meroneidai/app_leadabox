<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewServices->value);
    }

    public function view(User $user, Service $service): bool
    {
        return $user->can(Permission::ViewServices->value)
            && $user->company_id === $service->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageServices->value);
    }

    public function update(User $user, Service $service): bool
    {
        return $user->can(Permission::ManageServices->value)
            && $user->company_id === $service->company_id;
    }

    public function delete(User $user, Service $service): bool
    {
        return $this->update($user, $service);
    }
}
