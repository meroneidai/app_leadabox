<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySettingsTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        $company = $this->company();

        return [
            'name' => 'Leadabox',
            'legal_name' => 'Leadabox AB',
            'org_number' => '556677-8899',
            'vat_number' => 'SE556677889901',
            'has_f_skatt' => true,
            'country' => 'SE',
            'currency' => 'SEK',
            'locale' => 'en',
            'default_vat_rate' => 25,
            'payment_terms_days' => 30,
            'quotation_prefix' => 'Q',
            'quotation_next_number' => $company->quotation_next_number,
            'quotation_valid_days' => 30,
            'invoice_prefix' => 'F',
            'invoice_next_number' => $company->invoice_next_number,
            ...$overrides,
        ];
    }

    public function test_admin_can_update_company_settings(): void
    {
        $admin = $this->staff(Role::Admin);

        $this->actingAs($admin)
            ->put(route('settings.company.update'), $this->payload(['name' => 'Leadabox Group']))
            ->assertRedirect(route('settings.company.edit'));

        $this->assertSame('Leadabox Group', Company::query()->first()->name);
    }

    public function test_non_admin_cannot_open_settings(): void
    {
        $this->actingAs($this->staff(Role::Manager))
            ->get(route('settings.company.edit'))
            ->assertForbidden();
    }

    public function test_invoice_numbering_cannot_move_backwards(): void
    {
        $admin = $this->staff(Role::Admin);
        $company = $this->company();
        $company->update(['invoice_next_number' => 50]);

        $this->actingAs($admin)
            ->put(route('settings.company.update'), $this->payload([
                'invoice_next_number' => 10,
                'quotation_next_number' => $company->quotation_next_number,
            ]))
            ->assertSessionHasErrors('invoice_next_number');

        $this->assertSame(50, Company::query()->first()->invoice_next_number);
    }

    public function test_vat_rate_must_be_within_range(): void
    {
        $this->actingAs($this->staff(Role::Admin))
            ->put(route('settings.company.update'), $this->payload(['default_vat_rate' => 150]))
            ->assertSessionHasErrors('default_vat_rate');
    }
}
