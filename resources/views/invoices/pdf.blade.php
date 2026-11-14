@php
    $currency = $invoice->currency;
    $isCredit = $invoice->isCreditNote();
    $partlyPaid = (float) $invoice->amount_paid > 0;
    $hasDiscount = (float) $invoice->discount_total > 0;
    $qr = ($company?->payment_qr_enabled && ! $isCredit)
        ? \App\Support\PaymentQrCode::dataUri($invoice, 150)
        : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->reference() }}</title>
    <style>
        @page { margin: 18mm 16mm 20mm; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            line-height: 1.55;
            color: #15181f;
            margin: 0;
        }

        .muted { color: #5c6373; }
        .right { text-align: right; }

        .eyebrow {
            font-size: 8px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #8d94a3;
        }

        .doctype {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2.5px;
            text-transform: uppercase;
        }

        .rule { border-bottom: 1px solid #e4e7ec; }

        h1.subject { font-size: 15px; font-weight: bold; margin: 0 0 4px; }

        table.lines { width: 100%; border-collapse: collapse; }
        table.lines th {
            text-align: left;
            padding: 0 8px 8px;
            font-size: 8.5px;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #15181f;
            border-bottom: 1.5px solid #15181f;
        }
        table.lines td {
            padding: 11px 8px;
            border-bottom: 1px solid #edeff3;
            vertical-align: top;
        }
        table.lines td:first-child, table.lines th:first-child { padding-left: 0; }
        table.lines td:last-child, table.lines th:last-child { padding-right: 0; }

        .totals td { padding: 5px 0; font-size: 10.5px; }
        .totals .grand td {
            border-top: 1.5px solid #15181f;
            padding-top: 10px;
            font-size: 14px;
            font-weight: bold;
        }

        .note {
            background: #fafbfc;
            border: 1px solid #ecEEF2;
            border-radius: 4px;
            padding: 10px 12px;
        }

        .discount { color: #5c6373; font-size: 9.5px; }

        .footer {
            position: fixed;
            bottom: -12mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            line-height: 1.7;
            color: #8d94a3;
        }
    </style>
</head>
<body>

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="60%" style="vertical-align: top;">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding-right: 9px; vertical-align: middle;">
                            <img src="{{ public_path('images/leadabox-mark.png') }}" width="30" height="30" alt="">
                        </td>
                        <td style="vertical-align: middle;">
                            <span style="font-size: 14px; font-weight: bold;">{{ $company?->name }}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="40%" class="right" style="vertical-align: top;">
                <div class="doctype">{{ $isCredit ? __('invoices.credit_note') : __('invoices.singular') }}</div>
                <div class="muted" style="font-size: 10.5px;">{{ $invoice->reference() }}</div>
            </td>
        </tr>
    </table>

    <div class="rule" style="margin: 14px 0 16px;"></div>

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="44%" style="vertical-align: top;">
                <div class="eyebrow" style="margin-bottom: 4px;">{{ __('quotations.prepared_for') }}</div>
                <strong>{{ $invoice->customer->name }}</strong><br>
                @if ($invoice->contact){{ $invoice->contact->name }}<br>@endif
                <span class="muted">
                    @foreach ($invoice->customer->addressLines() as $line)
                        {{ $line }}<br>
                    @endforeach
                    @if ($invoice->customer->org_number)
                        {{ $invoice->customer->org_number }}
                    @endif
                </span>
            </td>

            <td width="22%"></td>

            <td width="34%" style="vertical-align: top;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="muted" style="padding: 1px 0;">{{ __('invoices.issue_date') }}</td>
                        <td class="right" style="padding: 1px 0;">{{ $invoice->issue_date->isoFormat('D MMM YYYY') }}</td>
                    </tr>
                    <tr>
                        <td class="muted" style="padding: 1px 0;">{{ __('invoices.due_date') }}</td>
                        <td class="right" style="padding: 1px 0;"><strong>{{ $invoice->due_date->isoFormat('D MMM YYYY') }}</strong></td>
                    </tr>
                    @if ($invoice->ocr_reference)
                        <tr>
                            <td class="muted" style="padding: 1px 0;">{{ __('invoices.ocr_reference') }}</td>
                            <td class="right" style="padding: 1px 0;">{{ $invoice->ocr_reference }}</td>
                        </tr>
                    @endif
                    @if ($invoice->customer_reference)
                        <tr>
                            <td class="muted" style="padding: 1px 0;">{{ __('invoices.customer_reference') }}</td>
                            <td class="right" style="padding: 1px 0;">{{ $invoice->customer_reference }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    @if ($invoice->title || $invoice->intro)
        <div style="margin-top: 20px;">
            @if ($invoice->title)
                <h1 class="subject">{{ $invoice->title }}</h1>
            @endif
            @if ($invoice->intro)
                <div class="muted">{!! nl2br(e($invoice->intro)) !!}</div>
            @endif
        </div>
    @endif

    <table class="lines" style="margin-top: 20px;">
        <thead>
            <tr>
                <th style="width: 48%;">{{ __('invoices.line_name') }}</th>
                <th class="right" style="width: 11%;">{{ __('invoices.line_qty') }}</th>
                <th class="right" style="width: 15%;">{{ __('invoices.line_price') }}</th>
                <th class="right" style="width: 8%;">{{ __('invoices.line_vat') }}</th>
                <th class="right" style="width: 18%;">{{ __('invoices.line_total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->lines as $line)
                <tr>
                    <td>
                        <strong>{{ $line->name }}</strong>
                        @if ($line->description)
                            <div class="muted" style="font-size: 9.5px;">{!! nl2br(e($line->description)) !!}</div>
                        @endif
                        @if ((float) $line->discount_percent > 0)
                            <div class="discount">
                                {{ __('invoices.discount_total') }} –{{ rtrim(rtrim((string) $line->discount_percent, '0'), '.') }}%
                                ({{ \App\Support\Money::format((string) $line->line_discount) }})
                            </div>
                        @endif
                    </td>
                    <td class="right">{{ rtrim(rtrim((string) $line->quantity, '0'), '.') }} {{ __('services.unit_'.$line->unit) }}</td>
                    <td class="right">{{ \App\Support\Money::format((string) $line->unit_price) }}</td>
                    <td class="right">{{ rtrim(rtrim((string) $line->vat_rate, '0'), '.') }}%</td>
                    <td class="right">{{ \App\Support\Money::format((string) $line->line_subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 18px;">
        <tr>
            <td width="52%" style="vertical-align: top;">
                @if ($company?->bankgiro || $company?->plusgiro || $company?->iban || $invoice->ocr_reference)
                    <table width="100%" cellpadding="0" cellspacing="0" class="note">
                        <tr>
                            <td style="vertical-align: top;">
                                <div class="eyebrow" style="margin-bottom: 3px;">{{ __('invoices.pay_to') }}</div>
                                @if ($company?->bankgiro)
                                    {{ __('invoices.bankgiro') }} <strong>{{ $company->bankgiro }}</strong><br>
                                @endif
                                @if ($company?->plusgiro)
                                    {{ __('invoices.plusgiro') }} <strong>{{ $company->plusgiro }}</strong><br>
                                @endif
                                @if ($company?->iban)
                                    <span class="muted">{{ __('invoices.iban') }} {{ $company->iban }}@if ($company?->bic) · {{ $company->bic }}@endif</span><br>
                                @endif
                                @if ($invoice->ocr_reference)
                                    {{ __('invoices.ocr_reference') }} <strong>{{ $invoice->ocr_reference }}</strong>
                                @endif
                            </td>

                            @if ($qr)
                                <td width="96" style="vertical-align: top; text-align: center; padding-left: 10px;">
                                    <img src="{{ $qr }}" width="86" height="86" alt="">
                                    <div style="font-size: 7px; color: #8d94a3; line-height: 1.3; margin-top: 3px;">
                                        {{ __('invoices.qr_hint') }}
                                    </div>
                                </td>
                            @endif
                        </tr>
                    </table>
                @endif

                @if ($invoice->customer->reverse_charge)
                    <div class="muted" style="margin-top: 7px; font-size: 8px;">{{ __('customers.reverse_charge') }}</div>
                @endif
            </td>
            <td width="6%"></td>
            <td width="42%" style="vertical-align: top;">
                <table width="100%" cellpadding="0" cellspacing="0" class="totals">
                    <tr>
                        <td class="muted">{{ __('invoices.subtotal') }}</td>
                        <td class="right">{{ \App\Support\Money::format((string) $invoice->subtotal) }}</td>
                    </tr>

                    @if ($hasDiscount)
                        <tr>
                            <td class="muted">{{ __('invoices.discount_total') }}</td>
                            <td class="right">–{{ \App\Support\Money::format((string) $invoice->discount_total) }}</td>
                        </tr>
                    @endif

                    @foreach ($invoice->vatBreakdown() as $vat)
                        <tr>
                            <td class="muted">{{ __('invoices.vat_at_rate', ['rate' => rtrim(rtrim($vat['rate'], '0'), '.')]) }}</td>
                            <td class="right">{{ \App\Support\Money::format($vat['vat']) }}</td>
                        </tr>
                    @endforeach

                    @if ($partlyPaid)
                        <tr>
                            <td class="muted">{{ __('invoices.total') }}</td>
                            <td class="right">{{ \App\Support\Money::format((string) $invoice->total) }}</td>
                        </tr>
                        <tr>
                            <td class="muted">{{ __('invoices.amount_paid') }}</td>
                            <td class="right">–{{ \App\Support\Money::format((string) $invoice->amount_paid) }}</td>
                        </tr>
                    @endif

                    <tr class="grand">
                        <td>{{ $partlyPaid ? __('invoices.amount_due') : __('invoices.total') }}</td>
                        <td class="right">{{ \App\Support\Money::format($invoice->amountDue(), $currency) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if ($invoice->terms)
        <div class="note" style="margin-top: 20px;">
            <div class="eyebrow" style="margin-bottom: 3px;">{{ __('invoices.terms') }}</div>
            <span class="muted">{!! nl2br(e($invoice->terms)) !!}</span>
        </div>
    @endif

    <div class="footer">
        {{ $company?->legal_name ?? $company?->name }}
        @if ($company?->address_line1) · {{ $company->address_line1 }}, {{ $company->postal_code }} {{ $company->city }} @endif
        <br>
        @if ($company?->org_number) {{ __('settings.org_number') }} {{ $company->org_number }} @endif
        @if ($company?->vat_number) · {{ __('settings.vat_number') }} {{ $company->vat_number }} @endif
        @if ($company?->has_f_skatt) · {{ __('settings.has_f_skatt') }} @endif
        @if ($company?->invoice_footer) <br>{{ $company->invoice_footer }} @endif
    </div>
</body>
</html>
