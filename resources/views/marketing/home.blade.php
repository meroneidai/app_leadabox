<x-layouts.marketing
    :title="__('marketing.seo.home_title')"
    :description="__('marketing.seo.home_description')"
    :canonical="$canonical"
>
    <section class="relative mx-auto grid min-h-[calc(100svh-6rem)] max-w-7xl items-center gap-8 px-6 pb-20 pt-12 sm:px-10 lg:grid-cols-[1fr_1.02fr] lg:gap-0 lg:pb-24 lg:pt-8">
        <div class="hero-enter relative z-10 max-w-2xl">
            <div class="marketing-glass mb-7 inline-flex items-center gap-2.5 rounded-full px-4 py-2 text-xs font-semibold tracking-wide">
                <span class="relative flex size-2">
                    <span class="absolute inline-flex size-full animate-ping rounded-full bg-violet-400 opacity-60"></span>
                    <span class="relative inline-flex size-2 rounded-full bg-violet-500"></span>
                </span>
                <span class="marketing-muted">{{ $content['eyebrow'] }}</span>
            </div>

            <h1 class="marketing-heading max-w-2xl text-[clamp(3.2rem,7.5vw,6.65rem)] font-semibold leading-[.94] tracking-[-.07em]">
                {{ $content['title_before'] }}
                <span class="marketing-gradient-text block pb-2">{{ $content['title_accent'] }}</span>
            </h1>

            <p class="marketing-muted mt-7 max-w-xl text-lg leading-8 sm:text-xl">{{ $content['lead'] }}</p>

            <div class="mt-9 flex flex-wrap items-center gap-3">
                <a href="{{ route('marketing.contact') }}" class="group inline-flex items-center gap-3 rounded-xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white shadow-xl shadow-violet-950/20 transition hover:-translate-y-0.5 hover:bg-violet-700 dark:bg-white dark:text-slate-950 dark:hover:bg-violet-100">
                    {{ $content['primary_cta'] }}
                    <span aria-hidden="true" class="transition group-hover:translate-x-1">↗</span>
                </a>
                <a href="{{ route('marketing.services') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300/80 bg-white/60 px-5 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-violet-400 hover:bg-white dark:border-white/15 dark:bg-white/[.04] dark:text-white/85 dark:hover:bg-white/10">
                    {{ $content['secondary_cta'] }}
                </a>
            </div>

            <div class="mt-10 flex flex-wrap gap-2.5">
                @foreach (['Web', 'Software', 'AI', 'Digital growth'] as $specialty)
                    <span class="rounded-full border border-slate-300/70 bg-white/45 px-3 py-1.5 text-[11px] font-medium text-slate-600 dark:border-white/10 dark:bg-white/[.035] dark:text-white/55">{{ $specialty }}</span>
                @endforeach
            </div>
        </div>

        <div class="hero-enter-delay relative mx-auto w-full max-w-[44rem] lg:-ml-4">
            <div class="absolute inset-[8%] rounded-full bg-gradient-to-br from-violet-400/25 via-indigo-400/15 to-cyan-300/25 blur-[80px]"></div>
            <div class="hero-art relative z-10">
                <img src="{{ asset('images/leadabox-digital-studio.svg') }}" alt="{{ $content['image_alt'] }}" width="850" height="720" fetchpriority="high" class="relative z-10 h-auto w-full drop-shadow-[0_40px_85px_rgba(57,48,134,.24)]">
            </div>
            <div class="marketing-glass absolute left-0 top-[24%] z-20 hidden items-center gap-3 rounded-2xl px-4 py-3 shadow-xl shadow-indigo-950/10 sm:flex">
                <span class="grid size-9 place-items-center rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-300">✦</span>
                <span><span class="marketing-muted block text-[10px]">{{ __('marketing.common.our_approach') }}</span><span class="marketing-heading block text-xs font-semibold">Strategy · Design · Build</span></span>
            </div>
            <div class="marketing-glass absolute bottom-[13%] right-0 z-20 hidden rounded-2xl px-4 py-3 shadow-xl shadow-indigo-950/10 sm:block">
                <span class="marketing-muted block text-[10px]">{{ __('marketing.common.stockholm') }}</span>
                <span class="marketing-heading mt-1 block text-xs font-semibold">{{ __('marketing.footer.statement') }}</span>
            </div>
        </div>
    </section>

    <section class="relative z-10 border-y border-slate-200/75 bg-white/55 px-6 py-16 dark:border-white/10 dark:bg-white/[.025] sm:px-10 sm:py-20">
        <div class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[.8fr_1.2fr] lg:gap-20">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ $content['intro_eyebrow'] }}</p>
                <h2 class="marketing-heading mt-5 text-3xl font-semibold leading-tight tracking-[-.04em] sm:text-4xl">{{ $content['intro_title'] }}</h2>
            </div>
            <div class="flex flex-col justify-center">
                <p class="marketing-muted max-w-3xl text-lg leading-8">{{ $content['intro_body'] }}</p>
                <a href="{{ route('marketing.about') }}" class="mt-6 inline-flex w-fit items-center gap-2 text-sm font-semibold text-violet-700 transition hover:gap-3 dark:text-violet-300">
                    {{ __('marketing.nav.about') }} <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section>

    <section class="relative z-10 mx-auto max-w-7xl px-6 py-20 sm:px-10 sm:py-28">
        <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ $content['services_eyebrow'] }}</p>
                <h2 class="marketing-heading mt-4 text-3xl font-semibold tracking-[-.045em] sm:text-5xl">{{ $content['services_title'] }}</h2>
                <p class="marketing-muted mt-4 max-w-xl leading-7">{{ $content['services_body'] }}</p>
            </div>
            <a href="{{ route('marketing.services') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-violet-700 transition hover:gap-3 dark:text-violet-300">
                {{ __('marketing.common.view_all_services') }} <span aria-hidden="true">→</span>
            </a>
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($services as $slug => $service)
                <x-marketing.service-card :slug="$slug" :service="$service" :index="$loop->iteration" />
            @endforeach
        </div>
    </section>

    <section class="relative z-10 overflow-hidden border-y border-slate-200/70 bg-gradient-to-br from-violet-50 via-white to-cyan-50/70 px-6 py-20 dark:border-white/10 dark:from-[#13122a] dark:via-[#101326] dark:to-[#0d2029] sm:px-10 sm:py-28">
        <div class="pointer-events-none absolute -right-40 -top-48 size-[36rem] rounded-full border border-violet-400/10"></div>
        <div class="pointer-events-none absolute -right-24 -top-32 size-[24rem] rounded-full border border-cyan-400/10"></div>
        <div class="relative mx-auto grid max-w-7xl gap-10 lg:grid-cols-[.82fr_1.18fr] lg:gap-20">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ $content['why_eyebrow'] }}</p>
                <h2 class="marketing-heading mt-4 text-3xl font-semibold tracking-[-.045em] sm:text-5xl">{{ $content['why_title'] }}</h2>
                <p class="marketing-muted mt-5 max-w-lg leading-7">{{ $content['why_body'] }}</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($content['reasons'] as $reason)
                    <article class="marketing-glass rounded-2xl p-5 sm:p-6">
                        <span class="mb-5 grid size-9 place-items-center rounded-xl bg-violet-500/10 text-sm text-violet-700 dark:text-violet-300" aria-hidden="true">✦</span>
                        <h3 class="marketing-heading text-base font-semibold">{{ $reason['title'] }}</h3>
                        <p class="marketing-muted mt-2 text-sm leading-6">{{ $reason['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="relative z-10 mx-auto max-w-7xl px-6 py-20 sm:px-10 sm:py-28">
        <div class="max-w-2xl">
            <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ $content['process_eyebrow'] }}</p>
            <h2 class="marketing-heading mt-4 text-3xl font-semibold tracking-[-.045em] sm:text-5xl">{{ $content['process_title'] }}</h2>
        </div>
        <div class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            @foreach ($content['steps'] as $step)
                <article class="marketing-surface marketing-card rounded-2xl p-5">
                    <span class="marketing-gradient-text font-mono text-sm font-bold">0{{ $loop->iteration }}</span>
                    <h3 class="marketing-heading mt-5 text-lg font-semibold">{{ $step['title'] }}</h3>
                    <p class="marketing-muted mt-2 text-sm leading-6">{{ $step['body'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="relative z-10 px-6 pb-20 sm:px-10 sm:pb-28">
        <div class="relative mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-gradient-to-br from-violet-600 via-indigo-600 to-[#308baf] px-7 py-12 text-white shadow-[0_35px_100px_-45px_rgba(91,73,220,.65)] sm:px-12 sm:py-16 lg:px-16">
            <div class="pointer-events-none absolute -right-20 -top-48 size-[32rem] rounded-full border border-white/15"></div>
            <div class="pointer-events-none absolute -right-6 -top-32 size-[22rem] rounded-full border border-white/15"></div>
            <div class="relative flex flex-col justify-between gap-8 lg:flex-row lg:items-end">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-white/70">{{ __('marketing.common.get_in_touch') }}</p>
                    <h2 class="mt-4 text-3xl font-semibold tracking-[-.045em] sm:text-5xl">{{ $content['closing_title'] }}</h2>
                    <p class="mt-4 max-w-xl leading-7 text-white/75">{{ $content['closing_body'] }}</p>
                </div>
                <a href="{{ route('marketing.contact') }}" class="inline-flex w-fit shrink-0 items-center gap-3 rounded-xl bg-white px-5 py-3.5 text-sm font-bold text-slate-950 transition hover:-translate-y-0.5 hover:bg-violet-50">
                    {{ __('marketing.common.talk_to_us') }} <span aria-hidden="true">↗</span>
                </a>
            </div>
        </div>
    </section>
</x-layouts.marketing>
