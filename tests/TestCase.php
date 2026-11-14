<?php

namespace Tests;

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    private static bool $rolesSeeded = false;

    protected function setUp(): void
    {
        parent::setUp();

        self::$rolesSeeded = false;
        Company::forgetCurrent();
    }

    /** Create a staff member of the operating company with the given role. */
    protected function staff(Role $role = Role::Admin, array $attributes = []): User
    {
        $this->seedRoles();

        $user = User::factory()->create([
            'company_id' => $this->company()->id,
            ...$attributes,
        ]);

        $user->assignRole($role->value);

        return $user->fresh();
    }

    protected function company(): Company
    {
        return Company::query()->first() ?? Company::factory()->create();
    }

    protected function seedRoles(): void
    {
        if (self::$rolesSeeded) {
            return;
        }

        $this->seed(RolePermissionSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        self::$rolesSeeded = true;
    }
}
