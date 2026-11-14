@php
    $brand = $company?->name ?? config('app.name');
    $contactName = $invoice->contact?->name ?? $invoice->customer->name;
    $overdue = $invoice->isOverdue();
@endphp

<x-mail-layout :company="$company" :preview="__('invoices.email_preview', ['amount' => \App\Support\Money::format($invoice->amountDue(), $invoice->currency)])">
    <p style="margin:0 0 6px;font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:0.6px;">
        {{ __('invoices.singular') }} {{ $invoice->reference() }}
    </p>

    <h1 style="margin:0 0 16px;font-size:24px;line-height:1.3;font-weight:700;color:#0f172a;">
        {{ $isReminder ? __('invoices.reminder_title') : __('invoices.email_title', ['company' => $brand]) }}
    </h1>

    <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#475569;">
        {{ __('invoices.email_greeting', ['name' => $contactName]) }}
    </p>

    @if ($customMessage)
        <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#475569;white-space:pre-line;">{{ $customMessage }}</p>
    @elseif ($isReminder)
        <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#475569;">
            {{ __('invoices.reminder_body', ['days' => $invoice->daysOverdue()]) }}
        </p>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:{{ $overdue ? '#fef2f2' : '#f8fafc' }};border:1px solid {{ $overdue ? '#fecaca' : '#e2e8f0' }};border-radius:12px;margin:0 0 24px;">
        <tr>
            <td style="padding:20px 24px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="font-size:14px;color:#64748b;padding-bottom:8px;">{{ __('invoices.due_date') }}</td>
                        <td align="right" style="font-size:14px;font-weight:600;color:{{ $overdue ? '#b91c1c' : '#0f172a' }};padding-bottom:8px;">
                            {{ $invoice->due_date->isoFormat('D MMMM YYYY') }}
                        </td>
                    </tr>
                    @if ($invoice->ocr_reference)
                        <tr>
                            <td style="font-size:14px;color:#64748b;padding-bottom:8px;">{{ __('invoices.ocr_reference') }}</td>
                            <td align="right" style="font-size:14px;font-weight:600;color:#0f172a;padding-bottom:8px;">{{ $invoice->ocr_reference }}</td>
                        </tr>
                    @endif
                    @if ($company?->bankgiro)
                        <tr>
                            <td style="font-size:14px;color:#64748b;padding-bottom:8px;">{{ __('invoices.bankgiro') }}</td>
                            <td align="right" style="font-size:14px;font-weight:600;color:#0f172a;padding-bottom:8px;">{{ $company->bankgiro }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="font-size:16px;font-weight:700;color:#0f172a;border-top:2px solid #0f172a;padding-top:12px;">
                            {{ __('invoices.amount_due') }}
                        </td>
                        <td align="right" style="font-size:22px;font-weight:700;color:{{ $overdue ? '#b91c1c' : '#2d6cff' }};border-top:2px solid #0f172a;padding-top:12px;">
                            {{ \App\Support\Money::format($invoice->amountDue(), $invoice->currency) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 16px;">
        <tr>
            <td style="background:#2d6cff;border-radius:10px;">
                <a href="{{ $link }}" style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;">
                    {{ __('invoices.email_cta') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 4px;font-size:13px;line-height:1.6;color:#64748b;">{{ __('invoices.email_cta_hint') }}</p>
    <p style="margin:0;font-size:12px;line-height:1.6;color:#94a3b8;word-break:break-all;">
        <a href="{{ $link }}" style="color:#2d6cff;text-decoration:none;">{{ $link }}</a>
    </p>
</x-mail-layout>
