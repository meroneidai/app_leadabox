<?php

namespace App\Actions;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Books a payment against an invoice and moves the status to partially or fully paid.
 * Safe to retry: a payment carrying an external id is only ever booked once.
 */
class RecordPayment
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Invoice $invoice, array $data, ?int $userId = null): Payment
    {
        if (! $invoice->status->acceptsPayment()) {
            throw new RuntimeException('This invoice cannot accept payments.');
        }

        return DB::transaction(function () use ($invoice, $data, $userId) {
            $externalId = $data['external_id'] ?? null;

            if ($externalId) {
                $existing = Payment::where('external_id', $externalId)->first();

                if ($existing) {
                    return $existing;
                }
            }

            $payment = $invoice->payments()->create([
                ...$data,
                'recorded_by' => $userId,
            ]);

            $this->refreshInvoice($invoice->fresh('payments'));

            return $payment;
        });
    }

    public function refreshInvoice(Invoice $invoice): Invoice
    {
        $paid = $invoice->payments->reduce(
            fn (string $carry, Payment $payment) => Money::add($carry, (string) $payment->amount),
            Money::zero()
        );

        $fullyPaid = bccomp($paid, (string) $invoice->total, Money::SCALE) >= 0;

        $invoice->forceFill([
            'amount_paid' => $paid,
            'status' => match (true) {
                $fullyPaid => InvoiceStatus::Paid,
                Money::isZero($paid) => InvoiceStatus::Issued,
                default => InvoiceStatus::PartiallyPaid,
            },
            'paid_at' => $fullyPaid ? ($invoice->paid_at ?? now()) : null,
        ])->save();

        return $invoice;
    }
}
