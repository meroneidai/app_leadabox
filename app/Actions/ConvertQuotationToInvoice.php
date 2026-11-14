<?php

namespace App\Actions;

use App\Enums\QuotationStatus;
use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Turns an accepted quotation into a draft invoice, copying the agreed lines.
 */
class ConvertQuotationToInvoice
{
    public function __construct(private readonly CalculateInvoiceTotals $totals) {}

    public function handle(Quotation $quotation, ?int $userId = null): Invoice
    {
        if ($quotation->status !== QuotationStatus::Accepted) {
            throw new RuntimeException('Only an accepted quotation can be invoiced.');
        }

        return DB::transaction(function () use ($quotation, $userId) {
            $terms = $quotation->customer->paymentTermsDays();

            $invoice = new Invoice([
                'customer_id' => $quotation->customer_id,
                'contact_id' => $quotation->contact_id,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays($terms)->toDateString(),
                'payment_terms_days' => $terms,
                'title' => $quotation->title,
                'intro' => $quotation->intro,
                'terms' => $quotation->terms,
                'currency' => $quotation->currency,
            ]);

            $invoice->company_id = $quotation->company_id;
            $invoice->quotation_id = $quotation->id;
            $invoice->created_by = $userId;
            $invoice->save();

            foreach ($quotation->lines as $line) {
                $invoice->lines()->create([
                    'service_id' => $line->service_id,
                    'name' => $line->name,
                    'description' => $line->description,
                    'unit' => $line->unit,
                    'quantity' => $line->quantity,
                    'unit_price' => $line->unit_price,
                    'discount_percent' => $line->discount_percent,
                    'vat_rate' => $line->vat_rate,
                    'sort_order' => $line->sort_order,
                ]);
            }

            return $this->totals->handle($invoice->load('lines'));
        });
    }
}
