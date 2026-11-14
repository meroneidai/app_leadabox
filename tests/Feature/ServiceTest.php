<?php

namespace Tests\Feature;

use App\Enums\BillingInterval;
use App\Enums\BillingType;
use App\Enums\Role;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Web Hosting — Business',
            'code' => 'HOST-PRO',
            'unit' => 'year',
            'price' => '6000.00',
            'cost' => '1500.00',
            'billing_type' => BillingType::OneTime->value,
            'is_active' => '1',
            ...$overrides,
        ];
    }

    public function test_manager_can_create_a_one_time_service(): void
    {
        $user = $this->staff(Role::Manager);

        $this->actingAs($user)
            ->post(route('services.store'), $this->payload())
            ->assertRedirect(route('services.index'));

        $service = Service::firstOrFail();

        $this->assertSame($user->company_id, $service->company_id);
        $this->assertSame(BillingType::OneTime, $service->billing_type);
        $this->assertNull($service->billing_interval);
    }

    public function test_recurring_service_requires_an_interval(): void
    {
        $this->actingAs($this->staff(Role::Manager))
            ->post(route('services.store'), $this->payload([
                'billing_type' => BillingType::Recurring->value,
                'billing_interval' => null,
            ]))
            ->assertSessionHasErrors('billing_interval');
    }

    public function test_recurring_service_stores_its_interval(): void
    {
        $this->actingAs($this->staff(Role::Manager))
            ->post(route('services.store'), $this->payload([
                'billing_type' => BillingType::Recurring->value,
                'billing_interval' => BillingInterval::Yearly->value,
                'tracks_expiry' => '1',
            ]));

        $service = Service::firstOrFail();

        $this->assertTrue($service->isRecurring());
        $this->assertSame(BillingInterval::Yearly, $service->billing_interval);
        $this->assertTrue($service->tracks_expiry);
    }

    public function test_switching_to_one_time_clears_the_interval(): void
    {
        $user = $this->staff(Role::Manager);
        $service = Service::factory()->recurring()->create(['company_id' => $user->company_id]);

        $this->actingAs($user)->put(route('services.update', $service), $this->payload([
            'code' => 'OTHER',
            'billing_type' => BillingType::OneTime->value,
            'billing_interval' => BillingInterval::Monthly->value,
        ]));

        $this->assertNull($service->fresh()->billing_interval);
    }

    public function test_service_code_is_unique_per_company(): void
    {
        $user = $this->staff(Role::Manager);
        Service::factory()->create(['company_id' => $user->company_id, 'code' => 'HOST-PRO']);

        $this->actingAs($user)
            ->post(route('services.store'), $this->payload())
            ->assertSessionHasErrors('code');
    }

    public function test_vat_rate_falls_back_to_the_company_default(): void
    {
        $service = Service::factory()->create([
            'company_id' => $this->company()->id,
            'vat_rate' => null,
        ]);

        $this->assertSame($this->company()->default_vat_rate, $service->vatRate());
    }

    public function test_margin_is_calculated_from_price_and_cost(): void
    {
        $service = Service::factory()->create([
            'company_id' => $this->company()->id,
            'price' => '1000.00',
            'cost' => '250.00',
        ]);

        $this->assertSame(75.0, $service->marginPercent());
    }

    public function test_sales_can_view_but_not_edit_services(): void
    {
        $sales = $this->staff(Role::Sales);

        $this->actingAs($sales)->get(route('services.index'))->assertOk();
        $this->actingAs($sales)->post(route('services.store'), $this->payload())->assertForbidden();
    }

    public function test_support_cannot_access_the_catalogue(): void
    {
        $this->actingAs($this->staff(Role::Support))
            ->get(route('services.index'))
            ->assertForbidden();
    }

    public function test_deleting_a_service_deactivates_it(): void
    {
        $user = $this->staff(Role::Manager);
        $service = Service::factory()->create(['company_id' => $user->company_id]);

        $this->actingAs($user)->delete(route('services.destroy', $service));

        $this->assertFalse($service->fresh()->is_active);
        $this->assertDatabaseHas('services', ['id' => $service->id]);
    }

    public function test_yearly_interval_advances_twelve_months(): void
    {
        $next = BillingInterval::Yearly->nextDateFrom(now()->setDate(2026, 1, 31));

        $this->assertSame('2027-01-31', $next->toDateString());
    }
}
