<?php

namespace Tests\Feature;

use App\Enums\QuotationStatus;
use App\Enums\Role;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(Customer $customer, array $overrides = []): array
    {
        return [
            'customer_id' => $customer->id,
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'title' => 'Website relaunch',
            'lines' => [
                [
                    'name' => 'Web Development',
                    'unit' => 'item',
                    'quantity' => '1',
                    'unit_price' => '10000.00',
                    'discount_percent' => '0',
                    'vat_rate' => '25',
                ],
            ],
            ...$overrides,
        ];
    }

    private function customer(int $companyId): Customer
    {
        return Customer::factory()->create(['company_id' => $companyId]);
    }

    public function test_sales_can_create_a_draft_quotation(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = $this->customer($user->company_id);

        $this->actingAs($user)->post(route('quotations.store'), $this->payload($customer));

        $quotation = Quotation::firstOrFail();

        $this->assertSame(QuotationStatus::Draft, $quotation->status);
        $this->assertNull($quotation->number);
        $this->assertNotNull($quotation->public_token);
        $this->assertSame($user->id, $quotation->created_by);
    }

    public function test_totals_are_calculated_on_the_server(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = $this->customer($user->company_id);

        $this->actingAs($user)->post(route('quotations.store'), $this->payload($customer, [
            'lines' => [
                ['name' => 'Build', 'unit' => 'item', 'quantity' => '2', 'unit_price' => '1000.00', 'discount_percent' => '10', 'vat_rate' => '25'],
                ['name' => 'Hosting', 'unit' => 'year', 'quantity' => '1', 'unit_price' => '2400.00', 'discount_percent' => '0', 'vat_rate' => '25'],
            ],
        ]));

        $quotation = Quotation::firstOrFail();

        // 2 x 1000 - 10% = 1800, plus 2400 = 4200 net; VAT 25% = 1050.
        $this->assertSame('4200.00', $quotation->subtotal);
        $this->assertSame('200.00', $quotation->discount_total);
        $this->assertSame('1050.00', $quotation->vat_total);
        $this->assertSame('5250.00', $quotation->total);
    }

    public function test_vat_is_grouped_by_rate(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = $this->customer($user->company_id);

        $this->actingAs($user)->post(route('quotations.store'), $this->payload($customer, [
            'lines' => [
                ['name' => 'Standard', 'unit' => 'item', 'quantity' => '1', 'unit_price' => '1000.00', 'discount_percent' => '0', 'vat_rate' => '25'],
                ['name' => 'Reduced', 'unit' => 'item', 'quantity' => '1', 'unit_price' => '1000.00', 'discount_percent' => '0', 'vat_rate' => '6'],
            ],
        ]));

        $breakdown = Quotation::firstOrFail()->load('lines')->vatBreakdown();

        $this->assertSame('250.00', $breakdown['25.00']['vat']);
        $this->assertSame('60.00', $breakdown['6.00']['vat']);
    }

    public function test_a_quotation_needs_at_least_one_line(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = $this->customer($user->company_id);

        $this->actingAs($user)
            ->post(route('quotations.store'), $this->payload($customer, ['lines' => []]))
            ->assertSessionHasErrors('lines');
    }

    public function test_valid_until_cannot_precede_the_issue_date(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = $this->customer($user->company_id);

        $this->actingAs($user)
            ->post(route('quotations.store'), $this->payload($customer, [
                'issue_date' => now()->toDateString(),
                'valid_until' => now()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors('valid_until');
    }

    public function test_sending_allocates_a_sequential_number_and_locks_editing(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = $this->customer($user->company_id);

        $this->actingAs($user)->post(route('quotations.store'), $this->payload($customer));
        $quotation = Quotation::firstOrFail();

        $this->actingAs($user)->post(route('quotations.send', $quotation));
        $quotation->refresh();

        $this->assertSame(QuotationStatus::Sent, $quotation->status);
        $this->assertSame('Q0001', $quotation->number);
        $this->assertFalse($quotation->isEditable());

        $this->actingAs($user)
            ->put(route('quotations.update', $quotation), $this->payload($customer))
            ->assertForbidden();
    }

    public function test_numbers_do_not_repeat(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = $this->customer($user->company_id);

        foreach (range(1, 3) as $ignored) {
            $this->actingAs($user)->post(route('quotations.store'), $this->payload($customer));
        }

        Quotation::all()->each(fn (Quotation $q) => $this->actingAs($user)->post(route('quotations.send', $q)));

        $numbers = Quotation::pluck('number')->all();

        $this->assertSame(['Q0001', 'Q0002', 'Q0003'], $numbers);
    }

    public function test_a_service_price_can_be_overridden_on_the_line(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = $this->customer($user->company_id);
        $service = Service::factory()->create(['company_id' => $user->company_id, 'price' => '95000.00']);

        $this->actingAs($user)->post(route('quotations.store'), $this->payload($customer, [
            'lines' => [[
                'service_id' => $service->id,
                'name' => $service->name,
                'unit' => 'item',
                'quantity' => '1',
                'unit_price' => '75000.00',
                'discount_percent' => '0',
                'vat_rate' => '25',
            ]],
        ]));

        $line = Quotation::firstOrFail()->lines()->firstOrFail();

        $this->assertSame('75000.00', $line->unit_price);
        $this->assertSame($service->id, $line->service_id);
    }

    public function test_customer_can_accept_through_the_public_link(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = $this->customer($user->company_id);

        $this->actingAs($user)->post(route('quotations.store'), $this->payload($customer));
        $quotation = Quotation::firstOrFail();
        $this->actingAs($user)->post(route('quotations.send', $quotation));

        $this->post(route('public.quotations.accept', $quotation->fresh()->public_token), [
            'decision_note' => 'Looks good, go ahead.',
        ]);

        $quotation->refresh();

        $this->assertSame(QuotationStatus::Accepted, $quotation->status);
        $this->assertNotNull($quotation->accepted_at);
        $this->assertSame('Looks good, go ahead.', $quotation->decision_note);
    }

    public function test_public_rejection_requires_a_reason(): void
    {
        $quotation = Quotation::factory()->sent()->create(['company_id' => $this->company()->id]);

        $this->post(route('public.quotations.reject', $quotation->public_token), [])
            ->assertSessionHasErrors('decision_note');
    }

    public function test_draft_quotations_are_not_reachable_publicly(): void
    {
        $quotation = Quotation::factory()->create(['company_id' => $this->company()->id]);

        $this->get(route('public.quotations.show', $quotation->public_token))->assertNotFound();
    }

    public function test_expired_quotations_cannot_be_answered(): void
    {
        $quotation = Quotation::factory()->expired()->create(['company_id' => $this->company()->id]);

        $this->post(route('public.quotations.accept', $quotation->public_token))->assertForbidden();
    }

    public function test_support_cannot_see_quotations(): void
    {
        $this->actingAs($this->staff(Role::Support))
            ->get(route('quotations.index'))
            ->assertForbidden();
    }

    public function test_pdf_can_be_downloaded(): void
    {
        $user = $this->staff(Role::Sales);
        $customer = $this->customer($user->company_id);

        $this->actingAs($user)->post(route('quotations.store'), $this->payload($customer));

        $this->actingAs($user)
            ->get(route('quotations.pdf', Quotation::firstOrFail()))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
