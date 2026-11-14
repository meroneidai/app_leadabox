@props(['slug', 'service', 'index'])

<a href="{{ route('marketing.service', ['service' => $slug]) }}" class="marketing-card marketing-surface group relative flex min-h-64 flex-col overflow-hidden rounded-3xl p-6 shadow-[0_20px_60px_-45px_rgba(73,64,145,.35)] sm:p-7">
    <div class="absolute -right-12 -top-12 size-36 rounded-full bg-violet-400/10 blur-3xl transition duration-500 group-hover:bg-violet-400/25"></div>
    <div class="relative flex items-center justify-between">
        <span class="grid size-12 place-items-center rounded-2xl bg-violet-500/10 text-violet-600 ring-1 ring-violet-500/15 dark:text-violet-300">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.55" aria-hidden="true">
                @switch($service['icon'])
                    @case('web')
                        <rect x="3" y="4" width="18" height="13" rx="2"/><path stroke-linecap="round" d="M8 21h8m-4-4v4M3 8h18"/>
                        @break
                    @case('design')
                        <rect x="3" y="3" width="18" height="18" rx="4"/><path stroke-linecap="round" stroke-linejoin="round" d="m8 14 4-7 4 7m-6.8-2h5.6M7 18h10"/>
                        @break
                    @case('software')
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8 8-4 4 4 4m8-8 4 4-4 4m-3-11-2 14"/>
                        @break
                    @case('mobile')
                        <rect x="6" y="2.5" width="12" height="19" rx="2.5"/><path stroke-linecap="round" d="M10 5h4m-3 13h2"/>
                        @break
                    @case('agents')
                        <path stroke-linecap="round" stroke-linejoin="round" d="m12 2 2.4 7.6L22 12l-7.6 2.4L12 22l-2.4-7.6L2 12l7.6-2.4L12 2Z"/><path stroke-linecap="round" d="m19 2 .8 2.2L22 5l-2.2.8L19 8l-.8-2.2L16 5l2.2-.8L19 2Z"/>
                        @break
                    @case('integration')
                        <circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="12" cy="18" r="2.5"/><path stroke-linecap="round" d="m8.2 7.4 2.6 8m5-8-2.6 8M8.5 6h7"/>
                        @break
                    @default
                        <circle cx="10.8" cy="10.8" r="7.3"/><path stroke-linecap="round" d="m16.2 16.2 5 5"/>
                @endswitch
            </svg>
        </span>
        <span class="font-mono text-xs tracking-[.18em] text-slate-400 dark:text-slate-500">{{ str_pad((string) $index, 2, '0', STR_PAD_LEFT) }}</span>
    </div>
    <h3 class="marketing-heading relative mt-6 text-xl font-semibold tracking-tight">{{ $service['title'] }}</h3>
    <p class="marketing-muted relative mt-2 flex-1 text-sm leading-6">{{ $service['short'] }}</p>
    <span class="relative mt-5 inline-flex items-center gap-2 text-sm font-semibold text-violet-700 transition group-hover:gap-3 dark:text-violet-300">
        {{ __('marketing.common.learn_more') }} <span aria-hidden="true">→</span>
    </span>
</a>
