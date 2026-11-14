@php
    $brand = $company?->name ?? config('app.name');
    $contactName = $quotation->contact?->name ?? $quotation->customer->name;
@endphp

<x-mail-layout :company="$company" :preview="__('quotations.accepted_email_preview')">
    <p style="margin:0 0 6px;font-size:13px;color:#059669;text-transform:uppercase;letter-spacing:0.6px;font-weight:600;">
        {{ __('quotations.status_accepted') }}
    </p>

    <h1 style="margin:0 0 16px;font-size:24px;line-height:1.3;font-weight:700;color:#0f172a;">
        {{ __('quotations.accepted_email_title', ['name' => $contactName]) }}
    </h1>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#475569;">
        {{ __('quotations.accepted_email_intro', ['company' => $brand, 'number' => $quotation->reference()]) }}
    </p>

    <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#475569;">
        {{ __('quotations.accepted_email_body') }}
    </p>

    {{-- What happens next --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;margin:0 0 24px;">
        <tr>
            <td style="padding:20px 24px;">
                <p style="margin:0 0 14px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#64748b;">
                    {{ __('quotations.next_steps') }}
                </p>

                @foreach ([
                    __('quotations.next_step_1'),
                    __('quotations.next_step_2'),
                    __('quotations.next_step_3'),
                ] as $index => $step)
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
                        <tr>
                            <td width="26" style="vertical-align:top;">
                                <span style="display:inline-block;width:20px;height:20px;border-radius:50%;background:#2d6cff;color:#ffffff;font-size:11px;font-weight:700;text-align:center;line-height:20px;">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td style="font-size:14px;line-height:1.6;color:#475569;">{{ $step }}</td>
                        </tr>
                    </table>
                @endforeach
            </td>
        </tr>
    </table>

    @if ($projectLink)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
               style="border:1px solid #e2e8f0;border-radius:12px;margin:0 0 20px;">
            <tr>
                <td style="padding:18px 22px;">
                    <p style="margin:0 0 4px;font-size:12px;text-transform:uppercase;letter-spacing:0.8px;color:#64748b;font-weight:600;">
                        {{ __('projects.singular_label') }}
                    </p>
                    <p style="margin:0;font-size:17px;font-weight:700;color:#0f172a;">{{ $project->name }}</p>
                </td>
            </tr>
        </table>

        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 14px;">
            <tr>
                <td style="background:#2d6cff;border-radius:10px;">
                    <a href="{{ $projectLink }}"
                       style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;">
                        {{ __('quotations.follow_project') }}
                    </a>
                </td>
            </tr>
        </table>

        <p style="margin:0 0 24px;font-size:13px;line-height:1.6;color:#64748b;">
            {{ __('quotations.follow_project_hint') }}
        </p>
    @endif

    {{-- How to reach us --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;margin:0 0 24px;">
        <tr>
            <td style="padding:18px 22px;">
                <p style="margin:0 0 8px;font-size:15px;font-weight:600;color:#0f172a;">
                    {{ __('quotations.contact_us_title') }}
                </p>
                <p style="margin:0 0 12px;font-size:14px;line-height:1.65;color:#475569;">
                    {{ __('quotations.contact_us_body') }}
                </p>
                <a href="{{ $projectLink ?? $quotationLink }}"
                   style="font-size:14px;font-weight:600;color:#2d6cff;text-decoration:none;">
                    {{ __('quotations.contact_us_cta') }} &rarr;
                </a>
                @if ($company?->email)
                    <p style="margin:10px 0 0;font-size:13px;color:#64748b;">
                        {{ $company->email }}@if ($company?->phone) · {{ $company->phone }}@endif
                    </p>
                @endif
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:15px;line-height:1.7;color:#475569;">
        {{ __('quotations.accepted_email_outro') }}<br>
        <strong style="color:#0f172a;">{{ $brand }}</strong>
    </p>
</x-mail-layout>
