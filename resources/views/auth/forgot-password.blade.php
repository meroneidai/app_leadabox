<x-layouts.guest :title="__('auth.forgot_password_title')">
    <h1 class="text-xl font-semibold tracking-tight">{{ __('auth.forgot_password_title') }}</h1>
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ __('auth.forgot_password_subtitle') }}</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-7 space-y-5">
        @csrf

        <div>
            <x-label for="email" :value="__('auth.email')" />
            <x-input name="email" type="email" required autofocus autocomplete="email" />
            <x-input-error for="email" />
        </div>

        <x-button type="submit" class="w-full" size="lg">{{ __('auth.send_reset_link') }}</x-button>
        <a href="{{ route('login') }}" class="block text-center text-sm font-medium text-navy-700 hover:text-navy-600 dark:text-navy-300">{{ __('auth.log_in') }}</a>
    </form>
</x-layouts.guest>
