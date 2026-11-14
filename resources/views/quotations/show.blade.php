<x-layouts.app :title="$quotation->reference()">
    <x-page-header :title="$quotation->reference()" :subtitle="$quotation->title">
        <x-slot:breadcrumbs>
            <a href="{{ route('quotations.index') }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ __('quotations.title') }}
            </a>
        </x-slot:breadcrumbs>

        <x-slot:actions>
            @if ($quotation->isExpired())
                <x-badge color="amber">{{ __('quotations.status_expired') }}</x-badge>
            @else
                <x-badge :badge="$quotation->status" />
            @endif

            <x-button :href="route('quotations.pdf', $quotation)" variant="secondary">
                {{ __('quotations.download_pdf') }}
            </x-button>

            @can('update', $quotation)
                <x-button :href="route('quotations.edit', $quotation)" variant="secondary">{{ __('common.edit') }}</x-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @if (! $quotation->isEditable())
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                    {{ __('quotations.locked_notice') }}
                </div>
            @endif

            @if ($quotation->isExpired())
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                    {{ __('quotations.expired_warning') }}
                </div>
            @endif

            <x-card :title="__('quotations.lines')" padding="p-0">
                @if ($quotation->intro)
                    <p class="whitespace-pre-line border-b border-slate-100 px-6 py-4 text-sm text-slate-600 dark:border-slate-800 dark:text-slate-400">
                        {{ $quotation->intro }}
                    </p>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full min-w-3xl text-left text-sm">
                        <thead class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-semibold">{{ __('quotations.line_name') }}</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 text-right font-semibold">{{ __('quotations.line_qty') }}</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 text-right font-semibold">{{ __('quotations.line_price') }}</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 text-right font-semibold">{{ __('quotations.line_vat') }}</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3 text-right font-semibold">{{ __('quotations.line_total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($quotation->lines as $line)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-medium">{{ $line->name }}</p>
                                        @if ($line->description)
                                            <p class="mt-0.5 whitespace-pre-line text-xs text-slate-500 dark:text-slate-400">{{ $line->description }}</p>
                                        @endif
                                        @if ((float) $line->discount_percent > 0)
                                            <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">
                                                −{{ rtrim(rtrim((string) $line->discount_percent, '0'), '.') }}%
                                            </p>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-right tabular-nums">
                                        {{ rtrim(rtrim((string) $line->quantity, '0'), '.') }} {{ __('services.unit_'.$line->unit) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-right tabular-nums">
                                        {{ \App\Support\Money::format((string) $line->unit_price) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-right tabular-nums text-slate-500 dark:text-slate-400">
                                        {{ rtrim(rtrim((string) $line->vat_rate, '0'), '.') }}%
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right font-medium tabular-nums">
                                        {{ \App\Support\Money::format((string) $line->line_subtotal) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end border-t border-slate-200 px-6 py-5 dark:border-slate-800">
                    <dl class="w-full max-w-xs space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">{{ __('quotations.subtotal') }}</dt>
                            <dd class="tabular-nums">{{ \App\Support\Money::format((string) $quotation->subtotal) }}</dd>
                        </div>

                        @if ((float) $quotation->discount_total > 0)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500 dark:text-slate-400">{{ __('quotations.discount_total') }}</dt>
                                <dd class="tabular-nums">−{{ \App\Support\Money::format((string) $quotation->discount_total) }}</dd>
                            </div>
                        @endif

                        @foreach ($quotation->vatBreakdown() as $vat)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500 dark:text-slate-400">
                                    {{ __('quotations.vat_at_rate', ['rate' => rtrim(rtrim($vat['rate'], '0'), '.')]) }}
                                </dt>
                                <dd class="tabular-nums">{{ \App\Support\Money::format($vat['vat']) }}</dd>
                            </div>
                        @endforeach

                        <div class="flex justify-between gap-4 border-t border-slate-200 pt-2 text-base font-semibold dark:border-slate-800">
                            <dt>{{ __('quotations.total') }}</dt>
                            <dd class="tabular-nums">{{ \App\Support\Money::format((string) $quotation->total, $quotation->currency) }}</dd>
                        </div>
                    </dl>
                </div>
            </x-card>

            @if ($quotation->terms)
                <x-card :title="__('quotations.terms')">
                    <p class="whitespace-pre-line text-sm text-slate-600 dark:text-slate-400">{{ $quotation->terms }}</p>
                </x-card>
            @endif

            @if ($quotation->internal_notes)
                <x-card :title="__('quotations.internal_notes')">
                    <p class="whitespace-pre-line text-sm text-slate-600 dark:text-slate-400">{{ $quotation->internal_notes }}</p>
                </x-card>
            @endif
        </div>

        <div class="space-y-6">
            <x-card :title="__('quotations.customer')">
                <p class="font-medium">
                    <a href="{{ route('customers.show', $quotation->customer) }}" class="transition hover:text-navy-600 dark:hover:text-navy-300">
                        {{ $quotation->customer->name }}
                    </a>
                </p>
                @if ($quotation->contact)
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                        {{ $quotation->contact->name }}
                        @if ($quotation->contact->email)
                            <span class="block">{{ $quotation->contact->email }}</span>
                        @endif
                    </p>
                @endif

                <dl class="mt-4 space-y-2 border-t border-slate-200 pt-4 text-sm dark:border-slate-800">
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('quotations.issue_date') }}</dt>
                        <dd>{{ $quotation->issue_date->isoFormat('D MMM YYYY') }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('quotations.valid_until') }}</dt>
                        <dd>{{ $quotation->valid_until->isoFormat('D MMM YYYY') }}</dd>
                    </div>
                    @if ($quotation->sent_at)
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500 dark:text-slate-400">{{ __('quotations.status_sent') }}</dt>
                            <dd>{{ $quotation->sent_at->isoFormat('D MMM YYYY') }}</dd>
                        </div>
                    @endif
                </dl>
            </x-card>

            @can('send', $quotation)
                <x-card :title="__('quotations.send_by_email')">
                    <form method="POST" action="{{ route('quotations.email', $quotation) }}" class="space-y-3">
                        @csrf
                        <x-label for="message" :value="__('quotations.email_message')" :hint="__('common.optional')" />
                        <x-textarea name="message" rows="3" />
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('quotations.email_message_hint') }}</p>
                        <x-button type="submit" class="w-full">
                            {{ $quotation->sent_at ? __('quotations.resend_email') : __('quotations.send_by_email') }}
                        </x-button>
                    </form>

                    <form method="POST" action="{{ route('quotations.send', $quotation) }}" class="mt-3 border-t border-slate-200 pt-3 dark:border-slate-800">
                        @csrf
                        <x-button type="submit" variant="secondary" size="sm" class="w-full">{{ __('quotations.send') }}</x-button>
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ __('quotations.send_hint') }}</p>
                    </form>
                </x-card>
            @endcan

            @if ($quotation->status !== \App\Enums\QuotationStatus::Draft)
                <x-card :title="__('quotations.delivery')">
                    <ul class="space-y-3 text-sm">
                        <li class="flex gap-2.5">
                            <span class="mt-1.5 size-2 shrink-0 rounded-full {{ $quotation->sent_at ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            <span>
                                @if ($quotation->sent_to)
                                    {{ __('quotations.sent_to_on', [
                                        'email' => $quotation->sent_to,
                                        'date' => $quotation->sent_at?->isoFormat('D MMM YYYY HH:mm'),
                                    ]) }}
                                @elseif ($quotation->sent_at)
                                    {{ __('quotations.status_sent') }} · {{ $quotation->sent_at->isoFormat('D MMM YYYY HH:mm') }}
                                @else
                                    <span class="text-slate-500 dark:text-slate-400">{{ __('quotations.not_sent_yet') }}</span>
                                @endif
                            </span>
                        </li>

                        <li class="flex gap-2.5">
                            <span class="mt-1.5 size-2 shrink-0 rounded-full {{ $quotation->viewed_at ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            <span>
                                @if ($quotation->viewed_at)
                                    {{ __('quotations.opened_on', ['date' => $quotation->viewed_at->isoFormat('D MMM YYYY HH:mm')]) }}
                                @else
                                    <span class="text-slate-500 dark:text-slate-400">{{ __('quotations.not_opened') }}</span>
                                @endif
                            </span>
                        </li>

                        <li class="flex gap-2.5">
                            <span class="mt-1.5 size-2 shrink-0 rounded-full {{ $quotation->status->isDecided() ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            <span>
                                @if ($quotation->status->isDecided())
                                    {{ __('quotations.answered_on', [
                                        'date' => ($quotation->accepted_at ?? $quotation->rejected_at)?->isoFormat('D MMM YYYY HH:mm'),
                                    ]) }}
                                    <x-badge :badge="$quotation->status" class="ml-1" />
                                @else
                                    <span class="text-slate-500 dark:text-slate-400">—</span>
                                @endif
                            </span>
                        </li>
                    </ul>

                    @if ($quotation->decision_note)
                        <div class="mt-4 rounded-lg bg-slate-50 p-3 dark:bg-slate-800/60">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                {{ __('quotations.customer_comment') }}
                            </p>
                            <p class="mt-1 whitespace-pre-line text-sm">{{ $quotation->decision_note }}</p>
                        </div>
                    @endif
                </x-card>

                <x-card :title="__('quotations.customer_link')">
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('quotations.customer_link_hint') }}</p>

                    <div class="mt-3" x-data="{ copied: false, link: '{{ route('public.quotations.show', $quotation->public_token) }}' }">
                        <input type="text" readonly :value="link"
                               class="block w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-xs
                                      dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <x-button type="button" variant="secondary" size="sm" class="mt-2 w-full"
                                  @click="navigator.clipboard.writeText(link); copied = true; setTimeout(() => copied = false, 2000)">
                            <span x-text="copied ? '✓' : '{{ __('quotations.copy') }}'"></span>
                        </x-button>
                    </div>
                </x-card>
            @endif

            @if ($quotation->status === \App\Enums\QuotationStatus::Accepted)
                @can('create', \App\Models\Invoice::class)
                    <x-card>
                        <form method="POST" action="{{ route('quotations.convert', $quotation) }}">
                            @csrf
                            <x-button type="submit" class="w-full">{{ __('invoices.convert_to_invoice') }}</x-button>
                        </form>
                    </x-card>
                @endcan
            @endif

            @can('decide', $quotation)
                @if ($quotation->status === \App\Enums\QuotationStatus::Sent)
                    <x-card>
                        <form method="POST" action="{{ route('quotations.accept', $quotation) }}">
                            @csrf
                            <x-button type="submit" class="w-full">{{ __('quotations.mark_accepted') }}</x-button>
                        </form>

                        <form method="POST" action="{{ route('quotations.reject', $quotation) }}" class="mt-2">
                            @csrf
                            <x-button type="submit" variant="secondary" class="w-full">{{ __('quotations.mark_rejected') }}</x-button>
                        </form>
                    </x-card>
                @endif
            @endcan

            @if ($quotation->status->isDecided())
                <x-card>
                    <p class="text-sm font-medium">
                        {{ __('quotations.decided_on', [
                            'date' => ($quotation->accepted_at ?? $quotation->rejected_at)?->isoFormat('D MMM YYYY HH:mm'),
                        ]) }}
                    </p>
                    @if ($quotation->decision_note)
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-400">{{ $quotation->decision_note }}</p>
                    @endif
                </x-card>
            @endif

            @can('delete', $quotation)
                <form method="POST" action="{{ route('quotations.destroy', $quotation) }}"
                      onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm" class="w-full">{{ __('common.delete') }}</x-button>
                </form>
            @endcan
        </div>
    </div>
</x-layouts.app>
