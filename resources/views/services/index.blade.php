<x-layouts.app :title="__('services.title')">
    <x-page-header :title="__('services.title')" :subtitle="__('services.subtitle')">
        @can('create', \App\Models\Service::class)
            <x-slot:actions>
                <x-button :href="route('service-categories.index')" variant="secondary">
                    {{ __('services.categories.manage') }}
                </x-button>
                <x-button :href="route('services.create')">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('services.new') }}
                </x-button>
            </x-slot:actions>
        @endcan
    </x-page-header>

    <div class="mb-6 flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('services.index') }}" class="flex min-w-64 flex-1 items-center gap-2">
            @if ($activeType)
                <input type="hidden" name="type" value="{{ $activeType }}">
            @endif
            <x-input name="q" :value="$search" type="search" :placeholder="__('services.search_placeholder')" class="flex-1" />
            <x-button type="submit" variant="secondary">{{ __('common.search') }}</x-button>
        </form>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('services.index', ['q' => $search]) }}"
               class="rounded-full px-3.5 py-1.5 text-sm font-medium transition
                      {{ $activeType
                          ? 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800'
                          : 'bg-navy-600 text-white shadow-xs' }}">
                {{ __('common.all') }}
            </a>
            @foreach (\App\Enums\BillingType::cases() as $type)
                <a href="{{ route('services.index', ['type' => $type->value, 'q' => $search]) }}"
                   class="rounded-full px-3.5 py-1.5 text-sm font-medium transition
                          {{ $activeType === $type->value
                              ? 'bg-navy-600 text-white shadow-xs'
                              : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800' }}">
                    {{ $type->label() }}
                </a>
            @endforeach
        </div>
    </div>

    @if ($grouped->isEmpty())
        <x-card padding="p-0">
            <x-empty-state :title="__('services.empty_title')" :description="__('services.empty_description')">
                @can('create', \App\Models\Service::class)
                    <x-slot:action>
                        <x-button :href="route('services.create')">{{ __('services.new') }}</x-button>
                    </x-slot:action>
                @endcan
            </x-empty-state>
        </x-card>
    @else
        <div class="space-y-6">
            @foreach ($grouped as $categoryName => $services)
                <x-card :title="$categoryName" padding="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-3xl text-left text-sm">
                            <thead class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 font-semibold">{{ __('services.name') }}</th>
                                    <th scope="col" class="whitespace-nowrap px-6 py-3 font-semibold">{{ __('services.billing_type') }}</th>
                                    <th scope="col" class="whitespace-nowrap px-6 py-3 text-right font-semibold">{{ __('services.price') }}</th>
                                    <th scope="col" class="whitespace-nowrap px-6 py-3 text-right font-semibold">{{ __('services.vat_rate') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right font-semibold">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach ($services as $service)
                                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50 {{ $service->is_active ? '' : 'opacity-60' }}">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="font-medium">{{ $service->name }}</span>
                                                @if (! $service->is_active)
                                                    <x-badge color="slate">{{ __('services.inactive') }}</x-badge>
                                                @endif
                                                @if ($service->tracks_expiry)
                                                    <x-badge color="amber">{{ __('services.tracks_expiry') }}</x-badge>
                                                @endif
                                            </div>
                                            @if ($service->code)
                                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $service->code }}</p>
                                            @endif
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <x-badge :badge="$service->billing_type" />
                                                @if ($service->billing_interval)
                                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $service->billing_interval->label() }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-4 text-right">
                                            <span class="font-medium tabular-nums">
                                                {{ number_format((float) $service->price, 2, ',', ' ') }} {{ $company?->currency }}
                                            </span>
                                            <span class="block text-xs text-slate-500 dark:text-slate-400">/ {{ __('services.unit_'.$service->unit) }}</span>
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-4 text-right tabular-nums text-slate-600 dark:text-slate-400">
                                            {{ rtrim(rtrim($service->vatRate(), '0'), '.') }}%
                                        </td>

                                        <td class="whitespace-nowrap px-6 py-4 text-right">
                                            @can('update', $service)
                                                <x-button :href="route('services.edit', $service)" variant="ghost" size="sm">
                                                    {{ __('common.edit') }}
                                                </x-button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</x-layouts.app>
