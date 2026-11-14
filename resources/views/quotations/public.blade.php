@php
    $brand = $company?->name ?? config('app.name');
    $answered = $quotation->status->isDecided();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/leadabox-mark.svg') }}">
    <title>{{ $quotation->reference() }} · {{ $brand }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        <header class="mb-8 flex items-center gap-2.5">
            <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-9" width="36" height="36">
            <span class="text-lg font-semibold tracking-tight">{{ $brand }}</span>
        </header>

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-6 sm:px-8">
                <p class="text-sm text-slate-500">{{ __('quotations.public_intro', ['company' => $brand]) }}</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">
                    {{ $quotation->title ?: $quotation->reference() }}
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    {{ __('quotations.our_reference') }}: {{ $quotation->reference() }} ·
                    {{ __('quotations.valid_until') }} {{ $quotation->valid_until->isoFormat('D MMM YYYY') }}
                </p>
            </div>

            @if ($quotation->intro)
                <p class="whitespace-pre-line border-b border-slate-200 px-6 py-5 text-sm text-slate-600 sm:px-8">
                    {{ $quotation->intro }}
                </p>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full min-w-2xl text-left text-sm">
                    <thead class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold sm:px-8">{{ __('quotations.line_name') }}</th>
                            <th scope="col" class="whitespace-nowrap px-3 py-3 text-right font-semibold">{{ __('quotations.line_qty') }}</th>
                            <th scope="col" class="whitespace-nowrap px-3 py-3 text-right font-semibold">{{ __('quotations.line_price') }}</th>
                            <th scope="col" class="whitespace-nowrap px-6 py-3 text-right font-semibold sm:px-8">{{ __('quotations.line_total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($quotation->lines as $line)
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
                                <td class="whitespace-nowrap px-3 py-4 text-right tabular-nums">
                                    {{ \App\Support\Money::format((string) $line->unit_price) }}
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
                        <dt class="text-slate-500">{{ __('quotations.subtotal') }}</dt>
                        <dd class="tabular-nums">{{ \App\Support\Money::format((string) $quotation->subtotal) }}</dd>
                    </div>
                    @foreach ($quotation->vatBreakdown() as $vat)
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">{{ __('quotations.vat_at_rate', ['rate' => rtrim(rtrim($vat['rate'], '0'), '.')]) }}</dt>
                            <dd class="tabular-nums">{{ \App\Support\Money::format($vat['vat']) }}</dd>
                        </div>
                    @endforeach
                    <div class="flex justify-between gap-4 border-t border-slate-200 pt-2 text-base font-semibold">
                        <dt>{{ __('quotations.total') }}</dt>
                        <dd class="tabular-nums">{{ \App\Support\Money::format((string) $quotation->total, $quotation->currency) }}</dd>
                    </div>
                </dl>
            </div>

            @if ($quotation->terms)
                <div class="border-t border-slate-200 px-6 py-5 text-sm text-slate-600 sm:px-8">
                    <h2 class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('quotations.terms') }}</h2>
                    <p class="whitespace-pre-line">{{ $quotation->terms }}</p>
                </div>
            @endif
        </div>

        <div class="mt-6">
            @if ($answered)
                <div class="rounded-xl border border-slate-200 bg-white px-6 py-5 text-sm">
                    <p class="font-medium">
                        <x-badge :badge="$quotation->status" />
                    </p>
                    <p class="mt-2 text-slate-600">
                        {{ __('quotations.public_answered', [
                            'date' => ($quotation->accepted_at ?? $quotation->rejected_at)?->isoFormat('D MMM YYYY'),
                        ]) }}
                    </p>
                </div>
            @elseif ($quotation->isExpired())
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-6 py-5 text-sm text-amber-800">
                    {{ __('quotations.public_expired') }}
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2" x-data="{ panel: null }">
                    <button type="button" @click="panel = panel === 'accept' ? null : 'accept'"
                            class="rounded-xl bg-navy-600 px-5 py-3 text-sm font-semibold text-white shadow-xs transition hover:bg-navy-500">
                        {{ __('quotations.public_accept') }}
                    </button>

                    <button type="button" @click="panel = panel === 'reject' ? null : 'reject'"
                            class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        {{ __('quotations.public_reject') }}
                    </button>

                    <form x-show="panel === 'accept'" x-cloak x-transition method="POST"
                          action="{{ route('public.quotations.accept', $quotation->public_token) }}"
                          class="sm:col-span-2 rounded-xl border border-slate-200 bg-white p-5">
                        @csrf
                        <label for="accept_note" class="mb-1.5 block text-sm font-medium">{{ __('quotations.public_accept_note') }}</label>
                        <textarea id="accept_note" name="decision_note" rows="3"
                                  class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-navy-500 focus:outline-none focus:ring-2 focus:ring-navy-500/20"></textarea>
                        <button type="submit" class="mt-3 rounded-lg bg-navy-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-navy-500">
                            {{ __('quotations.public_accept') }}
                        </button>
                    </form>

                    <form x-show="panel === 'reject'" x-cloak x-transition method="POST"
                          action="{{ route('public.quotations.reject', $quotation->public_token) }}"
                          class="sm:col-span-2 rounded-xl border border-slate-200 bg-white p-5">
                        @csrf
                        <label for="reject_note" class="mb-1.5 block text-sm font-medium">{{ __('quotations.public_reject_reason') }}</label>
                        <textarea id="reject_note" name="decision_note" rows="3" required
                                  class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-navy-500 focus:outline-none focus:ring-2 focus:ring-navy-500/20"></textarea>
                        @error('decision_note')
                            <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="mt-3 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            {{ __('quotations.public_reject') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="mt-6">
            <x-comment-thread
                :comments="$comments"
                :action="route('public.quotations.comment', $quotation->public_token)"
            />
        </div>

        <footer class="mt-10 text-center text-xs text-slate-500">
            {{ $company?->legal_name ?? $brand }}
            @if ($company?->org_number) · {{ $company->org_number }} @endif
            @if ($company?->email) · {{ $company->email }} @endif
        </footer>
    </div>
</body>
</html>
