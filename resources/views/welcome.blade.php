@php
    $brand = \App\Models\Company::current()?->name ?? config('app.name');
    $needsSetup = ! \App\Models\User::query()->exists();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/leadabox-mark.svg') }}">
    <title>{{ $brand }} — {{ __('auth.login_subtitle') }}</title>

    <script>
        (() => {
            const stored = localStorage.getItem('theme');
            const dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-white font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[32rem] bg-gradient-to-b from-navy-100/70 via-white to-white dark:from-navy-500/10 dark:via-slate-950 dark:to-slate-950"></div>

    <header class="mx-auto flex h-20 max-w-6xl items-center justify-between px-6">
        <span class="flex items-center gap-2.5 text-lg font-semibold tracking-tight">
            <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-9" width="36" height="36">
            {{ $brand }}
        </span>

        <nav class="flex items-center gap-2">
            @if ($needsSetup)
                <a href="{{ route('register') }}" class="rounded-lg bg-navy-600 px-4 py-2 text-sm font-semibold text-white shadow-xs transition hover:bg-navy-500">
                    {{ __('auth.create_account') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="rounded-lg bg-navy-600 px-4 py-2 text-sm font-semibold text-white shadow-xs transition hover:bg-navy-500">
                    {{ __('auth.log_in') }}
                </a>
            @endif
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-16 sm:py-24">
        <div class="animate-slide-up">
            <span class="inline-flex items-center rounded-full bg-navy-50 px-3 py-1 text-xs font-medium text-navy-700 ring-1 ring-inset ring-navy-600/20 dark:bg-navy-500/10 dark:text-navy-200 dark:ring-navy-400/30">
                {{ $brand }} Business OS
            </span>

            <h1 class="mt-5 max-w-3xl text-4xl font-semibold tracking-tight sm:text-6xl">
                {{ __('auth.login_subtitle') }}
            </h1>

            <p class="mt-5 max-w-xl text-lg text-slate-600 dark:text-slate-400">
                Leads, customers, quotations, invoices, projects and subscriptions — managed end to end in one place.
            </p>
        </div>

        <div class="mt-16 grid gap-6 sm:grid-cols-3">
            @foreach ([
                ['title' => __('nav.quotations'), 'body' => 'Send professional quotations and let customers approve them online.'],
                ['title' => __('nav.invoices'), 'body' => 'Sequential invoicing, partial payments and reminders that run themselves.'],
                ['title' => __('nav.subscriptions'), 'body' => 'Track hosting, domains and recurring services with automatic renewals.'],
            ] as $feature)
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="font-semibold">{{ $feature['title'] }}</h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $feature['body'] }}</p>
                </div>
            @endforeach
        </div>
    </main>
</body>
</html>
