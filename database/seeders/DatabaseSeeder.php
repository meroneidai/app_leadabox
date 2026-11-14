<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Production-shaped seed: the company, its roles and the real service catalogue.
 * No invented customers, projects or quotations.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            RolePermissionSeeder::class,
            ServiceSeeder::class,
        ]);

        $company = Company::current();

        $admin = User::firstOrCreate(
            ['email' => 'admin@leadabox.com'],
            [
                'company_id' => $company?->id,
                'name' => 'Admin',
                'job_title' => 'Owner',
                'password' => 'password',
                'is_active' => true,
            ],
        );

        $admin->syncRoles([Role::Admin->value]);
    }
}
