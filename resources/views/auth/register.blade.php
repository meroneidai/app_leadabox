<x-layouts.guest :title="__('auth.create_account')">
    <h1 class="text-xl font-semibold tracking-tight">{{ __('auth.register_title') }}</h1>
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ __('auth.register_subtitle') }}</p>

    <form method="POST" action="{{ route('register') }}" class="mt-7 space-y-5">
        @csrf

        <div>
            <x-label for="name" :value="__('auth.full_name')" />
            <x-input name="name" required autofocus autocomplete="name" />
            <x-input-error for="name" />
        </div>

        <div>
            <x-label for="email" :value="__('auth.email')" />
            <x-input name="email" type="email" required autocomplete="email" />
            <x-input-error for="email" />
        </div>

        <div>
            <x-label for="password" :value="__('auth.password_label')" />
            <x-input name="password" type="password" required autocomplete="new-password" />
            <x-input-error for="password" />
        </div>

        <div>
            <x-label for="password_confirmation" :value="__('auth.confirm_password')" />
            <x-input name="password_confirmation" type="password" required autocomplete="new-password" />
        </div>

        <x-button type="submit" class="w-full" size="lg">{{ __('auth.create_account') }}</x-button>
    </form>
</x-layouts.guest>
