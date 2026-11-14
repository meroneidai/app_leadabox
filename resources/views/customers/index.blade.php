<x-layouts.app :title="__('customers.title')">
    <x-page-header :title="__('customers.title')" :subtitle="__('customers.subtitle', ['count' => $customers->total()])">
        @can('create', \App\Models\Customer::class)
            <x-slot:actions>
                <x-button :href="route('customers.create')">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('customers.new') }}
                </x-button>
            </x-slot:actions>
        @endcan
    </x-page-header>

    <div class="mb-6 flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('customers.index') }}" class="flex min-w-64 flex-1 items-center gap-2">
            @if ($activeStatus)
                <input type="hidden" name="status" value="{{ $activeStatus }}">
            @endif
            <x-input name="q" :value="$search" type="search" :placeholder="__('customers.search_placeholder')" class="flex-1" />
            <x-button type="submit" variant="secondary">{{ __('common.search') }}</x-button>
        </form>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('customers.index', ['q' => $search]) }}"
               class="rounded-full px-3.5 py-1.5 text-sm font-medium transition
                      {{ $activeStatus
                          ? 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800'
                          : 'bg-navy-600 text-white shadow-xs' }}">
                {{ __('common.all') }}
            </a>
            @foreach (\App\Enums\CustomerStatus::cases() as $status)
                <a href="{{ route('customers.index', ['status' => $status->value, 'q' => $search]) }}"
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
        @if ($customers->isEmpty())
            <x-empty-state :title="__('customers.empty_title')" :description="__('customers.empty_description')">
                @can('create', \App\Models\Customer::class)
                    <x-slot:action>
                        <x-button :href="route('customers.create')">{{ __('customers.new') }}</x-button>
                    </x-slot:action>
                @endcan
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('customers.name') }}</th>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('customers.contacts.primary') }}</th>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('common.status') }}</th>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('customers.projects') }}</th>
                            <th scope="col" class="px-6 py-3 text-right font-semibold">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($customers as $customer)
                            @php $primary = $customer->primaryContact(); @endphp
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('customers.show', $customer) }}" class="font-medium transition hover:text-navy-600 dark:hover:text-navy-300">
                                        {{ $customer->name }}
                                    </a>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        #{{ $customer->number }}
                                        @if ($customer->org_number)
                                            · {{ $customer->org_number }}
                                        @endif
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($primary)
                                        <p class="truncate">{{ $primary->name }}</p>
                                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $primary->email }}</p>
                                    @else
                                        <span class="text-slate-400">{{ __('common.none') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4"><x-badge :badge="$customer->status" /></td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $customer->projects_count }}</td>
                                <td class="px-6 py-4 text-right">
                                    <x-button :href="route('customers.show', $customer)" variant="ghost" size="sm">
                                        {{ __('common.edit') }}
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($customers->hasPages())
                <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">{{ $customers->links() }}</div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
