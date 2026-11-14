<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::query()->firstOrCreate(
            ['org_number' => '000000-0000'],
            [
                'name' => 'Leadabox',
                'legal_name' => 'Leadabox AB',
                'vat_number' => 'SE000000000001',
                'has_f_skatt' => true,
                'email' => 'support@leadabox.com',
                'phone' => '+46 76 410 33 40',
                'website' => 'https://leadabox.com',
                'address_line1' => 'Perstorpsvägen 19',
                'postal_code' => '123 64',
                'city' => 'Stockholm',
                'country' => 'SE',
                'currency' => 'SEK',
                'locale' => 'en',
                'default_vat_rate' => 25.00,
                'payment_terms_days' => 30,
                'quotation_prefix' => 'Q',
                'invoice_prefix' => 'F',
                'invoice_footer' => null,
            ]
        );

        Company::forgetCurrent();
    }
}
