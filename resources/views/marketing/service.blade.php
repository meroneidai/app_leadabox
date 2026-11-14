<x-layouts.marketing
    :title="$content['seo_title']"
    :description="$content['seo_description']"
    :canonical="$canonical"
>
    @php
        $serviceJsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $content['eyebrow'],
            'description' => $content['intro'],
            'provider' => ['@id' => 'https://leadabox.com/#organization'],
            'areaServed' => ['@type' => 'Country', 'name' => 'Sweden'],
            'url' => $canonical,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    @endphp
    @push('structured-data')<script type="application/ld+json">{!! $serviceJsonLd !!}</script>@endpush

    <section class="relative z-10 mx-auto max-w-7xl px-6 pb-14 pt-16 sm:px-10 sm:pb-20">
        <a href="{{ route('marketing.services') }}" class="marketing-muted inline-flex items-center gap-2 text-sm font-semibold transition hover:text-violet-700 dark:hover:text-violet-300"><span aria-hidden="true">←</span> {{ __('marketing.common.back_to_services') }}</a>
        <div class="mt-9 grid gap-10 lg:grid-cols-[1.12fr_.88fr] lg:items-center">
            <div class="hero-enter max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ $content['eyebrow'] }}</p>
                <h1 class="marketing-heading mt-5 text-[clamp(2.8rem,6vw,5rem)] font-semibold leading-[.98] tracking-[-.065em]">{{ $content['title'] }}</h1>
                <p class="marketing-muted mt-6 text-lg leading-8">{{ $content['intro'] }}</p>
                <a href="{{ route('marketing.contact') }}" class="mt-8 inline-flex items-center gap-3 rounded-xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-violet-700 dark:bg-white dark:text-slate-950 dark:hover:bg-violet-100">
                    {{ __('marketing.common.talk_to_us') }} <span aria-hidden="true">↗</span>
                </a>
            </div>
            <div class="hero-art relative mx-auto w-full max-w-[34rem]">
                <div class="absolute inset-10 rounded-full bg-gradient-to-br from-violet-400/25 via-indigo-400/10 to-cyan-300/25 blur-[75px]"></div>
                <img src="{{ asset('images/leadabox-digital-studio.svg') }}" alt="{{ __('marketing.home.image_alt') }}" width="850" height="720" class="relative h-auto w-full">
            </div>
        </div>
    </section>

    <section class="relative z-10 border-y border-slate-200/70 bg-white/55 px-6 py-16 dark:border-white/10 dark:bg-white/[.025] sm:px-10 sm:py-20">
        <div class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[.8fr_1.2fr] lg:gap-20">
            <h2 class="marketing-heading text-3xl font-semibold leading-tight tracking-[-.04em] sm:text-4xl">{{ $content['capabilities_title'] }}</h2>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($content['capabilities'] as $capability)
                    <div class="marketing-surface flex items-start gap-3 rounded-2xl p-4 text-sm leading-6"><span class="mt-1 text-violet-600 dark:text-violet-300" aria-hidden="true">✦</span><span class="marketing-heading">{{ $capability }}</span></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="relative z-10 mx-auto grid max-w-7xl gap-8 px-6 py-20 sm:px-10 sm:py-24 lg:grid-cols-2 lg:gap-20">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ __('marketing.common.what_we_do') }}</p>
            <p class="marketing-muted mt-4 text-lg leading-8">{{ $content['body'] }}</p>
        </div>
        <div class="marketing-glass rounded-3xl p-7 sm:p-9">
            <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ __('marketing.common.how_we_work') }}</p>
            <h2 class="marketing-heading mt-4 text-2xl font-semibold tracking-[-.04em]">{{ $content['approach_title'] }}</h2>
            <p class="marketing-muted mt-4 leading-7">{{ $content['approach'] }}</p>
        </div>
    </section>

    <section class="relative z-10 px-6 pb-20 sm:px-10 sm:pb-28">
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-8 rounded-3xl bg-gradient-to-br from-violet-600 via-indigo-600 to-[#308baf] p-7 text-white shadow-[0_35px_100px_-45px_rgba(91,73,220,.65)] sm:p-10 lg:flex-row lg:items-center">
            <div class="max-w-2xl"><h2 class="text-3xl font-semibold tracking-[-.04em]">{{ __('marketing.home.closing_title') }}</h2><p class="mt-3 leading-7 text-white/75">{{ __('marketing.home.closing_body') }}</p></div>
            <a href="{{ route('marketing.contact') }}" class="inline-flex w-fit shrink-0 items-center gap-3 rounded-xl bg-white px-5 py-3.5 text-sm font-bold text-slate-950 transition hover:-translate-y-0.5 hover:bg-violet-50">{{ __('marketing.common.talk_to_us') }} <span aria-hidden="true">↗</span></a>
        </div>
    </section>
</x-layouts.marketing>
