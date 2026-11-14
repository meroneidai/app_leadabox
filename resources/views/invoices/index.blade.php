<x-layouts.app :title="__('invoices.title')">
    <x-page-header :title="__('invoices.title')" :subtitle="__('invoices.subtitle', ['count' => $invoices->total()])">
        @can('create', \App\Models\Invoice::class)
            <x-slot:actions>
                <x-button :href="route('invoices.create')">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('invoices.new') }}
                </x-button>
            </x-slot:actions>
        @endcan
    </x-page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <x-stat :label="__('invoices.outstanding')" :value="\App\Support\Money::format((string) ($totals['outstanding'] ?: 0), $company?->currency)" tone="navy" />
        <x-stat :label="__('invoices.overdue')" :value="\App\Support\Money::format((string) ($totals['overdue'] ?: 0), $company?->currency)" tone="red" />
    </div>

    <div class="mb-6 flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex min-w-64 flex-1 items-center gap-2">
            @if ($activeStatus)
                <input type="hidden" name="status" value="{{ $activeStatus }}">
            @endif
            <x-input name="q" :value="$search" type="search" :placeholder="__('invoices.search_placeholder')" class="flex-1" />
            <x-button type="submit" variant="secondary">{{ __('common.search') }}</x-button>
        </form>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('invoices.index', ['q' => $search]) }}"
               class="rounded-full px-3.5 py-1.5 text-sm font-medium transition
                      {{ $activeStatus ? 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800' : 'bg-navy-600 text-white shadow-xs' }}">
                {{ __('common.all') }}
            </a>

            <a href="{{ route('invoices.index', ['status' => 'overdue', 'q' => $search]) }}"
               class="rounded-full px-3.5 py-1.5 text-sm font-medium transition
                      {{ $activeStatus === 'overdue' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800' }}">
                {{ __('invoices.overdue') }}
            </a>

            @foreach (\App\Enums\InvoiceStatus::cases() as $status)
                <a href="{{ route('invoices.index', ['status' => $status->value, 'q' => $search]) }}"
                   class="rounded-full px-3.5 py-1.5 text-sm font-medium transition
                          {{ $activeStatus === $status->value ? 'bg-navy-600 text-white shadow-xs' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800' }}">
                    {{ $status->label() }}
                </a>
            @endforeach
        </div>
    </div>

    <x-card padding="p-0">
        @if ($invoices->isEmpty())
            <x-empty-state :title="__('invoices.empty_title')" :description="__('invoices.empty_description')">
                @can('create', \App\Models\Invoice::class)
                    <x-slot:action>
                        <x-button :href="route('invoices.create')">{{ __('invoices.new') }}</x-button>
                    </x-slot:action>
                @endcan
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-3xl text-left text-sm">
                    <thead class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('invoices.singular') }}</th>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('invoices.customer') }}</th>
                            <th scope="col" class="whitespace-nowrap px-6 py-3 font-semibold">{{ __('invoices.due_date') }}</th>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('common.status') }}</th>
                            <th scope="col" class="whitespace-nowrap px-6 py-3 text-right font-semibold">{{ __('invoices.total') }}</th>
                            <th scope="col" class="whitespace-nowrap px-6 py-3 text-right font-semibold">{{ __('invoices.amount_due') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($invoices as $invoice)
                            <tr class="cursor-pointer transition hover:bg-slate-50 dark:hover:bg-slate-800/50"
                                onclick="window.location='{{ route('invoices.show', $invoice) }}'">
                                <td class="px-6 py-4">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="font-medium transition hover:text-navy-600 dark:hover:text-navy-300">
                                        {{ $invoice->reference() }}
                                    </a>
                                    @if ($invoice->isCreditNote())
                                        <x-badge color="navy" class="ml-1">{{ __('invoices.credit_note') }}</x-badge>
                                    @endif
                                    @if ($invoice->title)
                                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $invoice->title }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $invoice->customer->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-slate-600 dark:text-slate-400">
                                    {{ $invoice->due_date->isoFormat('D MMM YYYY') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($invoice->isOverdue())
                                        <x-badge color="red">{{ __('invoices.overdue_days', ['days' => $invoice->daysOverdue()]) }}</x-badge>
                                    @else
                                        <x-badge :badge="$invoice->status" />
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right tabular-nums">
                                    {{ \App\Support\Money::format((string) $invoice->total, $invoice->currency) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-medium tabular-nums">
                                    {{ \App\Support\Money::format($invoice->amountDue()) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($invoices->hasPages())
                <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">{{ $invoices->links() }}</div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
