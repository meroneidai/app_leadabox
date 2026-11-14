@props(['align' => 'right', 'width' => 'w-56'])

@php
    $alignment = $align === 'left' ? 'left-0 origin-top-left' : 'right-0 origin-top-right';
@endphp

<div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-cloak
        @click.outside="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $alignment }} {{ $width }} rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg
               dark:border-slate-800 dark:bg-slate-900"
    >
        {{ $slot }}
    </div>
</div>
