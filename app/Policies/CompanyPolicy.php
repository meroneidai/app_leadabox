<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;

class CompanyPolicy
{
    public function manageSettings(User $user): bool
    {
        return $user->can(Permission::ManageSettings->value);
    }
}
