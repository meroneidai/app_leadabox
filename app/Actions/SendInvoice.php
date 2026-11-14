<?php

namespace App\Actions;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

/**
 * Emails an invoice with its PDF and records who it went to.
 */
class SendInvoice
{
    public function handle(Invoice $invoice, ?string $message = null, bool $isReminder = false): Invoice
    {
        $recipient = $this->recipient($invoice);

        if (! $recipient) {
            throw new RuntimeException(__('invoices.no_recipient'));
        }

        Mail::to($recipient)->send(new InvoiceMail($invoice, $message, $isReminder));

        $invoice->forceFill(array_filter([
            'sent_at' => now(),
            'sent_to' => $recipient,
            'last_reminder_at' => $isReminder ? now() : null,
        ], fn ($value) => $value !== null))->save();

        if ($isReminder) {
            $invoice->increment('reminder_count');
        }

        return $invoice;
    }

    public function recipient(Invoice $invoice): ?string
    {
        return $invoice->contact?->email
            ?: $invoice->customer->contacts->firstWhere('receives_invoices', true)?->email
            ?: $invoice->customer->primaryContact()?->email
            ?: $invoice->customer->email;
    }
}
