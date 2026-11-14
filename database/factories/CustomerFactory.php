<?php

namespace Database\Factories;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => fn () => Company::current()?->id ?? Company::factory(),
            'name' => fake()->company(),
            'type' => CustomerType::Company,
            'status' => fake()->randomElement(CustomerStatus::cases()),
            'org_number' => fake()->numerify('######-####'),
            'vat_number' => 'SE'.fake()->numerify('############'),
            'email' => fake()->companyEmail(),
            'phone' => fake()->numerify('+46 ## ### ## ##'),
            'website' => 'https://'.fake()->domainName(),
            'address_line1' => fake()->streetAddress(),
            'postal_code' => fake()->numerify('### ##'),
            'city' => fake()->city(),
            'country' => 'SE',
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => CustomerStatus::Active]);
    }
}
