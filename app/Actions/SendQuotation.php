<?php

namespace App\Actions;

use App\Enums\QuotationStatus;
use App\Mail\QuotationMail;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

/**
 * Emails a quotation to its recipient and records what was sent, to whom and when.
 * The number is allocated first so the customer and the record always match.
 */
class SendQuotation
{
    public function __construct(private readonly AllocateDocumentNumber $numbers) {}

    public function handle(Quotation $quotation, ?string $message = null): Quotation
    {
        $recipient = $this->recipient($quotation);

        if (! $recipient) {
            throw new RuntimeException(__('quotations.no_recipient'));
        }

        DB::transaction(function () use ($quotation) {
            $quotation->number ??= $this->numbers->handle($quotation->company_id, AllocateDocumentNumber::QUOTATION);
            $quotation->save();
        });

        // Delivery is only recorded once the mailer has accepted the message.
        Mail::to($recipient)->send(new QuotationMail($quotation->fresh(['lines', 'customer', 'contact']), $message));

        $quotation->forceFill([
            'status' => $quotation->status === QuotationStatus::Draft ? QuotationStatus::Sent : $quotation->status,
            'sent_at' => now(),
            'sent_to' => $recipient,
        ])->save();

        return $quotation;
    }

    public function recipient(Quotation $quotation): ?string
    {
        return $quotation->contact?->email
            ?: $quotation->customer->contacts->firstWhere('receives_quotations', true)?->email
            ?: $quotation->customer->primaryContact()?->email
            ?: $quotation->customer->email;
    }
}
