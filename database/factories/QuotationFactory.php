<?php

namespace Database\Factories;

use App\Enums\QuotationStatus;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quotation>
 */
class QuotationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => fn () => Company::current()?->id ?? Company::factory(),
            'customer_id' => Customer::factory(),
            'status' => QuotationStatus::Draft,
            'currency' => 'SEK',
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'title' => fake()->catchPhrase(),
        ];
    }

    public function sent(): static
    {
        return $this->state(fn () => [
            'status' => QuotationStatus::Sent,
            'number' => 'Q'.fake()->unique()->numerify('####'),
            'sent_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->sent()->state(fn () => [
            'valid_until' => now()->subDay()->toDateString(),
        ]);
    }
}
