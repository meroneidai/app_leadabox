@props(['title', 'description', 'canonical' => null])

@php
    $pageTitle = $title;
    $canonicalUrl = $canonical ?? request()->url();
    $organizationJsonLd = json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => ['Organization', 'ProfessionalService'],
                '@id' => 'https://leadabox.com/#organization',
                'name' => 'Leadabox',
                'url' => 'https://leadabox.com',
                'email' => 'support@leadabox.com',
                'telephone' => '+46764103340',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => 'Stockholm',
                    'addressCountry' => 'SE',
                ],
            ],
            [
                '@type' => 'WebSite',
                '@id' => 'https://leadabox.com/#website',
                'url' => 'https://leadabox.com',
                'name' => 'Leadabox',
                'publisher' => ['@id' => 'https://leadabox.com/#organization'],
                'inLanguage' => app()->getLocale() === 'sv' ? 'sv-SE' : 'en',
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f8f8fc">
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Leadabox">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ asset('images/leadabox-digital-studio.svg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ asset('images/leadabox-digital-studio.svg') }}">

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/leadabox-mark.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <title>{{ $pageTitle }}</title>

    <script type="application/ld+json">{!! $organizationJsonLd !!}</script>

    <script>
        (() => {
            const saved = localStorage.getItem('theme');
            const dark = saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>

    @stack('structured-data')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-water-lens data-water-particles class="marketing-site min-h-full overflow-x-hidden bg-[#f8f8fc] font-sans text-slate-950 antialiased selection:bg-violet-300 selection:text-slate-950 dark:bg-[#080b16] dark:text-white">
    <div class="marketing-atmosphere" aria-hidden="true">
        <div class="marketing-grid"></div>
        <canvas class="water-particles"></canvas>
        <div class="marketing-orb marketing-orb-one"></div>
        <div class="marketing-orb marketing-orb-two"></div>
    </div>

    <header class="site-header fixed inset-x-0 top-0 z-40 px-4 pt-4 sm:px-8">
        <div class="mx-auto flex h-[4.4rem] max-w-7xl items-center justify-between rounded-2xl border border-white/10 bg-white/75 px-4 text-slate-950 shadow-[0_16px_60px_-28px_rgba(8,11,22,.22)] backdrop-blur-2xl dark:bg-slate-950/60 dark:text-white sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="Leadabox home">
                <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-9" width="36" height="36">
                <span class="text-lg font-semibold tracking-tight">Leadabox</span>
            </a>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="Main navigation">
                <a href="{{ route('home') }}" class="site-nav-link {{ request()->routeIs('home') ? 'site-nav-active' : '' }}">{{ __('marketing.nav.home') }}</a>
                <a href="{{ route('marketing.services') }}" class="site-nav-link {{ request()->routeIs('marketing.services', 'marketing.service') ? 'site-nav-active' : '' }}">{{ __('marketing.nav.services') }}</a>
                <a href="{{ route('marketing.about') }}" class="site-nav-link {{ request()->routeIs('marketing.about') ? 'site-nav-active' : '' }}">{{ __('marketing.nav.about') }}</a>
                <a href="{{ route('marketing.contact') }}" class="site-nav-link {{ request()->routeIs('marketing.contact') ? 'site-nav-active' : '' }}">{{ __('marketing.nav.contact') }}</a>
            </nav>

            <div class="flex items-center gap-1.5">
                <div class="hidden items-center gap-1 rounded-xl border border-white/10 bg-white/[.04] p-1 sm:flex" aria-label="Language">
                    @foreach (config('app.supported_locales') as $code => $label)
                        <form method="POST" action="{{ route('locale.update', $code) }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="rounded-lg px-2 py-1.5 text-[10px] font-bold uppercase tracking-wider transition {{ app()->getLocale() === $code ? 'bg-white/15 text-white' : 'text-white/45 hover:text-white' }}" aria-label="{{ $label }}">
                                {{ $code }}
                            </button>
                        </form>
                    @endforeach
                </div>
                <button type="button" x-data="{ dark: document.documentElement.classList.contains('dark') }" @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('theme', dark ? 'dark' : 'light')" class="hidden size-10 place-items-center rounded-xl text-white/70 transition hover:bg-white/10 hover:text-white sm:grid" :aria-label="dark ? '{{ __('common.light_mode') }}' : '{{ __('common.dark_mode') }}'">
                    <svg x-show="!dark" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.36 6.36-1.42-1.42M7.05 7.05 5.64 5.64m12.72 0-1.42 1.41M7.05 16.95l-1.41 1.41M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/></svg>
                    <svg x-show="dark" x-cloak class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5 8.5 8.5 0 1 0 20.5 15.5Z"/></svg>
                </button>
                <a href="{{ route('login') }}" class="hidden rounded-xl border border-white/15 px-3.5 py-2.5 text-xs font-semibold text-white/85 transition hover:border-white/35 hover:bg-white/10 sm:inline-flex">{{ __('marketing.nav.sign_in') }}</a>
                <a href="{{ route('marketing.contact') }}" class="hidden rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-slate-950 shadow-lg shadow-violet-950/20 transition hover:-translate-y-0.5 hover:bg-violet-100 sm:inline-flex">{{ __('marketing.nav.start_project') }} <span class="ml-2" aria-hidden="true">↗</span></a>
                <button type="button" class="grid size-10 place-items-center rounded-xl text-white/80 hover:bg-white/10 lg:hidden" aria-label="{{ __('marketing.nav.menu') }}" @click="$dispatch('toggle-site-menu')">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>
        </div>
            <nav x-data="{ open: false }" @toggle-site-menu.window="open = !open" x-show="open" x-cloak x-transition class="mx-auto mt-2 max-w-7xl rounded-2xl border border-slate-200 bg-white/95 p-3 text-slate-950 shadow-2xl backdrop-blur-2xl dark:border-white/10 dark:bg-slate-950/95 dark:text-white lg:hidden" aria-label="Mobile navigation">
            <a @click="open = false" href="{{ route('home') }}" class="block rounded-xl px-4 py-3 text-sm text-white/75 hover:bg-white/10">{{ __('marketing.nav.home') }}</a>
            <a @click="open = false" href="{{ route('marketing.services') }}" class="block rounded-xl px-4 py-3 text-sm text-white/75 hover:bg-white/10">{{ __('marketing.nav.services') }}</a>
            <a @click="open = false" href="{{ route('marketing.about') }}" class="block rounded-xl px-4 py-3 text-sm text-white/75 hover:bg-white/10">{{ __('marketing.nav.about') }}</a>
            <a @click="open = false" href="{{ route('marketing.contact') }}" class="block rounded-xl px-4 py-3 text-sm text-white/75 hover:bg-white/10">{{ __('marketing.nav.contact') }}</a>
            <a href="{{ route('login') }}" class="mt-2 block rounded-xl border border-white/15 px-4 py-3 text-sm font-semibold text-center">{{ __('marketing.nav.sign_in') }}</a>
        </nav>
    </header>

    <main class="relative z-10 pt-24">
        {{ $slot }}
    </main>

    <footer class="relative z-10 border-t border-slate-200 bg-white/75 px-6 py-12 text-slate-950 dark:border-white/10 dark:bg-slate-950/75 dark:text-white sm:px-10">
        <div class="mx-auto grid max-w-7xl gap-10 md:grid-cols-[1.4fr_.7fr_.7fr]">
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-9" width="36" height="36">
                    <span class="text-lg font-semibold">Leadabox</span>
                </a>
                <p class="marketing-muted mt-4 max-w-sm text-sm leading-6">{{ __('marketing.footer.statement') }}</p>
                <p class="marketing-muted mt-3 text-xs">{{ __('marketing.footer.made_in') }}</p>
            </div>
            <div>
                <h2 class="marketing-muted text-xs font-bold uppercase tracking-[.18em]">{{ __('marketing.footer.company') }}</h2>
                <div class="marketing-muted mt-4 flex flex-col gap-3 text-sm">
                    <a class="transition hover:text-white" href="{{ route('marketing.about') }}">{{ __('marketing.nav.about') }}</a>
                    <a class="transition hover:text-white" href="{{ route('marketing.services') }}">{{ __('marketing.nav.services') }}</a>
                    <a class="transition hover:text-white" href="{{ route('marketing.contact') }}">{{ __('marketing.nav.contact') }}</a>
                    <a class="transition hover:text-white" href="{{ route('login') }}">{{ __('marketing.nav.sign_in') }}</a>
                </div>
            </div>
            <div>
                <h2 class="marketing-muted text-xs font-bold uppercase tracking-[.18em]">{{ __('marketing.footer.legal') }}</h2>
                <div class="marketing-muted mt-4 flex flex-col gap-3 text-sm">
                    <a class="transition hover:text-white" href="https://leadabox.com/privacy">{{ __('marketing.footer.privacy') }}</a>
                    <a class="transition hover:text-white" href="https://leadabox.com/terms">{{ __('marketing.footer.terms') }}</a>
                    <a class="transition hover:text-white" href="mailto:support@leadabox.com">support@leadabox.com</a>
                    <a class="transition hover:text-white" href="https://wa.me/46764103340" target="_blank" rel="noopener noreferrer">WhatsApp · +46 76 410 33 40</a>
                </div>
            </div>
        </div>
        <div class="marketing-muted mx-auto mt-10 flex max-w-7xl flex-col gap-2 border-t border-slate-200 pt-5 text-xs dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
            <span>© {{ date('Y') }} Leadabox. {{ __('marketing.common.all_rights') }}</span>
            <span>{{ __('marketing.common.stockholm') }}</span>
        </div>
    </footer>

    <x-toast />
</body>
</html>
