<x-layouts.app :title="__('profile.title')">
    <x-page-header :title="__('profile.title')" :subtitle="__('profile.subtitle')" />

    <div class="grid max-w-4xl gap-6 lg:grid-cols-2">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <x-card :title="__('profile.details')">
                <div class="space-y-5">
                    <div>
                        <x-label for="name" :value="__('common.name')" />
                        <x-input name="name" :value="$user->name" required />
                        <x-input-error for="name" />
                    </div>

                    <div>
                        <x-label for="email" :value="__('common.email')" />
                        <x-input name="email" type="email" :value="$user->email" required />
                        <x-input-error for="email" />
                    </div>

                    <div>
                        <x-label for="job_title" :value="__('team.job_title')" :hint="__('common.optional')" />
                        <x-input name="job_title" :value="$user->job_title" />
                    </div>

                    <div>
                        <x-label for="phone" :value="__('common.phone')" :hint="__('common.optional')" />
                        <x-input name="phone" :value="$user->phone" />
                    </div>

                    <div>
                        <x-label for="locale" :value="__('common.language')" />
                        <x-select name="locale">
                            @foreach ($locales as $code => $label)
                                <option value="{{ $code }}" @selected(old('locale', $user->locale) === $code)>{{ $label }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="locale" />
                    </div>

                    <x-button type="submit">{{ __('common.save_changes') }}</x-button>
                </div>
            </x-card>
        </form>

        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')

            <x-card :title="__('profile.password')">
                <div class="space-y-5">
                    <div>
                        <x-label for="current_password" :value="__('profile.current_password')" />
                        <x-input name="current_password" type="password" autocomplete="current-password" required />
                        <x-input-error for="current_password" />
                    </div>

                    <div>
                        <x-label for="password" :value="__('profile.new_password')" />
                        <x-input name="password" type="password" autocomplete="new-password" required />
                        <x-input-error for="password" />
                    </div>

                    <div>
                        <x-label for="password_confirmation" :value="__('profile.confirm_new_password')" />
                        <x-input name="password_confirmation" type="password" autocomplete="new-password" required />
                    </div>

                    <x-button type="submit">{{ __('common.save') }}</x-button>
                </div>
            </x-card>
        </form>
    </div>
</x-layouts.app>
