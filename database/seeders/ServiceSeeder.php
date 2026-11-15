<?php

namespace Database\Seeders;

use App\Enums\BillingInterval;
use App\Enums\BillingType;
use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

/**
 * The real Leadabox catalogue. Prices are starting points and are meant to be edited.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::current();

        if (! $company) {
            return;
        }

        $categories = collect([
            'Web' => 10,
            'Mobile' => 20,
            'AI' => 30,
            'Hosting & Infrastructure' => 40,
            'Marketing' => 50,
        ])->mapWithKeys(fn (int $order, string $name) => [
            $name => ServiceCategory::firstOrCreate(
                ['company_id' => $company->id, 'slug' => str($name)->slug()->value()],
                ['name' => $name, 'sort_order' => $order],
            ),
        ]);

        $services = [
            ['Web', 'WEB-DEV', 'Web Development', 'item', 95000, null, BillingType::OneTime, null, false],
            ['Web', 'WEB-DESIGN', 'Web Design', 'item', 55000, null, BillingType::OneTime, null, false],
            ['Web', 'WEB-HOUR', 'Development hour', 'hour', 1250, 0, BillingType::OneTime, null, false],
            ['Mobile', 'APP-DEV', 'Mobile App Development', 'item', 180000, null, BillingType::OneTime, null, false],
            ['AI', 'AI-AGENT', 'AI Agent', 'month', 4900, 900, BillingType::Recurring, BillingInterval::Monthly, false],
            ['AI', 'AI-INT', 'AI Integration', 'item', 65000, null, BillingType::OneTime, null, false],
            ['Hosting & Infrastructure', 'HOST-BAS', 'Web Hosting — Basic', 'year', 2400, 600, BillingType::Recurring, BillingInterval::Yearly, false],
            ['Hosting & Infrastructure', 'HOST-PRO', 'Web Hosting — Business', 'year', 6000, 1500, BillingType::Recurring, BillingInterval::Yearly, false],
            ['Hosting & Infrastructure', 'DOMAIN', 'Domain registration', 'year', 290, 120, BillingType::Recurring, BillingInterval::Yearly, true],
            ['Hosting & Infrastructure', 'SSL', 'SSL certificate', 'year', 990, 350, BillingType::Recurring, BillingInterval::Yearly, true],
            ['Hosting & Infrastructure', 'CARE', 'Maintenance & updates', 'month', 1490, 300, BillingType::Recurring, BillingInterval::Monthly, false],
            ['Marketing', 'SEO', 'SEO retainer', 'month', 8900, 1200, BillingType::Recurring, BillingInterval::Monthly, false],
        ];

        foreach ($services as $i => [$category, $code, $name, $unit, $price, $cost, $type, $interval, $tracksExpiry]) {
            Service::firstOrCreate(
                ['company_id' => $company->id, 'code' => $code],
                [
                    'service_category_id' => $categories[$category]->id,
                    'name' => $name,
                    'unit' => $unit,
                    'price' => $price,
                    'cost' => $cost,
                    'billing_type' => $type,
                    'billing_interval' => $interval,
                    'tracks_expiry' => $tracksExpiry,
                    'is_active' => true,
                    'sort_order' => $i * 10,
                ],
            );
        }
    }
}
