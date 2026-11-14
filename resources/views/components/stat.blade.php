@props(['label', 'value', 'tone' => 'navy'])

@php
    $tones = [
        'navy' => 'text-navy-600 dark:text-navy-300',
        'green' => 'text-emerald-600 dark:text-emerald-400',
        'amber' => 'text-amber-600 dark:text-amber-400',
        'red' => 'text-rose-600 dark:text-rose-400',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'group rounded-xl border border-slate-200 bg-white p-5 shadow-xs transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-900']) }}>
    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
    <p class="mt-2 text-3xl font-semibold tracking-tight {{ $tones[$tone] ?? $tones['navy'] }}">{{ $value }}</p>
    @isset($meta)
        <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">{{ $meta }}</p>
    @endisset
</div>
