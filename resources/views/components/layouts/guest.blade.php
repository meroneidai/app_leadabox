@props(['title' => null])

@php
    $company = \App\Models\Company::current();
    $brand = $company?->name ?? config('app.name');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/leadabox-mark.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <title>{{ $title ? $title.' · '.$brand : $brand }}</title>

    <script>
        (() => {
            const stored = localStorage.getItem('theme');
            const dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative isolate min-h-full overflow-x-hidden bg-[#f5f3ee] font-sans text-slate-900 antialiased dark:bg-[#070a12] dark:text-slate-100">
    <div class="auth-atmosphere pointer-events-none fixed inset-0 z-0 overflow-hidden" aria-hidden="true">
        <div class="auth-grid absolute inset-0"></div>
        <canvas class="water-particles"></canvas>
        <div class="auth-orb auth-orb-left absolute -left-48 top-8 size-[34rem] rounded-full"></div>
        <div class="auth-orb auth-orb-right absolute -right-48 bottom-[-8rem] size-[38rem] rounded-full"></div>
    </div>

    <div class="relative z-10 mx-auto flex min-h-svh w-full max-w-6xl flex-col px-5 py-7 sm:px-8 sm:py-9">
        <header class="flex items-center justify-between">
            <a href="https://leadabox.com" class="inline-flex items-center gap-3" aria-label="{{ $brand }} home">
                <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-10" width="40" height="40">
                <span class="text-base font-semibold tracking-tight">{{ $brand }}</span>
            </a>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    x-data="{ dark: document.documentElement.classList.contains('dark') }"
                    @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('theme', dark ? 'dark' : 'light')"
                    class="grid size-10 place-items-center rounded-full border border-slate-300/70 bg-white/70 text-slate-600 backdrop-blur transition hover:border-amber-500/50 hover:text-slate-950 dark:border-white/15 dark:bg-white/[.06] dark:text-slate-300 dark:hover:text-white"
                    :aria-label="dark ? '{{ __('common.light_mode') }}' : '{{ __('common.dark_mode') }}'"
                >
                    <svg x-show="!dark" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M7.05 16.95l-1.414 1.414m12.728 0-1.414-1.414M7.05 7.05 5.636 5.636M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" /></svg>
                    <svg x-show="dark" x-cloak class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.2 15.4A8.5 8.5 0 0 1 8.6 3.8 8.5 8.5 0 1 0 20.2 15.4Z" /></svg>
                </button>
                <div class="flex items-center rounded-full border border-slate-300/70 bg-white/65 p-1 backdrop-blur dark:border-white/15 dark:bg-white/[.06]">
                    @foreach (config('app.supported_locales') as $code => $label)
                        <form method="POST" action="{{ route('locale.update', $code) }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="rounded-full px-2.5 py-1.5 text-[11px] font-semibold transition {{ app()->getLocale() === $code ? 'bg-[#172337] text-white dark:bg-[#f0e6d2] dark:text-[#172337]' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
                                {{ strtoupper($code) }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </header>

        <main class="grid flex-1 content-center gap-10 py-10 md:grid-cols-[minmax(0,1fr)_minmax(23rem,.78fr)] md:items-center md:gap-16 md:py-14">
            @if (request()->routeIs('login'))
                <section class="hidden max-w-xl animate-slide-up md:block">
                    <p class="mb-6 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[.24em] text-amber-700 dark:text-[#d9c18f]">
                        <span class="h-px w-9 bg-amber-600/70 dark:bg-[#d9c18f]/70"></span>
                        {{ __('auth.workspace_label') }}
                    </p>
                    <h1 class="max-w-lg text-5xl font-medium leading-[1.08] tracking-[-.055em] text-[#162033] dark:text-[#f5f2eb] lg:text-6xl">
                        {{ __('auth.workspace_heading') }}
                    </h1>
                    <p class="mt-6 max-w-md text-base leading-7 text-slate-600 dark:text-slate-300/80">
                        {{ __('auth.workspace_description') }}
                    </p>
                    <div class="mt-10 flex items-center gap-4 text-[10px] font-semibold uppercase tracking-[.2em] text-slate-500 dark:text-slate-400">
                        <span class="size-1.5 rounded-full bg-amber-600 dark:bg-[#d9c18f]"></span>
                        {{ __('auth.workspace_note') }}
                    </div>
                </section>
            @endif

            <section class="mx-auto w-full max-w-md animate-slide-up {{ request()->routeIs('login') ? 'md:mx-0 md:justify-self-end' : 'md:col-span-2' }}">
                <div class="auth-card rounded-[1.75rem] border p-7 shadow-2xl shadow-slate-900/10 backdrop-blur-xl sm:p-9">
                    {{ $slot }}
                </div>

                <div class="mt-5 flex items-center justify-center gap-2 text-center text-xs text-slate-500 dark:text-slate-400">
                    <svg class="size-3.5 text-amber-700 dark:text-[#d9c18f]" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11V7a4 4 0 1 0-8 0v4m0 0h16v10H4V11Zm8 4v2" /></svg>
                    {{ __('auth.workspace_note') }}
                </div>
            </section>
        </main>
    </div>

    <x-toast />
</body>
</html>
