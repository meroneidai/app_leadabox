@props(['name'])

<select
    name="{{ $name }}"
    {{ $attributes->merge([
        'id' => $name,
        'class' => 'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-xs transition
                    focus:border-navy-500 focus:outline-none focus:ring-2 focus:ring-navy-500/20
                    dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                    dark:focus:border-navy-400 dark:focus:ring-navy-400/20',
    ]) }}
>
    {{ $slot }}
</select>
