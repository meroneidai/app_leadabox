@props(['href', 'active' => false, 'icon' => null])

<a
    href="{{ $href }}"
    @if ($active) aria-current="page" @endif
    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition duration-150
           {{ $active
               ? 'bg-navy-600 text-white shadow-xs'
               : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-100' }}"
>
    @if ($icon)
        <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
            {!! $icon !!}
        </svg>
    @endif

    <span x-show="!collapsed" x-cloak class="truncate">{{ $slot }}</span>
</a>
