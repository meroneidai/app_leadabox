<x-layouts.app :title="__('quotations.title')">
    <x-page-header :title="__('quotations.title')" :subtitle="__('quotations.subtitle', ['count' => $quotations->total()])">
        @can('create', \App\Models\Quotation::class)
            <x-slot:actions>
                <x-button :href="route('quotations.create')">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('quotations.new') }}
                </x-button>
            </x-slot:actions>
        @endcan
    </x-page-header>

    <div class="mb-6 flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('quotations.index') }}" class="flex min-w-64 flex-1 items-center gap-2">
            @if ($activeStatus)
                <input type="hidden" name="status" value="{{ $activeStatus }}">
            @endif
            <x-input name="q" :value="$search" type="search" :placeholder="__('quotations.search_placeholder')" class="flex-1" />
            <x-button type="submit" variant="secondary">{{ __('common.search') }}</x-button>
        </form>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('quotations.index', ['q' => $search]) }}"
               class="rounded-full px-3.5 py-1.5 text-sm font-medium transition
                      {{ $activeStatus
                          ? 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800'
                          : 'bg-navy-600 text-white shadow-xs' }}">
                {{ __('common.all') }}
            </a>
            @foreach (\App\Enums\QuotationStatus::cases() as $status)
                <a href="{{ route('quotations.index', ['status' => $status->value, 'q' => $search]) }}"
                   class="rounded-full px-3.5 py-1.5 text-sm font-medium transition
                          {{ $activeStatus === $status->value
                              ? 'bg-navy-600 text-white shadow-xs'
                              : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800' }}">
                    {{ $status->label() }}
                </a>
            @endforeach
        </div>
    </div>

    <x-card padding="p-0">
        @if ($quotations->isEmpty())
            <x-empty-state :title="__('quotations.empty_title')" :description="__('quotations.empty_description')">
                @can('create', \App\Models\Quotation::class)
                    <x-slot:action>
                        <x-button :href="route('quotations.create')">{{ __('quotations.new') }}</x-button>
                    </x-slot:action>
                @endcan
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-3xl text-left text-sm">
                    <thead class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('quotations.singular') }}</th>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('quotations.customer') }}</th>
                            <th scope="col" class="whitespace-nowrap px-6 py-3 font-semibold">{{ __('quotations.valid_until') }}</th>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('common.status') }}</th>
                            <th scope="col" class="whitespace-nowrap px-6 py-3 text-right font-semibold">{{ __('quotations.total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($quotations as $quotation)
                            <tr class="cursor-pointer transition hover:bg-slate-50 dark:hover:bg-slate-800/50"
                                onclick="window.location='{{ route('quotations.show', $quotation) }}'">
                                <td class="px-6 py-4">
                                    <a href="{{ route('quotations.show', $quotation) }}" class="font-medium transition hover:text-navy-600 dark:hover:text-navy-300">
                                        {{ $quotation->reference() }}
                                    </a>
                                    @if ($quotation->title)
                                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $quotation->title }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $quotation->customer->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-slate-600 dark:text-slate-400">
                                    {{ $quotation->valid_until->isoFormat('D MMM YYYY') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($quotation->isExpired())
                                        <x-badge color="amber">{{ __('quotations.status_expired') }}</x-badge>
                                    @else
                                        <x-badge :badge="$quotation->status" />
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-medium tabular-nums">
                                    {{ \App\Support\Money::format((string) $quotation->total, $quotation->currency) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($quotations->hasPages())
                <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">{{ $quotations->links() }}</div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
