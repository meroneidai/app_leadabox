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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/leadabox-mark.svg') }}">
    <title>{{ $title ? $title.' · '.$brand : $brand }}</title>

    {{-- Applied before paint so the theme never flashes. --}}
    <script>
        (() => {
            const stored = localStorage.getItem('theme');
            const dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div
        class="min-h-full lg:flex"
        x-data="{
            collapsed: localStorage.getItem('sidebar') === 'collapsed',
            mobileOpen: false,
            toggleSidebar() {
                this.collapsed = !this.collapsed;
                localStorage.setItem('sidebar', this.collapsed ? 'collapsed' : 'expanded');
            },
        }"
    >
        <div
            x-show="mobileOpen"
            x-cloak
            x-transition.opacity
            @click="mobileOpen = false"
            class="fixed inset-0 z-30 bg-slate-900/40 backdrop-blur-sm lg:hidden"
        ></div>

        <aside
            :class="[
                collapsed ? 'lg:w-20' : 'lg:w-64',
                mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            ]"
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-slate-200 bg-white transition-all duration-200
                   dark:border-slate-800 dark:bg-slate-900 lg:static lg:z-auto lg:translate-x-0"
        >
            <div class="flex h-16 shrink-0 items-center gap-2 px-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 overflow-hidden">
                    <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-9 shrink-0" width="36" height="36">
                    <span x-show="!collapsed" class="truncate text-base font-semibold tracking-tight">{{ $brand }}</span>
                </a>
            </div>

            <nav class="scrollbar-slim flex-1 space-y-6 overflow-y-auto px-3 py-4">
                <div class="space-y-1">
                    <x-nav-link
                        :href="route('dashboard')"
                        :active="request()->routeIs('dashboard')"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />'
                    >{{ __('nav.dashboard') }}</x-nav-link>
                </div>

                @can('view customers')
                    <div>
                        <p x-show="!collapsed" class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            {{ __('nav.sales') }}
                        </p>
                        <div class="space-y-1">
                            <x-nav-link
                                :href="route('customers.index')"
                                :active="request()->routeIs('customers.*')"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />'
                            >{{ __('nav.customers') }}</x-nav-link>

                            @can('view services')
                                <x-nav-link
                                    :href="route('services.index')"
                                    :active="request()->routeIs('services.*') || request()->routeIs('service-categories.*')"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z" />'
                                >{{ __('nav.services') }}</x-nav-link>
                            @endcan

                            @can('view quotations')
                                <x-nav-link
                                    :href="route('quotations.index')"
                                    :active="request()->routeIs('quotations.*')"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />'
                                >{{ __('nav.quotations') }}</x-nav-link>
                            @endcan
                        </div>
                    </div>
                @endcan

                @can('view invoices')
                    <div>
                        <p x-show="!collapsed" class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            {{ __('nav.finance') }}
                        </p>
                        <div class="space-y-1">
                            <x-nav-link
                                :href="route('invoices.index')"
                                :active="request()->routeIs('invoices.*')"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0018.75 4.5H3.75A2.25 2.25 0 001.5 6.75v10.5A2.25 2.25 0 003.75 19.5z" />'
                            >{{ __('nav.invoices') }}</x-nav-link>
                        </div>
                    </div>
                @endcan

                @can('view projects')
                    <div>
                        <p x-show="!collapsed" class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            {{ __('nav.operations') }}
                        </p>                        <div class="space-y-1">
                            <x-nav-link
                                :href="route('projects.index')"
                                :active="request()->routeIs('projects.*') || request()->routeIs('tasks.*')"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25A2.25 2.25 0 0113.5 8.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'
                            >{{ __('nav.projects') }}</x-nav-link>
                        </div>
                    </div>
                @endcan

                @canany(['view team', 'manage settings'])
                    <div>
                        <p x-show="!collapsed" class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            {{ __('nav.administration') }}
                        </p>
                        <div class="space-y-1">
                            @can('view team')
                                <x-nav-link
                                    :href="route('team.index')"
                                    :active="request()->routeIs('team.*')"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />'
                                >{{ __('nav.team') }}</x-nav-link>
                            @endcan

                            @can('manage settings')
                                <x-nav-link
                                    :href="route('settings.company.edit')"
                                    :active="request()->routeIs('settings.*')"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />'
                                >{{ __('nav.settings') }}</x-nav-link>
                            @endcan
                        </div>
                    </div>
                @endcanany
            </nav>

            <div class="border-t border-slate-200 p-3 dark:border-slate-800">
                <button
                    type="button"
                    @click="toggleSidebar()"
                    class="hidden w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-slate-500 transition
                           hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-100 lg:flex"
                >
                    <svg class="size-5 shrink-0 transition-transform duration-200" :class="collapsed && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                    </svg>
                    <span x-show="!collapsed">{{ __('common.back') }}</span>
                </button>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur
                           dark:border-slate-800 dark:bg-slate-900/80 sm:px-6">
                <button
                    type="button"
                    @click="mobileOpen = true"
                    class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden"
                    aria-label="Open navigation"
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div class="ml-auto flex items-center gap-1.5">
                    <x-dropdown>
                        <x-slot:trigger>
                            <button type="button" class="flex items-center gap-1.5 rounded-lg px-2.5 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3 7.5 7.03 7.5 12s2.015 9 4.5 9zM3.6 9h16.8M3.6 15h16.8" />
                                </svg>
                                <span class="hidden uppercase sm:inline">{{ app()->getLocale() }}</span>
                            </button>
                        </x-slot:trigger>

                        @foreach (config('app.supported_locales') as $code => $label)
                            <form method="POST" action="{{ route('locale.update', $code) }}">
                                @csrf
                                @method('PUT')
                                <x-dropdown-item type="submit" class="{{ app()->getLocale() === $code ? 'bg-slate-100 dark:bg-slate-800' : '' }}">
                                    {{ $label }}
                                </x-dropdown-item>
                            </form>
                        @endforeach
                    </x-dropdown>

                    <button
                        type="button"
                        x-data="{ dark: document.documentElement.classList.contains('dark') }"
                        @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('theme', dark ? 'dark' : 'light')"
                        class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                        :aria-label="dark ? '{{ __('common.light_mode') }}' : '{{ __('common.dark_mode') }}'"
                    >
                        <svg x-show="!dark" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                        <svg x-show="dark" x-cloak class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                    </button>

                    <x-dropdown>
                        <x-slot:trigger>
                            <button type="button" class="flex items-center gap-2 rounded-lg p-1.5 transition hover:bg-slate-100 dark:hover:bg-slate-800">
                                <x-avatar :user="auth()->user()" />
                                <span class="hidden text-sm font-medium sm:block">{{ auth()->user()->name }}</span>
                            </button>
                        </x-slot:trigger>

                        <div class="border-b border-slate-100 px-3 py-2 dark:border-slate-800">
                            <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</p>
                        </div>

                        <div class="pt-1.5">
                            <x-dropdown-item :href="route('profile.edit')">{{ __('nav.profile') }}</x-dropdown-item>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-item type="submit">{{ __('nav.logout') }}</x-dropdown-item>
                            </form>
                        </div>
                    </x-dropdown>
                </div>
            </header>

            <main class="flex-1 px-4 py-8 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl animate-slide-up">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <x-toast />

    @stack('scripts')
</body>
</html>
