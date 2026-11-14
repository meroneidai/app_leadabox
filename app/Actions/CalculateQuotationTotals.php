<?php

namespace App\Actions;

use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Support\Money;

/**
 * Recalculates every line and the document totals from the stored inputs.
 * Totals are never trusted from the browser.
 */
class CalculateQuotationTotals
{
    public function handle(Quotation $quotation): Quotation
    {
        $subtotal = Money::zero();
        $discountTotal = Money::zero();
        $vatTotal = Money::zero();

        foreach ($quotation->lines as $line) {
            $this->calculateLine($line);

            $subtotal = Money::add($subtotal, (string) $line->line_subtotal);
            $discountTotal = Money::add($discountTotal, (string) $line->line_discount);
            $vatTotal = Money::add($vatTotal, (string) $line->line_vat);
        }

        $quotation->forceFill([
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'vat_total' => $vatTotal,
            'total' => Money::add($subtotal, $vatTotal),
        ])->save();

        return $quotation;
    }

    public function calculateLine(QuotationLine $line): QuotationLine
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
