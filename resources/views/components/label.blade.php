@props(['value' => null, 'hint' => null])

<label {{ $attributes->merge(['class' => 'mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300']) }}>
    {{ $value ?? $slot }}
    @if ($hint)
        <span class="font-normal text-slate-400 dark:text-slate-500">({{ $hint }})</span>
    @endif
</label>
