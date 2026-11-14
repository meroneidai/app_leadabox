@php
    $currency = $quotation->currency;
    $hasDiscount = (float) $quotation->discount_total > 0;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $quotation->reference() }}</title>
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
            color: #15181f;
        }

        .rule { border-bottom: 1px solid #e4e7ec; }

        h1.subject {
            font-size: 15px;
            font-weight: bold;
            margin: 0 0 4px;
            color: #15181f;
        }

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

        .discount { color: #5c6373; font-size: 9.5px; }

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

    {{-- Masthead --}}
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
                <div class="doctype">{{ __('quotations.singular') }}</div>
                <div class="muted" style="font-size: 10.5px;">{{ $quotation->reference() }}</div>
            </td>
        </tr>
    </table>

    <div class="rule" style="margin: 14px 0 16px;"></div>

    {{-- Parties and dates --}}
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="44%" style="vertical-align: top;">
                <div class="eyebrow" style="margin-bottom: 4px;">{{ __('quotations.prepared_for') }}</div>
                <strong>{{ $quotation->customer->name }}</strong><br>
                @if ($quotation->contact)
                    {{ $quotation->contact->name }}<br>
                @endif
                <span class="muted">
                    @foreach ($quotation->customer->addressLines() as $line)
                        {{ $line }}<br>
                    @endforeach
                    @if ($quotation->customer->org_number)
                        {{ $quotation->customer->org_number }}
                    @endif
                </span>
            </td>

            <td width="22%"></td>

            <td width="34%" style="vertical-align: top;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="muted" style="padding: 1px 0;">{{ __('quotations.issue_date') }}</td>
                        <td class="right" style="padding: 1px 0;">{{ $quotation->issue_date->isoFormat('D MMM YYYY') }}</td>
                    </tr>
                    <tr>
                        <td class="muted" style="padding: 1px 0;">{{ __('quotations.valid_until') }}</td>
                        <td class="right" style="padding: 1px 0;"><strong>{{ $quotation->valid_until->isoFormat('D MMM YYYY') }}</strong></td>
                    </tr>
                    <tr>
                        <td class="muted" style="padding: 1px 0;">{{ __('quotations.our_reference') }}</td>
                        <td class="right" style="padding: 1px 0;">{{ $quotation->creator?->name ?? $company?->name }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Subject --}}
    @if ($quotation->title || $quotation->intro)
        <div style="margin-top: 20px;">
            @if ($quotation->title)
                <h1 class="subject">{{ $quotation->title }}</h1>
            @endif
            @if ($quotation->intro)
                <div class="muted">{!! nl2br(e($quotation->intro)) !!}</div>
            @endif
        </div>
    @endif

    {{-- Lines --}}
    <table class="lines" style="margin-top: 20px;">
        <thead>
            <tr>
                <th style="width: 48%;">{{ __('quotations.line_name') }}</th>
                <th class="right" style="width: 11%;">{{ __('quotations.line_qty') }}</th>
                <th class="right" style="width: 15%;">{{ __('quotations.line_price') }}</th>
                <th class="right" style="width: 8%;">{{ __('quotations.line_vat') }}</th>
                <th class="right" style="width: 18%;">{{ __('quotations.line_total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->lines as $line)
                <tr>
                    <td>
                        <strong>{{ $line->name }}</strong>
                        @if ($line->description)
                            <div class="muted" style="font-size: 9.5px;">{!! nl2br(e($line->description)) !!}</div>
                        @endif
                        @if ((float) $line->discount_percent > 0)
                            <div class="discount">
                                {{ __('quotations.discount_total') }} –{{ rtrim(rtrim((string) $line->discount_percent, '0'), '.') }}%
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

    {{-- Totals --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 18px;">
        <tr>
            <td width="52%" style="vertical-align: top;">
                @if ($quotation->customer->reverse_charge)
                    <div class="muted" style="font-size: 8px;">{{ __('customers.reverse_charge') }}</div>
                @endif
            </td>
            <td width="6%"></td>
            <td width="42%" style="vertical-align: top;">
                <table width="100%" cellpadding="0" cellspacing="0" class="totals">
                    <tr>
                        <td class="muted">{{ __('quotations.subtotal') }}</td>
                        <td class="right">{{ \App\Support\Money::format((string) $quotation->subtotal) }}</td>
                    </tr>

                    @if ($hasDiscount)
                        <tr>
                            <td class="muted">{{ __('quotations.discount_total') }}</td>
                            <td class="right">–{{ \App\Support\Money::format((string) $quotation->discount_total) }}</td>
                        </tr>
                    @endif

                    @foreach ($quotation->vatBreakdown() as $vat)
                        <tr>
                            <td class="muted">{{ __('quotations.vat_at_rate', ['rate' => rtrim(rtrim($vat['rate'], '0'), '.')]) }}</td>
                            <td class="right">{{ \App\Support\Money::format($vat['vat']) }}</td>
                        </tr>
                    @endforeach

                    <tr class="grand">
                        <td>{{ __('quotations.total') }}</td>
                        <td class="right">{{ \App\Support\Money::format((string) $quotation->total, $currency) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if ($quotation->terms)
        <div class="note" style="margin-top: 20px;">
            <div class="eyebrow" style="margin-bottom: 3px;">{{ __('quotations.terms') }}</div>
            <span class="muted">{!! nl2br(e($quotation->terms)) !!}</span>
        </div>
    @endif

    <div class="footer">
        {{ $company?->legal_name ?? $company?->name }}
        @if ($company?->address_line1) · {{ $company->address_line1 }}, {{ $company->postal_code }} {{ $company->city }} @endif
        <br>
        @if ($company?->org_number) {{ __('settings.org_number') }} {{ $company->org_number }} @endif
        @if ($company?->vat_number) · {{ __('settings.vat_number') }} {{ $company->vat_number }} @endif
        @if ($company?->has_f_skatt) · {{ __('settings.has_f_skatt') }} @endif
        @if ($company?->email) · {{ $company->email }} @endif
    </div>
</body>
</html>
