@props(['user', 'size' => 'size-8'])

<span {{ $attributes->merge([
    'class' => "flex {$size} shrink-0 items-center justify-center rounded-full bg-navy-100 text-xs font-semibold text-navy-700
                dark:bg-navy-500/20 dark:text-navy-200",
]) }}>
    {{ $user->initials() }}
</span>
