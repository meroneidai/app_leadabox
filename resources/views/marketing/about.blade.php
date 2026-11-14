<x-layouts.marketing
    :title="__('marketing.seo.about_title')"
    :description="__('marketing.seo.about_description')"
    :canonical="$canonical"
>
    @php
        $aboutJsonLd = json_encode(['@context' => 'https://schema.org', '@type' => 'AboutPage', 'name' => __('marketing.seo.about_title'), 'url' => $canonical, 'about' => ['@id' => 'https://leadabox.com/#organization']], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    @endphp
    @push('structured-data')<script type="application/ld+json">{!! $aboutJsonLd !!}</script>@endpush

    <section class="relative mx-auto grid max-w-7xl items-center gap-8 px-6 pb-20 pt-16 sm:px-10 sm:pb-28 lg:grid-cols-[1.05fr_.95fr]">
        <div class="hero-enter relative z-10 max-w-2xl">
            <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ $content['eyebrow'] }}</p>
            <h1 class="marketing-heading mt-5 text-[clamp(3rem,6vw,5.4rem)] font-semibold leading-[.98] tracking-[-.065em]">{{ $content['title'] }}</h1>
            <p class="marketing-muted mt-7 max-w-xl text-lg leading-8">{{ $content['intro'] }}</p>
            <a href="{{ route('marketing.contact') }}" class="mt-8 inline-flex items-center gap-3 rounded-xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-violet-700 dark:bg-white dark:text-slate-950 dark:hover:bg-violet-100">
                {{ __('marketing.common.talk_to_us') }} <span aria-hidden="true">↗</span>
            </a>
        </div>
        <div class="hero-art relative mx-auto w-full max-w-[38rem]">
            <div class="absolute inset-12 rounded-full bg-gradient-to-br from-violet-400/25 to-cyan-300/25 blur-[80px]"></div>
            <img src="{{ asset('images/leadabox-digital-studio.svg') }}" alt="{{ __('marketing.home.image_alt') }}" width="850" height="720" class="relative h-auto w-full">
        </div>
    </section>

    <section class="relative z-10 border-y border-slate-200/70 bg-white/55 px-6 py-16 dark:border-white/10 dark:bg-white/[.025] sm:px-10 sm:py-20">
        <div class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[.8fr_1.2fr] lg:gap-20">
            <h2 class="marketing-heading text-3xl font-semibold leading-tight tracking-[-.04em] sm:text-4xl">{{ $content['story_title'] }}</h2>
            <p class="marketing-muted max-w-3xl text-lg leading-8">{{ $content['story'] }}</p>
        </div>
    </section>

    <section class="relative z-10 mx-auto grid max-w-7xl gap-10 px-6 py-20 sm:px-10 sm:py-28 lg:grid-cols-[1fr_1fr] lg:gap-20">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ $content['mission_title'] }}</p>
            <p class="marketing-gradient-text mt-5 max-w-xl text-3xl font-semibold leading-tight tracking-[-.04em] sm:text-4xl">{{ $content['mission'] }}</p>
        </div>
        <div>
            <h2 class="marketing-heading text-2xl font-semibold leading-tight tracking-[-.04em] sm:text-3xl">{{ $content['belief_title'] }}</h2>
            <p class="marketing-muted mt-4 leading-7">{{ $content['belief'] }}</p>
        </div>
    </section>

    <section class="relative z-10 border-y border-slate-200/70 bg-gradient-to-br from-violet-50 via-white to-cyan-50/70 px-6 py-20 dark:border-white/10 dark:from-[#13122a] dark:via-[#101326] dark:to-[#0d2029] sm:px-10 sm:py-24">
        <div class="mx-auto max-w-7xl">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ __('marketing.common.our_approach') }}</p>
                <h2 class="marketing-heading mt-4 text-3xl font-semibold tracking-[-.045em] sm:text-5xl">{{ $content['story_title'] }}</h2>
            </div>
            <div class="mt-8 grid gap-4 md:grid-cols-3">
                @foreach ($content['values'] as $value)
                    <article class="marketing-glass rounded-2xl p-6">
                        <span class="marketing-gradient-text font-mono text-sm font-bold">0{{ $loop->iteration }}</span>
                        <h3 class="marketing-heading mt-5 text-lg font-semibold">{{ $value['title'] }}</h3>
                        <p class="marketing-muted mt-2 text-sm leading-6">{{ $value['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="relative z-10 px-6 py-20 sm:px-10 sm:py-24">
        <div class="marketing-surface mx-auto flex max-w-7xl flex-col justify-between gap-8 rounded-3xl p-7 sm:p-10 lg:flex-row lg:items-center">
            <div class="max-w-2xl">
                <h2 class="marketing-heading text-3xl font-semibold tracking-[-.04em]">{{ __('marketing.home.closing_title') }}</h2>
                <p class="marketing-muted mt-3 leading-7">{{ __('marketing.home.closing_body') }}</p>
            </div>
            <a href="{{ route('marketing.contact') }}" class="inline-flex w-fit shrink-0 items-center gap-3 rounded-xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-violet-700 dark:bg-white dark:text-slate-950 dark:hover:bg-violet-100">
                {{ __('marketing.common.get_in_touch') }} <span aria-hidden="true">↗</span>
            </a>
        </div>
    </section>
</x-layouts.marketing>
