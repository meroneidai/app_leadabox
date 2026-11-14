<x-layouts.app :title="$invoice->reference()">
    <x-page-header :title="$invoice->reference()" :subtitle="$invoice->title">
        <x-slot:breadcrumbs>
            <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ __('invoices.title') }}
            </a>
        </x-slot:breadcrumbs>

        <x-slot:actions>
            @if ($invoice->isCreditNote())
                <x-badge color="navy">{{ __('invoices.credit_note') }}</x-badge>
            @endif

            @if ($invoice->isOverdue())
                <x-badge color="red">{{ __('invoices.overdue_days', ['days' => $invoice->daysOverdue()]) }}</x-badge>
            @else
                <x-badge :badge="$invoice->status" />
            @endif

            <x-button :href="route('invoices.pdf', $invoice)" variant="secondary">{{ __('invoices.download_pdf') }}</x-button>

            @can('update', $invoice)
                <x-button :href="route('invoices.edit', $invoice)" variant="secondary">{{ __('common.edit') }}</x-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @unless ($invoice->isEditable())
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                    {{ __('invoices.locked_notice') }}
                </div>
            @endunless

            @if ($invoice->creditedInvoice)
                <div class="rounded-xl border border-violet-200 bg-violet-50 px-5 py-4 text-sm text-violet-800 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-200">
                    {{ __('invoices.credits_invoice', ['number' => $invoice->creditedInvoice->reference()]) }}
                </div>
            @endif

            @foreach ($invoice->creditNotes as $credit)
                <div class="rounded-xl border border-violet-200 bg-violet-50 px-5 py-4 text-sm dark:border-violet-500/30 dark:bg-violet-500/10">
                    <a href="{{ route('invoices.show', $credit) }}" class="font-medium text-violet-800 hover:underline dark:text-violet-200">
                        {{ __('invoices.credited_by', ['number' => $credit->reference()]) }}
                    </a>
                </div>
            @endforeach

            <x-card :title="__('invoices.lines')" padding="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-3xl text-left text-sm">
                        <thead class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-semibold">{{ __('invoices.line_name') }}</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 text-right font-semibold">{{ __('invoices.line_qty') }}</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 text-right font-semibold">{{ __('invoices.line_price') }}</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 text-right font-semibold">{{ __('invoices.line_vat') }}</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3 text-right font-semibold">{{ __('invoices.line_total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($invoice->lines as $line)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-medium">{{ $line->name }}</p>
                                        @if ($line->description)
                                            <p class="mt-0.5 whitespace-pre-line text-xs text-slate-500 dark:text-slate-400">{{ $line->description }}</p>
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
                            <dt class="text-slate-500 dark:text-slate-400">{{ __('invoices.subtotal') }}</dt>
                            <dd class="tabular-nums">{{ \App\Support\Money::format((string) $invoice->subtotal) }}</dd>
                        </div>
                        @foreach ($invoice->vatBreakdown() as $vat)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500 dark:text-slate-400">
                                    {{ __('invoices.vat_at_rate', ['rate' => rtrim(rtrim($vat['rate'], '0'), '.')]) }}
                                </dt>
                                <dd class="tabular-nums">{{ \App\Support\Money::format($vat['vat']) }}</dd>
                            </div>
                        @endforeach
                        <div class="flex justify-between gap-4 border-t border-slate-200 pt-2 text-base font-semibold dark:border-slate-800">
                            <dt>{{ __('invoices.total') }}</dt>
                            <dd class="tabular-nums">{{ \App\Support\Money::format((string) $invoice->total, $invoice->currency) }}</dd>
                        </div>
                        @if ((float) $invoice->amount_paid > 0)
                            <div class="flex justify-between gap-4 text-emerald-600 dark:text-emerald-400">
                                <dt>{{ __('invoices.amount_paid') }}</dt>
                                <dd class="tabular-nums">−{{ \App\Support\Money::format((string) $invoice->amount_paid) }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 font-semibold">
                                <dt>{{ __('invoices.amount_due') }}</dt>
                                <dd class="tabular-nums">{{ \App\Support\Money::format($invoice->amountDue(), $invoice->currency) }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </x-card>

            <x-card :title="__('invoices.payments')" padding="p-0">
                @forelse ($invoice->payments as $payment)
                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-3.5 last:border-0 dark:border-slate-800">
                        <div>
                            <p class="font-medium tabular-nums">{{ \App\Support\Money::format((string) $payment->amount, $invoice->currency) }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $payment->paid_on->isoFormat('D MMM YYYY') }} · {{ $payment->method->label() }}
                                @if ($payment->reference) · {{ $payment->reference }} @endif
                            </p>
                        </div>
                        @if ($payment->recorder)
                            <span class="text-xs text-slate-400">{{ $payment->recorder->name }}</span>
                        @endif
                    </div>
                @empty
                    <x-empty-state :title="__('invoices.no_payments')" />
                @endforelse

                @can('recordPayment', $invoice)
                    <div class="border-t border-slate-200 px-6 py-5 dark:border-slate-800" x-data="{ open: false }">
                        <x-button type="button" variant="secondary" size="sm" @click="open = !open">
                            {{ __('invoices.record_payment') }}
                        </x-button>

                        <form x-show="open" x-cloak method="POST" action="{{ route('invoices.payments.store', $invoice) }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                            @csrf

                            <div>
                                <x-label for="amount" :value="__('invoices.payment_amount')" />
                                <x-input name="amount" type="number" step="0.01" min="0.01" :value="$invoice->amountDue()" required />
                                <x-input-error for="amount" />
                            </div>

                            <div>
                                <x-label for="paid_on" :value="__('invoices.payment_date')" />
                                <x-input name="paid_on" type="date" :value="now()->toDateString()" required />
                                <x-input-error for="paid_on" />
                            </div>

                            <div>
                                <x-label for="method" :value="__('invoices.payment_method')" />
                                <x-select name="method">
                                    @foreach (\App\Enums\PaymentMethod::cases() as $method)
                                        <option value="{{ $method->value }}">{{ $method->label() }}</option>
                                    @endforeach
                                </x-select>
                            </div>

                            <div>
                                <x-label for="reference" :value="__('invoices.payment_reference')" :hint="__('common.optional')" />
                                <x-input name="reference" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-button type="submit" size="sm">{{ __('common.save') }}</x-button>
                            </div>
                        </form>
                    </div>
                @endcan
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card :title="__('invoices.customer')">
                <p class="font-medium">
                    <a href="{{ route('customers.show', $invoice->customer) }}" class="transition hover:text-navy-600 dark:hover:text-navy-300">
                        {{ $invoice->customer->name }}
                    </a>
                </p>

                <dl class="mt-4 space-y-2 border-t border-slate-200 pt-4 text-sm dark:border-slate-800">
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('invoices.issue_date') }}</dt>
                        <dd>{{ $invoice->issue_date->isoFormat('D MMM YYYY') }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('invoices.due_date') }}</dt>
                        <dd>{{ $invoice->due_date->isoFormat('D MMM YYYY') }}</dd>
                    </div>
                    @if ($invoice->ocr_reference)
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500 dark:text-slate-400">{{ __('invoices.ocr_reference') }}</dt>
                            <dd class="tabular-nums">{{ $invoice->ocr_reference }}</dd>
                        </div>
                    @endif
                    @if ($invoice->quotation)
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500 dark:text-slate-400">{{ __('quotations.singular') }}</dt>
                            <dd>
                                <a href="{{ route('quotations.show', $invoice->quotation) }}" class="text-navy-600 hover:underline dark:text-navy-300">
                                    {{ $invoice->quotation->reference() }}
                                </a>
                            </dd>
                        </div>
                    @endif
                </dl>
            </x-card>

            @can('issue', $invoice)
                <x-card>
                    <form method="POST" action="{{ route('invoices.issue', $invoice) }}">
                        @csrf
                        <x-button type="submit" class="w-full">{{ __('invoices.issue') }}</x-button>
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ __('invoices.issue_hint') }}</p>
                    </form>
                </x-card>
            @endcan

            @if ($invoice->status->isIssued())
                <x-card :title="__('invoices.send_by_email')">
                    <form method="POST" action="{{ route('invoices.email', $invoice) }}" class="space-y-3">
                        @csrf
                        <x-label for="message" :value="__('invoices.email_message')" :hint="__('common.optional')" />
                        <x-textarea name="message" rows="2" />
                        <x-button type="submit" class="w-full">
                            {{ $invoice->sent_at ? __('invoices.resend_email') : __('invoices.send_by_email') }}
                        </x-button>
                    </form>
                </x-card>

                <x-card :title="__('invoices.delivery')">
                    <ul class="space-y-3 text-sm">
                        <li class="flex gap-2.5">
                            <span class="mt-1.5 size-2 shrink-0 rounded-full {{ $invoice->sent_at ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            <span>
                                @if ($invoice->sent_to)
                                    {{ __('invoices.sent_to_on', ['email' => $invoice->sent_to, 'date' => $invoice->sent_at?->isoFormat('D MMM YYYY HH:mm')]) }}
                                @else
                                    <span class="text-slate-500 dark:text-slate-400">{{ __('invoices.not_sent_yet') }}</span>
                                @endif
                            </span>
                        </li>
                        <li class="flex gap-2.5">
                            <span class="mt-1.5 size-2 shrink-0 rounded-full {{ $invoice->viewed_at ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            <span>
                                @if ($invoice->viewed_at)
                                    {{ __('invoices.opened_on', ['date' => $invoice->viewed_at->isoFormat('D MMM YYYY HH:mm')]) }}
                                @else
                                    <span class="text-slate-500 dark:text-slate-400">{{ __('invoices.not_opened') }}</span>
                                @endif
                            </span>
                        </li>
                        @if ($invoice->reminder_count > 0)
                            <li class="flex gap-2.5">
                                <span class="mt-1.5 size-2 shrink-0 rounded-full bg-amber-500"></span>
                                <span>{{ __('invoices.reminders_count', ['count' => $invoice->reminder_count]) }}</span>
                            </li>
                        @endif
                    </ul>

                    <div class="mt-4 border-t border-slate-200 pt-4 dark:border-slate-800"
                         x-data="{ copied: false, link: '{{ route('public.invoices.show', $invoice->public_token) }}' }">
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('invoices.customer_link_hint') }}</p>
                        <input type="text" readonly :value="link"
                               class="mt-2 block w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-xs
                                      dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <x-button type="button" variant="secondary" size="sm" class="mt-2 w-full"
                                  @click="navigator.clipboard.writeText(link); copied = true; setTimeout(() => copied = false, 2000)">
                            <span x-text="copied ? '✓' : '{{ __('invoices.copy') }}'"></span>
                        </x-button>
                    </div>
                </x-card>
            @endif

            @can('credit', $invoice)
                <x-card>
                    <form method="POST" action="{{ route('invoices.credit', $invoice) }}"
                          onsubmit="return confirm('{{ __('invoices.credit_hint') }}')">
                        @csrf
                        <x-button type="submit" variant="secondary" class="w-full">{{ __('invoices.create_credit_note') }}</x-button>
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ __('invoices.credit_hint') }}</p>
                    </form>
                </x-card>
            @endcan

            @can('delete', $invoice)
                <form method="POST" action="{{ route('invoices.destroy', $invoice) }}"
                      onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm" class="w-full">{{ __('common.delete') }}</x-button>
                </form>
            @endcan
        </div>
    </div>
</x-layouts.app>
