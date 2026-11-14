@php
    $brand = \App\Models\Company::current()?->name ?? config('app.name');
    $needsSetup = ! \App\Models\User::query()->exists();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f8f8fc">
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
    <style>
        .hero-grid { background-image: linear-gradient(rgba(99, 91, 255, .045) 1px, transparent 1px), linear-gradient(90deg, rgba(99, 91, 255, .045) 1px, transparent 1px); background-size: 42px 42px; mask-image: linear-gradient(to bottom, black, transparent 88%); }
        .hero-dots { background-image: radial-gradient(rgba(111, 102, 255, .65) 1.1px, transparent 1.1px); background-size: 17px 17px; mask-image: radial-gradient(ellipse at center, black 8%, transparent 72%); }
        .hero-orb { animation: orb-drift 11s ease-in-out infinite alternate; }
        .hero-orb-delayed { animation-delay: -5s; }
        .hero-enter { animation: hero-enter .85s cubic-bezier(.2,.8,.2,1) both; }
        .hero-enter-delay { animation: hero-enter .9s .16s cubic-bezier(.2,.8,.2,1) both; }
        .preview-float { animation: preview-float 6s ease-in-out infinite; }
        .preview-pulse { animation: preview-pulse 2.8s ease-in-out infinite; }
        @keyframes hero-enter { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes orb-drift { from { transform: translate3d(-10px, 8px, 0) scale(.96); } to { transform: translate3d(14px, -12px, 0) scale(1.06); } }
        @keyframes preview-float { 0%, 100% { transform: translateY(0) rotate(-1deg); } 50% { transform: translateY(-9px) rotate(0deg); } }
        @keyframes preview-pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(108, 99, 255, .18); } 50% { box-shadow: 0 0 0 7px rgba(108, 99, 255, 0); } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; } }
    </style>
</head>
<body class="min-h-full overflow-x-hidden bg-[#f8f8fc] font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="pointer-events-none fixed inset-0 -z-0 overflow-hidden" aria-hidden="true">
        <div class="hero-grid absolute inset-0 opacity-80 dark:opacity-30"></div>
        <div class="hero-dots absolute -right-48 top-10 size-[42rem] opacity-40 dark:opacity-20"></div>
        <div class="hero-orb absolute -left-32 top-20 size-[28rem] rounded-full bg-violet-300/25 blur-[100px] dark:bg-indigo-600/15"></div>
        <div class="hero-orb hero-orb-delayed absolute -right-24 bottom-0 size-[30rem] rounded-full bg-sky-200/35 blur-[110px] dark:bg-violet-700/15"></div>
    </div>

    <header class="relative z-10 mx-auto flex h-20 max-w-7xl items-center justify-between px-6 sm:px-10">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-lg font-semibold tracking-tight" aria-label="{{ $brand }} home">
            <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-9" width="36" height="36">
            <span>{{ $brand }}</span>
        </a>
        <a href="{{ $needsSetup ? route('register') : route('login') }}" class="group inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-white/70 px-4 py-2 text-sm font-semibold shadow-sm backdrop-blur transition hover:border-violet-300 hover:bg-white dark:border-slate-700 dark:bg-slate-900/70 dark:hover:border-violet-500">
            {{ $needsSetup ? __('auth.create_account') : __('auth.log_in') }}
            <span aria-hidden="true" class="transition group-hover:translate-x-0.5">↗</span>
        </a>
    </header>

    <main class="relative z-10 mx-auto grid min-h-[calc(100svh-5rem)] max-w-7xl items-center gap-14 px-6 pb-16 pt-10 sm:px-10 lg:grid-cols-[1.03fr_.97fr] lg:gap-8 lg:pb-20 lg:pt-4">
        <section class="hero-enter relative max-w-2xl">
            <div class="mb-7 inline-flex items-center gap-2 rounded-full border border-violet-200/80 bg-white/70 px-3.5 py-1.5 text-xs font-semibold tracking-wide text-violet-700 shadow-sm backdrop-blur dark:border-violet-400/20 dark:bg-slate-900/70 dark:text-violet-200">
                <span class="preview-pulse size-2 rounded-full bg-violet-500"></span>
                A clearer way to run your business
            </div>
            <h1 class="max-w-2xl text-[clamp(3.25rem,7vw,6.2rem)] font-semibold leading-[.98] tracking-[-.065em] text-slate-950 dark:text-white">
                From first hello to <span class="bg-gradient-to-r from-violet-600 via-indigo-500 to-sky-500 bg-clip-text text-transparent">paid.</span>
            </h1>
            <p class="mt-7 max-w-xl text-lg leading-8 text-slate-600 dark:text-slate-300 sm:text-xl">
                Keep leads, quotes, projects and invoices moving together. Less admin in the way, more room to do your best work.
            </p>
            <div class="mt-9 flex flex-wrap items-center gap-4">
                <a href="{{ $needsSetup ? route('register') : route('login') }}" class="group inline-flex items-center gap-3 rounded-xl bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white shadow-xl shadow-violet-900/15 transition hover:-translate-y-0.5 hover:bg-violet-700 dark:bg-white dark:text-slate-950 dark:hover:bg-violet-100">
                    {{ $needsSetup ? __('auth.create_account') : __('auth.log_in') }}
                    <span aria-hidden="true" class="transition group-hover:translate-x-1">→</span>
                </a>
                <span class="text-sm text-slate-500 dark:text-slate-400">One calm flow for the work that moves you forward.</span>
            </div>
            <div class="mt-12 flex items-center gap-4 border-t border-slate-200/80 pt-6 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <div class="flex -space-x-2" aria-hidden="true">
                    <span class="grid size-8 place-items-center rounded-full border-2 border-[#f8f8fc] bg-violet-100 text-xs font-bold text-violet-700 dark:border-slate-950 dark:bg-violet-900 dark:text-violet-200">L</span>
                    <span class="grid size-8 place-items-center rounded-full border-2 border-[#f8f8fc] bg-sky-100 text-xs font-bold text-sky-700 dark:border-slate-950 dark:bg-sky-900 dark:text-sky-200">Q</span>
                    <span class="grid size-8 place-items-center rounded-full border-2 border-[#f8f8fc] bg-emerald-100 text-xs font-bold text-emerald-700 dark:border-slate-950 dark:bg-emerald-900 dark:text-emerald-200">✓</span>
                </div>
                <span>Lead. Quote. Deliver. Get paid.</span>
            </div>
        </section>

        <section class="hero-enter-delay relative mx-auto w-full max-w-[34rem]" aria-label="Leadabox workspace preview">
            <div class="absolute inset-8 rounded-[3rem] bg-gradient-to-br from-violet-300/50 via-indigo-200/25 to-sky-200/50 blur-3xl dark:from-violet-700/20 dark:via-indigo-800/15 dark:to-sky-700/20"></div>
            <div class="preview-float relative rounded-[1.75rem] border border-white/80 bg-white/80 p-3 shadow-[0_35px_100px_-35px_rgba(48,41,110,.35)] backdrop-blur-xl dark:border-slate-700/80 dark:bg-slate-900/85 sm:p-4">
                <div class="overflow-hidden rounded-[1.2rem] border border-slate-200/80 bg-[#fbfbfe] dark:border-slate-800 dark:bg-slate-950">
                    <div class="flex items-center justify-between border-b border-slate-200/80 px-5 py-4 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-7" width="28" height="28">
                            <span class="text-sm font-semibold">Workspace</span>
                        </div>
                        <div class="flex gap-1.5" aria-hidden="true"><span class="size-2 rounded-full bg-rose-300"></span><span class="size-2 rounded-full bg-amber-300"></span><span class="size-2 rounded-full bg-emerald-300"></span></div>
                    </div>
                    <div class="grid grid-cols-[3.5rem_1fr] sm:grid-cols-[4.25rem_1fr]">
                        <div class="flex flex-col items-center gap-5 border-r border-slate-200/80 py-5 dark:border-slate-800" aria-hidden="true">
                            <span class="grid size-8 place-items-center rounded-xl bg-violet-600 text-xs font-bold text-white">L</span>
                            <span class="size-4 rounded bg-slate-200 dark:bg-slate-800"></span><span class="size-4 rounded bg-slate-200 dark:bg-slate-800"></span><span class="size-4 rounded bg-violet-200 dark:bg-violet-900"></span><span class="size-4 rounded bg-slate-200 dark:bg-slate-800"></span>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="flex items-start justify-between gap-3">
                                <div><p class="text-[10px] font-medium uppercase tracking-[.18em] text-slate-400">Your business, in view</p><h2 class="mt-1 text-lg font-semibold tracking-tight sm:text-xl">A good day to make progress.</h2></div>
                                <span class="grid size-9 shrink-0 place-items-center rounded-full bg-violet-100 text-sm text-violet-700 dark:bg-violet-950 dark:text-violet-200">✦</span>
                            </div>
                            <div class="mt-6 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl border border-slate-200/80 bg-white p-3.5 dark:border-slate-800 dark:bg-slate-900"><div class="flex items-center justify-between"><span class="text-[11px] text-slate-500">Leads</span><span class="size-2 rounded-full bg-violet-500"></span></div><p class="mt-3 text-2xl font-semibold">Stay<br>in touch</p><div class="mt-3 h-1.5 rounded-full bg-violet-100 dark:bg-violet-950"><div class="h-full w-3/5 rounded-full bg-violet-500"></div></div></div>
                                <div class="rounded-2xl border border-slate-200/80 bg-white p-3.5 dark:border-slate-800 dark:bg-slate-900"><div class="flex items-center justify-between"><span class="text-[11px] text-slate-500">Projects</span><span class="size-2 rounded-full bg-sky-500"></span></div><p class="mt-3 text-2xl font-semibold">Work<br>in motion</p><div class="mt-3 h-1.5 rounded-full bg-sky-100 dark:bg-sky-950"><div class="h-full w-4/5 rounded-full bg-sky-500"></div></div></div>
                            </div>
                            <div class="mt-3 rounded-2xl border border-slate-200/80 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                                <div class="flex items-center justify-between"><span class="text-xs font-semibold">The next steps</span><span class="text-[10px] text-slate-400">ALL IN ONE PLACE</span></div>
                                <div class="mt-4 space-y-3">
                                    <div class="flex items-center gap-3"><span class="grid size-7 place-items-center rounded-lg bg-violet-50 text-xs text-violet-600 dark:bg-violet-950 dark:text-violet-300">↗</span><span class="flex-1 text-xs text-slate-600 dark:text-slate-300">Send a polished quote</span><span class="rounded-full bg-amber-50 px-2 py-1 text-[9px] font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300">READY</span></div>
                                    <div class="flex items-center gap-3"><span class="grid size-7 place-items-center rounded-lg bg-sky-50 text-xs text-sky-600 dark:bg-sky-950 dark:text-sky-300">✓</span><span class="flex-1 text-xs text-slate-600 dark:text-slate-300">Move a project forward</span><span class="rounded-full bg-emerald-50 px-2 py-1 text-[9px] font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">IN FLOW</span></div>
                                    <div class="flex items-center gap-3"><span class="grid size-7 place-items-center rounded-lg bg-indigo-50 text-xs text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300">◎</span><span class="flex-1 text-xs text-slate-600 dark:text-slate-300">Keep customers close</span><span class="size-1.5 rounded-full bg-slate-300"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute -left-5 top-[24%] hidden items-center gap-2.5 rounded-2xl border border-white bg-white/90 px-3.5 py-3 shadow-xl shadow-slate-900/10 backdrop-blur sm:flex dark:border-slate-700 dark:bg-slate-900/90">
                <span class="grid size-8 place-items-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">✓</span><span><span class="block text-[10px] text-slate-500">Everything connected</span><span class="block text-xs font-semibold">One smooth workflow</span></span>
            </div>
            <div class="absolute -right-3 bottom-[12%] hidden rounded-2xl border border-white bg-white/90 px-4 py-3 shadow-xl shadow-slate-900/10 backdrop-blur sm:block dark:border-slate-700 dark:bg-slate-900/90">
                <span class="block text-[10px] text-slate-500">More time for the good work</span><span class="mt-1 block text-xs font-semibold text-violet-700 dark:text-violet-300">A little less admin, every day ✦</span>
            </div>
        </section>
    </main>
</body>
</html>
