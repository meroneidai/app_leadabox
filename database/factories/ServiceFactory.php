<?php

namespace Database\Factories;

use App\Enums\BillingInterval;
use App\Enums\BillingType;
use App\Models\Company;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => fn () => Company::current()?->id ?? Company::factory(),
            'name' => fake()->unique()->catchPhrase(),
            'code' => strtoupper(fake()->unique()->bothify('SRV-###')),
            'unit' => 'item',
            'price' => fake()->randomFloat(2, 500, 50000),
            'cost' => fake()->randomFloat(2, 100, 400),
            'billing_type' => BillingType::OneTime,
            'is_active' => true,
        ];
    }

    public function recurring(BillingInterval $interval = BillingInterval::Yearly): static
    {
        return $this->state(fn () => [
            'billing_type' => BillingType::Recurring,
            'billing_interval' => $interval,
        ]);
    }
}
