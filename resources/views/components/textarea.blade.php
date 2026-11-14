@props(['name', 'rows' => 4, 'value' => null])

<textarea
    name="{{ $name }}"
    rows="{{ $rows }}"
    {{ $attributes->merge([
        'id' => $name,
        'class' => 'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-xs transition
                    placeholder:text-slate-400
                    focus:border-navy-500 focus:outline-none focus:ring-2 focus:ring-navy-500/20
                    dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500
                    dark:focus:border-navy-400 dark:focus:ring-navy-400/20',
    ]) }}
>{{ old($name, $value) }}</textarea>
