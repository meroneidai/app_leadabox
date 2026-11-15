@php $brand = $company?->name ?? config('app.name'); @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/leadabox-mark.svg') }}">
    <title>{{ $invoice->reference() }} · {{ $brand }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        <header class="mb-8 flex items-center gap-2.5">
            <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-9" width="36" height="36">
            <span class="text-lg font-bold tracking-tight">{{ $brand }}</span>
        </header>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-6 sm:px-8">
                <p class="text-sm text-slate-500">{{ __('invoices.public_intro', ['company' => $brand]) }}</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">{{ $invoice->reference() }}</h1>

                @if ($invoice->status === \App\Enums\InvoiceStatus::Paid)
                    <p class="mt-3 inline-block rounded-lg bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700">
                        {{ __('invoices.public_paid') }}
                    </p>
                @else
                    <p class="mt-2 text-sm text-slate-600">
                        {{ __('invoices.public_due', ['date' => $invoice->due_date->isoFormat('D MMMM YYYY')]) }}
                    </p>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-2xl text-left text-sm">
                    <thead class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold sm:px-8">{{ __('invoices.line_name') }}</th>
                            <th scope="col" class="whitespace-nowrap px-3 py-3 text-right font-semibold">{{ __('invoices.line_qty') }}</th>
                            <th scope="col" class="whitespace-nowrap px-6 py-3 text-right font-semibold sm:px-8">{{ __('invoices.line_total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($invoice->lines as $line)
                            <tr>
                                <td class="px-6 py-4 sm:px-8">
                                    <p class="font-medium">{{ $line->name }}</p>
                                    @if ($line->description)
                                        <p class="mt-0.5 whitespace-pre-line text-xs text-slate-500">{{ $line->description }}</p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-right tabular-nums">
                                    {{ rtrim(rtrim((string) $line->quantity, '0'), '.') }} {{ __('services.unit_'.$line->unit) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-medium tabular-nums sm:px-8">
                                    {{ \App\Support\Money::format((string) $line->line_subtotal) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end border-t border-slate-200 px-6 py-6 sm:px-8">
                <dl class="w-full max-w-xs space-y-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">{{ __('invoices.subtotal') }}</dt>
                        <dd class="tabular-nums">{{ \App\Support\Money::format((string) $invoice->subtotal) }}</dd>
                    </div>
                    @foreach ($invoice->vatBreakdown() as $vat)
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">{{ __('invoices.vat_at_rate', ['rate' => rtrim(rtrim($vat['rate'], '0'), '.')]) }}</dt>
                            <dd class="tabular-nums">{{ \App\Support\Money::format($vat['vat']) }}</dd>
                        </div>
                    @endforeach
                    <div class="flex justify-between gap-4 border-t border-slate-200 pt-2 text-base font-semibold">
                        <dt>{{ __('invoices.amount_due') }}</dt>
                        <dd class="tabular-nums">{{ \App\Support\Money::format($invoice->amountDue(), $invoice->currency) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="border-t border-slate-200 bg-slate-50 px-6 py-5 text-sm sm:px-8">
                @php $qr = $company?->payment_qr_enabled ? \App\Support\PaymentQrCode::dataUri($invoice, 200) : null; @endphp

                <div class="flex flex-wrap items-start justify-between gap-6">
                    <div>
                        <h2 class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('invoices.pay_to') }}</h2>
                        @if ($company?->bankgiro)
                            <p>{{ __('invoices.bankgiro') }}: <strong>{{ $company->bankgiro }}</strong></p>
                        @endif
                        @if ($company?->plusgiro)
                            <p>{{ __('invoices.plusgiro') }}: <strong>{{ $company->plusgiro }}</strong></p>
                        @endif
                        @if ($invoice->ocr_reference)
                            <p>{{ __('invoices.ocr_reference') }}: <strong>{{ $invoice->ocr_reference }}</strong></p>
                        @endif
                    </div>

                    @if ($qr)
                        <div class="text-center">
                            <img src="{{ $qr }}" alt="" class="size-28 rounded-lg bg-white p-1.5 ring-1 ring-slate-200">
                            <p class="mt-1.5 max-w-28 text-xs leading-tight text-slate-500">{{ __('invoices.qr_hint') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <footer class="mt-10 text-center text-xs text-slate-500">
            {{ $company?->legal_name ?? $brand }}
            @if ($company?->org_number) · {{ $company->org_number }} @endif
            @if ($company?->email) · {{ $company->email }} @endif
        </footer>
    </div>
</body>
</html>
