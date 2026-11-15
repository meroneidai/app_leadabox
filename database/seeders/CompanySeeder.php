<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        if (! Company::query()->exists()) {
            Company::query()->create([
                'name' => 'Leadabox',
                'legal_name' => 'Leadabox AB',
                'email' => 'support@leadabox.com',
                'phone' => '+46 76 410 33 40',
                'website' => 'https://leadabox.com',
                'country' => 'SE',
                'currency' => 'SEK',
                'locale' => 'en',
                'default_vat_rate' => 25.00,
                'payment_terms_days' => 30,
                'quotation_prefix' => 'Q',
                'invoice_prefix' => 'F',
                'invoice_footer' => null,
            ]);
        }

        Company::forgetCurrent();
    }
}
