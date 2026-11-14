<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Invoice;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Swedish invoice payment QR code.
 *
 * Follows the "uqr" payload used by Swedish banking apps (qrkod.info), so the
 * customer can scan the invoice and get amount, OCR and account pre-filled.
 */
final class PaymentQrCode
{
    /** Returns the scannable payload, or null when the company has no giro account. */
    public static function payload(Invoice $invoice): ?string
    {
        $company = $invoice->company ?? Company::current();

        [$type, $account] = match (true) {
            filled($company?->bankgiro) => ['BG', $company->bankgiro],
            filled($company?->plusgiro ?? null) => ['PG', $company->plusgiro],
            default => [null, null],
        };

        if (! $account || ! $invoice->number) {
            return null;
        }

        $reference = preg_replace('/\D/', '', (string) $invoice->ocr_reference) ?: '';

        $data = array_filter([
            'uqr' => 1,
            'tp' => 1,
            'nme' => $company->legal_name ?: $company->name,
            'cid' => (string) $invoice->customer_id,
            'iref' => $reference,
            'idt' => $invoice->issue_date?->format('Ymd'),
            'ddt' => $invoice->due_date?->format('Ymd'),
            // Banking apps expect the amount still outstanding, not the original total.
            'due' => (float) $invoice->amountDue(),
            'pt' => $type,
            'acc' => preg_replace('/\s/', '', (string) $account),
            'ref' => $reference,
        ], fn ($value) => $value !== null && $value !== '');

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /** PNG data URI, which dompdf embeds reliably. */
    public static function dataUri(Invoice $invoice, int $size = 150): ?string
    {
        $payload = self::payload($invoice);

        if (! $payload) {
            return null;
        }

        $result = (new Builder(
            writer: new PngWriter,
            data: $payload,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 0,
        ))->build();

        return $result->getDataUri();
    }
}
