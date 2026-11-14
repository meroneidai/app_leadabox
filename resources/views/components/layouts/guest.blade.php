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
<body data-water-lens class="relative isolate flex h-full items-center justify-center bg-slate-50 px-4 py-12 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-[linear-gradient(rgba(99,91,255,.035)_1px,transparent_1px),linear-gradient(90deg,rgba(99,91,255,.035)_1px,transparent_1px)] bg-size-[42px_42px] dark:opacity-40"></div>
        <canvas class="water-particles"></canvas>
        <div class="absolute -left-32 top-12 size-[28rem] rounded-full bg-violet-300/25 blur-[100px] dark:bg-indigo-600/15"></div>
        <div class="absolute -right-28 bottom-0 size-[28rem] rounded-full bg-sky-200/35 blur-[110px] dark:bg-violet-700/15"></div>
    </div>

    <div class="relative z-10 w-full max-w-md animate-slide-up">
        <a href="{{ route('home') }}" class="mb-8 flex items-center justify-center gap-2.5">
            <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-10" width="40" height="40">
            <span class="text-xl font-semibold tracking-tight">{{ $brand }}</span>
        </a>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            {{ $slot }}
        </div>

        <div class="mt-6 flex justify-center gap-2">
            @foreach (config('app.supported_locales') as $code => $label)
                <form method="POST" action="{{ route('locale.update', $code) }}">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="rounded-md px-2.5 py-1 text-xs font-medium transition
                        {{ app()->getLocale() === $code
                            ? 'bg-slate-200 text-slate-900 dark:bg-slate-800 dark:text-slate-100'
                            : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100' }}">
                        {{ $label }}
                    </button>
                </form>
            @endforeach
        </div>
    </div>

    <x-toast />
</body>
</html>
