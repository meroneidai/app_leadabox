@props(['href' => null, 'type' => 'button'])

@php
    $classes = 'flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-slate-700 transition
                hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
