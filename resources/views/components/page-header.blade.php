@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-wrap items-end justify-between gap-4']) }}>
    <div class="animate-fade-in">
        @isset($breadcrumbs)
            <div class="mb-2">{{ $breadcrumbs }}</div>
        @endisset

        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-50">{{ $title }}</h1>

        @if ($subtitle)
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
