<?php

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::current();

        if (! $company) {
            throw new RuntimeException('Seed the Leadabox company before creating the administrator.');
        }

        $email = strtolower(trim((string) config('app.admin.email', 'meroneidai@gmail.com')));
        $admin = User::query()->firstOrNew(['email' => $email]);

        if (! $admin->exists) {
            $password = config('app.admin.password');

            if (blank($password)) {
                throw new RuntimeException(
                    'Set LEADABOX_ADMIN_PASSWORD before seeding a new administrator account.'
                );
            }

            $admin->password = $password;
            $admin->is_active = true;
        }

        $admin->company_id = $company->id;
        $admin->name = $admin->name ?: config('app.admin.name', 'Leadabox Admin');
        $admin->job_title = $admin->job_title ?: 'Owner';
        $admin->save();
        $admin->syncRoles([RoleEnum::Admin->value]);
    }
}
