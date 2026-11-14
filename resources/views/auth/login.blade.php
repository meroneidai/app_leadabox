@php $brand = \App\Models\Company::current()?->name ?? config('app.name'); @endphp

<x-layouts.guest :title="__('auth.log_in')">
    <h1 class="text-xl font-semibold tracking-tight">{{ __('auth.login_title', ['app' => $brand]) }}</h1>
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ __('auth.login_subtitle') }}</p>

    <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
        @csrf

        <div>
            <x-label for="email" :value="__('auth.email')" />
            <x-input name="email" type="email" required autofocus autocomplete="email" />
            <x-input-error for="email" />
        </div>

        <div>
            <x-label for="password" :value="__('auth.password_label')" />
            <x-input name="password" type="password" required autocomplete="current-password" />
            <x-input-error for="password" />
        </div>

        <div class="-mt-2 text-right">
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-navy-700 hover:text-navy-600 dark:text-navy-300 dark:hover:text-navy-200">
                {{ __('auth.forgot_password') }}
            </a>
        </div>

        <label class="flex items-center gap-2.5 text-sm text-slate-600 dark:text-slate-400">
            <input type="checkbox" name="remember" value="1"
                   class="size-4 rounded border-slate-300 text-navy-600 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800">
            {{ __('auth.remember_me') }}
        </label>

        <x-button type="submit" class="w-full" size="lg">{{ __('auth.log_in') }}</x-button>
    </form>
</x-layouts.guest>
