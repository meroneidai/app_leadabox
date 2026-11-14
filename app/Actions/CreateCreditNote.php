<?php

namespace App\Actions;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Issued invoices are never edited or deleted. A mistake is corrected by issuing a
 * credit note that mirrors the original with negative amounts.
 */
class CreateCreditNote
{
    public function __construct(
        private readonly CalculateInvoiceTotals $totals,
        private readonly IssueInvoice $issuer,
    ) {}

    public function handle(Invoice $invoice, ?int $userId = null): Invoice
    {
        if (! $invoice->status->isIssued() || $invoice->isCreditNote()) {
            throw new RuntimeException('Only an issued invoice can be credited.');
        }

        return DB::transaction(function () use ($invoice, $userId) {
            $credit = $invoice->replicate([
                'number', 'ocr_reference', 'status', 'issued_at', 'paid_at',
                'amount_paid', 'public_token', 'subtotal', 'discount_total', 'vat_total', 'total',
            ]);

            $credit->forceFill([
                'type' => Invoice::TYPE_CREDIT_NOTE,
                'status' => InvoiceStatus::Draft,
                'credits_invoice_id' => $invoice->id,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->toDateString(),
                'amount_paid' => 0,
                'created_by' => $userId,
                'title' => __('invoices.credit_of', ['number' => $invoice->number]),
            ])->save();

            foreach ($invoice->lines as $line) {
                $credit->lines()->create([
                    'service_id' => $line->service_id,
                    'name' => $line->name,
                    'description' => $line->description,
                    'unit' => $line->unit,
                    'quantity' => '-'.$line->quantity,
                    'unit_price' => $line->unit_price,
                    'discount_percent' => $line->discount_percent,
                    'vat_rate' => $line->vat_rate,
                    'sort_order' => $line->sort_order,
                ]);
            }

            $this->totals->handle($credit->load('lines'));
            $this->issuer->handle($credit);

            $invoice->forceFill(['status' => InvoiceStatus::Credited])->save();

            return $credit;
        });
    }
}
