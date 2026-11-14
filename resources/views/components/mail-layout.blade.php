@props(['company', 'preview' => null])

@php $brand = $company?->name ?? config('app.name'); @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brand }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;color:#0f172a;">
    @if ($preview)
        <div style="display:none;max-height:0;overflow:hidden;opacity:0;">{{ $preview }}</div>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                       style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
                    <tr>
                        <td style="background:#0b0e1f;padding:24px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-right:12px;">
                                        <img src="{{ asset('images/leadabox-mark.svg') }}"
                                             width="36" height="36" alt="{{ $brand }}"
                                             style="display:block;border:0;width:36px;height:36px;">
                                    </td>
                                    <td>
                                        <span style="color:#ffffff;font-size:18px;font-weight:700;letter-spacing:-0.3px;">{{ $brand }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px;">
                            {{ $slot }}
                        </td>
                    </tr>

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
