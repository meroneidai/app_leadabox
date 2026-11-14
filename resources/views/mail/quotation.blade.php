@php
    $brand = $company?->name ?? config('app.name');
    $currency = $quotation->currency;
    $contactName = $quotation->contact?->name ?? $quotation->customer->name;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $quotation->reference() }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;color:#0f172a;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        {{ __('quotations.email_preview', ['total' => \App\Support\Money::format((string) $quotation->total, $currency)]) }}
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                       style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#0b0e1f;padding:28px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-right:12px;">
                                        <img src="{{ asset('images/leadabox-mark.svg') }}"
                                             width="40" height="40" alt="{{ $brand }}"
                                             style="display:block;border:0;width:40px;height:40px;">
                                    </td>
                                    <td>
                                        <span style="color:#ffffff;font-size:19px;font-weight:700;letter-spacing:-0.3px;">{{ $brand }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 6px;font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:0.6px;">
                                {{ __('quotations.singular') }} {{ $quotation->reference() }}
                            </p>

                            <h1 style="margin:0 0 16px;font-size:24px;line-height:1.3;font-weight:700;color:#0f172a;">
                                {{ $quotation->title ?: __('quotations.public_intro', ['company' => $brand]) }}
                            </h1>

                            <p style="margin:0 0 20px;font-size:15px;line-height:1.65;color:#475569;">
                                {{ __('quotations.email_greeting', ['name' => $contactName]) }}
                            </p>

                            @if ($customMessage)
                                <p style="margin:0 0 20px;font-size:15px;line-height:1.65;color:#475569;white-space:pre-line;">{{ $customMessage }}</p>
                            @elseif ($quotation->intro)
                                <p style="margin:0 0 20px;font-size:15px;line-height:1.65;color:#475569;white-space:pre-line;">{{ $quotation->intro }}</p>
                            @endif

                            {{-- Highlighted total --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                   style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;margin:0 0 24px;">
                                <tr>
                                    <td style="padding:20px 24px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            @if ((float) $quotation->discount_total > 0)
                                                <tr>
                                                    <td style="font-size:14px;color:#64748b;padding-bottom:6px;">{{ __('quotations.subtotal') }}</td>
                                                    <td align="right" style="font-size:14px;color:#64748b;padding-bottom:6px;">
                                                        {{ \App\Support\Money::format((string) $quotation->subtotal) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size:14px;color:#059669;font-weight:600;padding-bottom:6px;">{{ __('quotations.discount_total') }}</td>
                                                    <td align="right" style="font-size:14px;color:#059669;font-weight:600;padding-bottom:6px;">
                                                        −{{ \App\Support\Money::format((string) $quotation->discount_total) }}
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="font-size:14px;color:#64748b;padding-bottom:10px;">{{ __('quotations.vat_total') }}</td>
                                                <td align="right" style="font-size:14px;color:#64748b;padding-bottom:10px;">
                                                    {{ \App\Support\Money::format((string) $quotation->vat_total) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:16px;font-weight:700;color:#0f172a;border-top:2px solid #0f172a;padding-top:12px;">
                                                    {{ __('quotations.total') }}
                                                </td>
                                                <td align="right" style="font-size:22px;font-weight:700;color:#2d6cff;border-top:2px solid #0f172a;padding-top:12px;">
                                                    {{ \App\Support\Money::format((string) $quotation->total, $currency) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 24px;font-size:14px;color:#64748b;">
                                {{ __('quotations.valid_until') }}: <strong style="color:#0f172a;">{{ $quotation->valid_until->isoFormat('D MMMM YYYY') }}</strong>
                            </p>

                            {{-- CTA --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 16px;">
                                <tr>
                                    <td style="background:#2d6cff;border-radius:10px;">
                                        <a href="{{ $link }}"
                                           style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;">
                                            {{ __('quotations.email_cta') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 4px;font-size:13px;line-height:1.6;color:#64748b;">
                                {{ __('quotations.email_cta_hint') }}
                            </p>
                            <p style="margin:0;font-size:12px;line-height:1.6;color:#94a3b8;word-break:break-all;">
                                <a href="{{ $link }}" style="color:#2d6cff;text-decoration:none;">{{ $link }}</a>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 32px;">
                            <p style="margin:0 0 4px;font-size:12px;line-height:1.6;color:#64748b;">
                                <strong style="color:#0f172a;">{{ $company?->legal_name ?? $brand }}</strong>
                                @if ($company?->org_number) · {{ $company->org_number }} @endif
                            </p>
                            <p style="margin:0;font-size:12px;line-height:1.6;color:#94a3b8;">
                                @if ($company?->email){{ $company->email }}@endif
                                @if ($company?->phone) · {{ $company->phone }} @endif
                                @if ($company?->website) · {{ $company->website }} @endif
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
