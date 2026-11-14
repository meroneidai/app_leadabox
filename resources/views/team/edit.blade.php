<x-layouts.app :title="__('team.edit_member')">
    <x-page-header :title="__('team.edit_member')" :subtitle="$member->email" />

    <form method="POST" action="{{ route('team.update', $member) }}" class="max-w-2xl">
        @csrf
        @method('PUT')

        <x-card>
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-label for="name" :value="__('common.name')" />
                    <x-input name="name" :value="$member->name" required autofocus />
                    <x-input-error for="name" />
                </div>

                <div class="sm:col-span-2">
                    <x-label for="email" :value="__('common.email')" />
                    <x-input name="email" type="email" :value="$member->email" required />
                    <x-input-error for="email" />
                </div>

                <div>
                    <x-label for="job_title" :value="__('team.job_title')" :hint="__('common.optional')" />
                    <x-input name="job_title" :value="$member->job_title" />
                    <x-input-error for="job_title" />
                </div>

                <div>
                    <x-label for="phone" :value="__('common.phone')" :hint="__('common.optional')" />
                    <x-input name="phone" :value="$member->phone" />
                    <x-input-error for="phone" />
                </div>

                <div class="sm:col-span-2">
                    <x-label for="role" :value="__('common.role')" />
                    @php $currentRole = $member->roles->first()?->name; @endphp

                    <x-select name="role" :disabled="auth()->user()->is($member)">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected(old('role', $currentRole) === $role->value)>
                                {{ $role->label() }} — {{ $role->description() }}
                            </option>
                        @endforeach
                    </x-select>

                    @if (auth()->user()->is($member))
                        <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ __('team.cannot_edit_self_role') }}</p>
                        <input type="hidden" name="role" value="{{ $currentRole }}">
                    @endif

                    <x-input-error for="role" />
                </div>
            </div>
        </x-card>

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('common.save_changes') }}</x-button>
            <x-button :href="route('team.index')" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>
</x-layouts.app>
