<x-layouts.app :title="__('team.title')">
    <x-page-header
        :title="__('team.title')"
        :subtitle="__('team.subtitle', ['app' => \App\Models\Company::current()?->name ?? config('app.name')])"
    >
        @can('manage team')
            <x-slot:actions>
                <x-button :href="route('team.create')">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('team.invite') }}
                </x-button>
            </x-slot:actions>
        @endcan
    </x-page-header>

    <x-card padding="p-0">
        @if ($members->isEmpty())
            <x-empty-state :title="__('team.empty')" />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('common.name') }}</th>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('common.role') }}</th>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('common.status') }}</th>
                            <th scope="col" class="px-6 py-3 font-semibold">{{ __('team.last_login') }}</th>
                            <th scope="col" class="px-6 py-3 text-right font-semibold">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($members as $member)
                            @php $role = \App\Enums\Role::tryFrom($member->roles->first()?->name ?? ''); @endphp
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <x-avatar :user="$member" size="size-9" />
                                        <div class="min-w-0">
                                            <p class="truncate font-medium">{{ $member->name }}</p>
                                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $member->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($role)
                                        <x-badge :badge="$role" />
                                    @else
                                        <span class="text-slate-400">{{ __('common.none') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge :color="$member->is_active ? 'green' : 'slate'">
                                        {{ $member->is_active ? __('common.active') : __('common.inactive') }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                    {{ $member->last_login_at?->diffForHumans() ?? __('team.never') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-button :href="route('team.edit', $member)" variant="ghost" size="sm">
                                            {{ __('common.edit') }}
                                        </x-button>

                                        @can('manage team')
                                            @if ($member->last_login_at === null && $member->is_active)
                                                <form method="POST" action="{{ route('team.send-invite', $member) }}">
                                                    @csrf
                                                    <x-button type="submit" variant="ghost" size="sm">
                                                        {{ __('team.resend_invite') }}
                                                    </x-button>
                                                </form>
                                            @endif

                                            @if (auth()->user()->isNot($member))
                                                <form method="POST" action="{{ route('team.toggle-active', $member) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-button type="submit" variant="ghost" size="sm">
                                                        {{ $member->is_active ? __('team.deactivate') : __('team.activate') }}
                                                    </x-button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($members->hasPages())
                <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">{{ $members->links() }}</div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
