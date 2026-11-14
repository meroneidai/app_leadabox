@props(['badge' => null, 'color' => 'slate'])

@php
    $palette = [
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-500/20 dark:bg-slate-500/10 dark:text-slate-300 dark:ring-slate-400/30',
        'navy' => 'bg-navy-50 text-navy-700 ring-navy-600/20 dark:bg-navy-500/10 dark:text-navy-200 dark:ring-navy-400/30',
        'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/30',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-400/30',
        'red' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-400/30',
    ];

    $classes = $badge ? $badge->badgeClasses() : ($palette[$color] ?? $palette['slate']);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {$classes}"]) }}>
    {{ $badge?->label() ?? $slot }}
</span>
