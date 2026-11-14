<x-mail-layout :company="$company" :preview="$summary">
    <h1 style="margin:0 0 12px;font-size:21px;line-height:1.35;font-weight:700;color:#0f172a;">
        {{ $headline }}
    </h1>

    <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#475569;">
        {{ $summary }}
    </p>

    @if ($quote)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
               style="background:#f8fafc;border-left:3px solid #2d6cff;border-radius:6px;margin:0 0 20px;">
            <tr>
                <td style="padding:16px 20px;font-size:14px;line-height:1.7;color:#334155;white-space:pre-line;">{{ $quote }}</td>
            </tr>
        </table>
    @endif

    @if ($details)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
               style="border:1px solid #e2e8f0;border-radius:10px;margin:0 0 24px;">
            @foreach ($details as $row)
                <tr>
                    <td style="padding:10px 16px;font-size:13px;color:#64748b;border-bottom:{{ $loop->last ? '0' : '1px solid #f1f5f9' }};">
                        {{ $row['label'] }}
                    </td>
                    <td align="right" style="padding:10px 16px;font-size:13px;font-weight:600;color:#0f172a;border-bottom:{{ $loop->last ? '0' : '1px solid #f1f5f9' }};">
                        {{ $row['value'] }}
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    @if ($actionUrl)
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td style="background:#0f172a;border-radius:10px;">
                    <a href="{{ $actionUrl }}"
                       style="display:inline-block;padding:13px 26px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;">
                        {{ $actionLabel ?? __('comments.open_in_leadabox') }}
                    </a>
                </td>
            </tr>
        </table>
    @endif
</x-mail-layout>
