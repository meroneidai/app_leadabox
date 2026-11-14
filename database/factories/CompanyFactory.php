<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'legal_name' => fake()->company().' AB',
            'org_number' => fake()->numerify('######-####'),
            'vat_number' => 'SE'.fake()->numerify('############'),
            'email' => fake()->companyEmail(),
            'country' => 'SE',
            'currency' => 'SEK',
            'locale' => 'en',
            'default_vat_rate' => 25.00,
            'payment_terms_days' => 30,
        ];
    }
}
