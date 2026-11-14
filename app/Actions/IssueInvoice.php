<?php

namespace App\Actions;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Support\OcrReference;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Issues a draft invoice: allocates the sequential number, stamps the dates and
 * locks the document. After this the invoice is a financial record.
 */
class IssueInvoice
{
    public function __construct(private readonly AllocateDocumentNumber $numbers) {}

    public function handle(Invoice $invoice): Invoice
    {
        if (! $invoice->isEditable()) {
            throw new RuntimeException('Only a draft invoice can be issued.');
        }

        if (! $invoice->lines()->exists()) {
            throw new RuntimeException('An invoice needs at least one line.');
        }

        return DB::transaction(function () use ($invoice) {
            $invoice->number = $this->numbers->handle($invoice->company_id, AllocateDocumentNumber::INVOICE);
            $invoice->ocr_reference = OcrReference::fromNumber($invoice->number);
            $invoice->status = InvoiceStatus::Issued;
            $invoice->issued_at = now();
            $invoice->due_date = $invoice->issue_date->copy()->addDays($invoice->payment_terms_days);
            $invoice->save();

            return $invoice;
        });
    }
}
