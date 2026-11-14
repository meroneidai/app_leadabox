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
<body class="flex h-full items-center justify-center bg-slate-50 px-4 py-12 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="absolute inset-x-0 top-0 -z-10 h-72 bg-gradient-to-b from-navy-100/60 to-transparent dark:from-navy-500/10"></div>

    <div class="w-full max-w-md animate-slide-up">
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
