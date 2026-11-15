<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Idempotent application setup: company, roles/permissions, service catalogue,
 * and the configured Leadabox administrator. No invented customer data.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            RolePermissionSeeder::class,
            ServiceSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
