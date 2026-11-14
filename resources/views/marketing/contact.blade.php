<x-layouts.marketing
    :title="__('marketing.seo.contact_title')"
    :description="__('marketing.seo.contact_description')"
    :canonical="$canonical"
>
    @php
        $contactJsonLd = json_encode(['@context' => 'https://schema.org', '@type' => 'ContactPage', 'name' => __('marketing.seo.contact_title'), 'url' => $canonical, 'about' => ['@id' => 'https://leadabox.com/#organization']], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    @endphp
    @push('structured-data')<script type="application/ld+json">{!! $contactJsonLd !!}</script>@endpush

    <section class="relative z-10 mx-auto max-w-7xl px-6 pb-20 pt-16 sm:px-10 sm:pb-28">
        <div class="grid gap-12 lg:grid-cols-[.9fr_1.1fr] lg:gap-20">
            <div class="hero-enter">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ $content['eyebrow'] }}</p>
                <h1 class="marketing-heading mt-5 text-[clamp(2.8rem,6vw,5rem)] font-semibold leading-[.98] tracking-[-.065em]">{{ $content['title'] }}</h1>
                <p class="marketing-muted mt-6 max-w-xl text-lg leading-8">{{ $content['intro'] }}</p>
                <div class="mt-10 flex flex-wrap gap-3">
                    <a href="mailto:support@leadabox.com" class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-violet-700 dark:bg-white dark:text-slate-950 dark:hover:bg-violet-100"><span aria-hidden="true">✉</span> {{ __('marketing.common.email') }}</a>
                    <a href="https://wa.me/46764103340" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white/70 px-4 py-3 text-sm font-semibold text-slate-800 transition hover:-translate-y-0.5 hover:border-violet-400 dark:border-white/15 dark:bg-white/[.04] dark:text-white"><span aria-hidden="true">↗</span> {{ __('marketing.common.whatsapp') }}</a>
                </div>
            </div>
            <div class="marketing-glass relative overflow-hidden rounded-[2rem] p-7 shadow-[0_35px_100px_-60px_rgba(61,49,150,.5)] sm:p-10">
                <div class="pointer-events-none absolute -right-20 -top-28 size-72 rounded-full bg-violet-400/15 blur-3xl"></div>
                <div class="relative">
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ $content['project_title'] }}</p>
                    <p class="marketing-muted mt-4 leading-7">{{ $content['project_body'] }}</p>
                    <div class="mt-8 space-y-4">
                        <a href="mailto:support@leadabox.com" class="group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white/45 p-4 transition hover:border-violet-300 dark:border-white/10 dark:bg-white/[.035] dark:hover:border-violet-400/40">
                            <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-violet-500/10 text-violet-700 dark:text-violet-300" aria-hidden="true">✉</span>
                            <span><span class="marketing-muted block text-xs">{{ __('marketing.common.email_us') }}</span><span class="marketing-heading mt-1 block font-semibold">support@leadabox.com</span></span>
                        </a>
                        <a href="tel:+46764103340" class="group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white/45 p-4 transition hover:border-violet-300 dark:border-white/10 dark:bg-white/[.035] dark:hover:border-violet-400/40">
                            <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-cyan-500/10 text-cyan-700 dark:text-cyan-300" aria-hidden="true">☎</span>
                            <span><span class="marketing-muted block text-xs">{{ __('marketing.common.call_us') }}</span><span class="marketing-heading mt-1 block font-semibold">+46 76 410 33 40</span></span>
                        </a>
                        <a href="https://wa.me/46764103340" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-white/45 p-4 transition hover:border-violet-300 dark:border-white/10 dark:bg-white/[.035] dark:hover:border-violet-400/40">
                            <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-emerald-500/10 text-emerald-700 dark:text-emerald-300" aria-hidden="true">↗</span>
                            <span><span class="marketing-muted block text-xs">{{ __('marketing.common.open_whatsapp') }}</span><span class="marketing-heading mt-1 block font-semibold">+46 76 410 33 40</span></span>
                        </a>
                    </div>
                    <p class="marketing-muted mt-7 text-sm leading-6">{{ $content['response_note'] }}</p>
                </div>
            </div>
        </div>
        <div class="marketing-surface mt-12 rounded-3xl p-7 sm:p-9">
            <h2 class="marketing-heading text-xl font-semibold">{{ $content['location_title'] }}</h2>
            <p class="marketing-muted mt-3 max-w-3xl leading-7">{{ $content['location_body'] }}</p>
        </div>
    </section>
</x-layouts.marketing>
