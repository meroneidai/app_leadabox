<x-layouts.guest :title="__('auth.reset_password_title')">
    <h1 class="text-xl font-semibold tracking-tight">{{ __('auth.reset_password_title') }}</h1>

    <form method="POST" action="{{ route('password.store') }}" class="mt-7 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <x-label for="email" :value="__('auth.email')" />
            <x-input name="email" type="email" :value="old('email', $email)" required autofocus autocomplete="email" />
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

        <x-button type="submit" class="w-full" size="lg">{{ __('auth.reset_password') }}</x-button>
    </form>
</x-layouts.guest>
