<?php

namespace Tests\Feature;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Enums\Role;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Acme AB',
            'type' => CustomerType::Company->value,
            'status' => CustomerStatus::Active->value,
            'org_number' => '556677-8899',
            'country' => 'SE',
            ...$overrides,
        ];
    }

    public function test_sales_can_create_a_customer_with_a_primary_contact(): void
    {
        $user = $this->staff(Role::Sales);

        $this->actingAs($user)->post(route('customers.store'), $this->payload([
            'contact_name' => 'Anna Svensson',
            'contact_email' => 'anna@acme.se',
        ]));

        $customer = Customer::firstOrFail();

        $this->assertSame('Acme AB', $customer->name);
        $this->assertSame($user->company_id, $customer->company_id);
        $this->assertSame('0001', $customer->number);

        $contact = $customer->contacts()->firstOrFail();
        $this->assertTrue($contact->is_primary);
        $this->assertTrue($contact->receives_invoices);
    }

    public function test_customer_numbers_increment(): void
    {
        $user = $this->staff(Role::Sales);

        $this->actingAs($user)->post(route('customers.store'), $this->payload(['name' => 'First AB']));
        $this->actingAs($user)->post(route('customers.store'), $this->payload(['name' => 'Second AB']));

        $this->assertSame(['0001', '0002'], Customer::orderBy('id')->pluck('number')->all());
    }

    public function test_contact_email_is_required_when_a_contact_name_is_given(): void
    {
        $this->actingAs($this->staff(Role::Sales))
            ->post(route('customers.store'), $this->payload(['contact_name' => 'No Email']))
            ->assertSessionHasErrors('contact_email');
    }

    public function test_invalid_org_number_is_rejected(): void
    {
        $this->actingAs($this->staff(Role::Sales))
            ->post(route('customers.store'), $this->payload(['org_number' => 'not-a-number']))
            ->assertSessionHasErrors('org_number');
    }

    public function test_support_cannot_create_customers_but_can_view_them(): void
    {
        $support = $this->staff(Role::Support);

        $this->actingAs($support)->get(route('customers.index'))->assertOk();
        $this->actingAs($support)->post(route('customers.store'), $this->payload())->assertForbidden();
    }

    public function test_customer_can_be_searched_by_name_and_contact(): void
    {
        $user = $this->staff(Role::Sales);
        $match = Customer::factory()->create(['company_id' => $user->company_id, 'name' => 'Northern Lights AB']);
        Customer::factory()->create(['company_id' => $user->company_id, 'name' => 'Southern Seas AB']);
        Contact::factory()->for($match)->create(['name' => 'Erik Unique']);

        $this->actingAs($user)->get(route('customers.index', ['q' => 'Northern']))
            ->assertOk()
            ->assertSee('Northern Lights AB')
            ->assertDontSee('Southern Seas AB');

        $this->actingAs($user)->get(route('customers.index', ['q' => 'Erik Unique']))
            ->assertOk()
            ->assertSee('Northern Lights AB');
    }

    public function test_vat_and_payment_terms_fall_back_to_company_defaults(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = Customer::factory()->create([
            'company_id' => $user->company_id,
            'payment_terms_days' => null,
            'vat_rate' => null,
        ]);

        $this->assertSame($this->company()->payment_terms_days, $customer->paymentTermsDays());
        $this->assertSame($this->company()->default_vat_rate, $customer->vatRate());
    }

    public function test_reverse_charge_forces_zero_vat(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->company()->id,
            'vat_rate' => 25,
            'reverse_charge' => true,
        ]);

        $this->assertSame('0.00', $customer->vatRate());
    }

    public function test_only_one_contact_can_be_primary(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = Customer::factory()->create(['company_id' => $user->company_id]);
        $first = Contact::factory()->for($customer)->primary()->create();

        $this->actingAs($user)->post(route('customers.contacts.store', $customer), [
            'name' => 'Second Person',
            'email' => 'second@acme.se',
            'is_primary' => '1',
        ]);

        $this->assertFalse($first->fresh()->is_primary);
        $this->assertSame(1, $customer->contacts()->where('is_primary', true)->count());
    }

    public function test_deleting_a_customer_archives_it(): void
    {
        $user = $this->staff(Role::Manager);
        $customer = Customer::factory()->create(['company_id' => $user->company_id]);

        $this->actingAs($user)->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers.index'));

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_customer_from_another_company_is_not_visible(): void
    {
        $user = $this->staff(Role::Sales);
        $foreign = Customer::factory()->create(['company_id' => Company::factory()->create()->id]);

        $this->actingAs($user)->get(route('customers.show', $foreign))->assertForbidden();
    }
}
