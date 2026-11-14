<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewTeam->value);
    }

    public function view(User $user, User $member): bool
    {
        return $user->can(Permission::ViewTeam->value)
            && $user->company_id === $member->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageTeam->value);
    }

    /** Everyone may edit their own profile; editing others needs the team permission. */
    public function update(User $user, User $member): bool
    {
        if ($user->is($member)) {
            return true;
        }

        return $user->can(Permission::ManageTeam->value)
            && $user->company_id === $member->company_id;
    }
}
