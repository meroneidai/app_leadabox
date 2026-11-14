<?php

namespace Tests\Feature;

use App\Actions\CalculateInvoiceTotals;
use App\Actions\IssueInvoice;
use App\Actions\RecordPayment;
use App\Enums\Role;
use App\Mail\InvoiceMail;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Invoice;
use App\Support\OcrReference;
use App\Support\PaymentQrCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoicePaymentQrTest extends TestCase
{
    use RefreshDatabase;

    private function issuedInvoice(array $companyAttributes = []): Invoice
    {
        $admin = $this->staff(Role::Admin);
        $company = Company::query()->first();
        $company->forceFill(array_merge(['bankgiro' => '5555-6666'], $companyAttributes))->save();
        Company::forgetCurrent();

        $customer = Customer::factory()->create(['company_id' => $admin->company_id]);
        Contact::factory()->for($customer)->primary()->create(['email' => 'billing@acme.se']);

        $invoice = new Invoice([
            'customer_id' => $customer->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'payment_terms_days' => 30,
        ]);
        $invoice->company_id = $admin->company_id;
        $invoice->save();

        $invoice->lines()->create([
            'name' => 'Web Development',
            'unit' => 'item',
            'quantity' => 1,
            'unit_price' => 1000,
            'discount_percent' => 0,
            'vat_rate' => 25,
        ]);

        app(CalculateInvoiceTotals::class)->handle($invoice->load('lines'));
        app(IssueInvoice::class)->handle($invoice);

        return $invoice->fresh(['lines', 'customer.contacts', 'contact', 'company']);
    }

    public function test_qr_payload_follows_the_swedish_specification(): void
    {
        $invoice = $this->issuedInvoice();

        $payload = json_decode(PaymentQrCode::payload($invoice), true);

        $this->assertSame(1, $payload['uqr']);
        $this->assertSame(1, $payload['tp']);
        $this->assertSame('BG', $payload['pt']);
        $this->assertSame('5555-6666', $payload['acc']);
        $this->assertEquals(1250.00, $payload['due']);
        $this->assertSame($invoice->ocr_reference, $payload['ref']);
        $this->assertSame($invoice->issue_date->format('Ymd'), $payload['idt']);
    }

    public function test_qr_uses_plusgiro_when_there_is_no_bankgiro(): void
    {
        $invoice = $this->issuedInvoice(['bankgiro' => null, 'plusgiro' => '12 34 56-7']);

        $payload = json_decode(PaymentQrCode::payload($invoice), true);

        $this->assertSame('PG', $payload['pt']);
        $this->assertSame('123456-7', $payload['acc']);
    }

    public function test_qr_amount_reflects_what_is_still_owed(): void
    {
        $invoice = $this->issuedInvoice();

        app(RecordPayment::class)->handle($invoice, [
            'amount' => 250,
            'paid_on' => now()->toDateString(),
            'method' => 'bankgiro',
        ]);

        $payload = json_decode(PaymentQrCode::payload($invoice->fresh(['company'])), true);

        $this->assertEquals(1000.00, $payload['due']);
    }

    public function test_no_qr_without_a_giro_account(): void
    {
        $invoice = $this->issuedInvoice(['bankgiro' => null, 'plusgiro' => null]);

        $this->assertNull(PaymentQrCode::payload($invoice));
        $this->assertNull(PaymentQrCode::dataUri($invoice));
    }

    public function test_qr_renders_as_a_png_data_uri(): void
    {
        $invoice = $this->issuedInvoice();

        $uri = PaymentQrCode::dataUri($invoice);

        $this->assertStringStartsWith('data:image/png;base64,', $uri);
    }

    public function test_ocr_reference_carries_a_valid_luhn_check_digit(): void
    {
        $this->assertTrue(OcrReference::isValid(OcrReference::fromNumber('F0042')));
        $this->assertFalse(OcrReference::isValid('12345'));
    }

    public function test_invoice_can_be_emailed_and_delivery_is_recorded(): void
    {
        Mail::fake();
        $invoice = $this->issuedInvoice();

        $this->actingAs($this->staff(Role::Admin))
            ->post(route('invoices.email', $invoice))
            ->assertRedirect();

        Mail::assertSent(InvoiceMail::class, fn ($mail) => $mail->hasTo('billing@acme.se'));
        $this->assertSame('billing@acme.se', $invoice->fresh()->sent_to);
    }

    public function test_opening_the_public_link_records_the_view(): void
    {
        $invoice = $this->issuedInvoice();

        $this->get(route('public.invoices.show', $invoice->public_token))->assertOk();

        $this->assertNotNull($invoice->fresh()->viewed_at);
    }

    public function test_reminders_only_go_to_overdue_invoices(): void
    {
        Mail::fake();
        $invoice = $this->issuedInvoice();

        $this->artisan('invoices:remind')->assertSuccessful();
        Mail::assertNothingSent();

        $invoice->forceFill(['due_date' => now()->subDays(8)])->save();

        $this->artisan('invoices:remind')->assertSuccessful();
        Mail::assertSent(InvoiceMail::class);

        $this->assertSame(1, $invoice->fresh()->reminder_count);
    }

    public function test_the_same_invoice_is_not_reminded_twice_in_one_day(): void
    {
        Mail::fake();
        $invoice = $this->issuedInvoice();
        $invoice->forceFill(['due_date' => now()->subDays(8)])->save();

        $this->artisan('invoices:remind');
        $this->artisan('invoices:remind');

        // The team alert is a separate mailable, so count the customer email only.
        Mail::assertSent(InvoiceMail::class, 1);
    }
}
