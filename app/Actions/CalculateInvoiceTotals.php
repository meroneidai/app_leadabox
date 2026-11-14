<?php

namespace App\Actions;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Support\Money;

/**
 * Recalculates every line and the document totals. Browser values are never trusted.
 */
class CalculateInvoiceTotals
{
    public function handle(Invoice $invoice): Invoice
    {
        $subtotal = Money::zero();
        $discountTotal = Money::zero();
        $vatTotal = Money::zero();

        foreach ($invoice->lines as $line) {
            $this->calculateLine($line);

            $subtotal = Money::add($subtotal, (string) $line->line_subtotal);
            $discountTotal = Money::add($discountTotal, (string) $line->line_discount);
            $vatTotal = Money::add($vatTotal, (string) $line->line_vat);
        }

        $invoice->forceFill([
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'vat_total' => $vatTotal,
            'total' => Money::add($subtotal, $vatTotal),
        ])->save();

        return $invoice;
    }

    public function calculateLine(InvoiceLine $line): InvoiceLine
    {
        $gross = Money::mul((string) $line->quantity, (string) $line->unit_price);
        $discount = Money::percentOf($gross, (string) $line->discount_percent);
        $net = Money::sub($gross, $discount);
        $vat = Money::percentOf($net, (string) $line->vat_rate);

        $line->forceFill([
            'line_subtotal' => $net,
            'line_discount' => $discount,
            'line_vat' => $vat,
            'line_total' => Money::add($net, $vat),
        ])->save();

        return $line;
    }
}
