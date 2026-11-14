<x-layouts.app :title="__('team.invite')">
    <x-page-header :title="__('team.invite')" />

    <form method="POST" action="{{ route('team.store') }}" class="max-w-2xl">
        @csrf

        <x-card>
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-label for="name" :value="__('common.name')" />
                    <x-input name="name" required autofocus />
                    <x-input-error for="name" />
                </div>

                <div class="sm:col-span-2">
                    <x-label for="email" :value="__('common.email')" />
                    <x-input name="email" type="email" required />
                    <x-input-error for="email" />
                </div>

                <div>
                    <x-label for="job_title" :value="__('team.job_title')" :hint="__('common.optional')" />
                    <x-input name="job_title" />
                    <x-input-error for="job_title" />
                </div>

                <div>
                    <x-label for="phone" :value="__('common.phone')" :hint="__('common.optional')" />
                    <x-input name="phone" />
                    <x-input-error for="phone" />
                </div>

                <div class="sm:col-span-2">
                    <x-label for="role" :value="__('common.role')" />
                    <x-select name="role">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected(old('role') === $role->value)>
                                {{ $role->label() }} — {{ $role->description() }}
                            </option>
                        @endforeach
                    </x-select>
                    <x-input-error for="role" />
                </div>

                <label class="sm:col-span-2 flex items-start gap-3 rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                           class="mt-0.5 size-4 rounded border-slate-300 text-navy-600 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800">
                    <span>
                        <span class="block text-sm font-medium">{{ __('team.active_account') }}</span>
                        <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">{{ __('team.active_account_hint') }}</span>
                    </span>
                </label>

                <label class="sm:col-span-2 flex items-start gap-3 rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                    <input type="checkbox" name="send_invite" value="1" @checked(old('send_invite', true))
                           class="mt-0.5 size-4 rounded border-slate-300 text-navy-600 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800">
                    <span>
                        <span class="block text-sm font-medium">{{ __('team.send_invite') }}</span>
                        <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">{{ __('team.send_invite_hint') }}</span>
                    </span>
                </label>
            </div>
        </x-card>

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('common.create') }}</x-button>
            <x-button :href="route('team.index')" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>
</x-layouts.app>
