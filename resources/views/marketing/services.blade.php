<x-layouts.marketing
    :title="__('marketing.seo.services_title')"
    :description="__('marketing.seo.services_description')"
    :canonical="$canonical"
>
    <section class="relative z-10 mx-auto max-w-7xl px-6 pb-16 pt-16 sm:px-10 sm:pb-20">
        <div class="hero-enter max-w-3xl">
            <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ $content['eyebrow'] }}</p>
            <h1 class="marketing-heading mt-5 text-[clamp(2.8rem,6vw,5rem)] font-semibold leading-[.98] tracking-[-.065em]">{{ $content['title'] }}</h1>
            <p class="marketing-muted mt-6 max-w-2xl text-lg leading-8">{{ $content['intro'] }}</p>
        </div>
        <div class="mt-12 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($content['cards'] as $slug => $service)
                <x-marketing.service-card :slug="$slug" :service="$service" :index="$loop->iteration" />
            @endforeach
        </div>
    </section>
    <section class="relative z-10 border-y border-slate-200/70 bg-gradient-to-br from-violet-50 via-white to-cyan-50/70 px-6 py-16 dark:border-white/10 dark:from-[#13122a] dark:via-[#101326] dark:to-[#0d2029] sm:px-10 sm:py-20">
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-8 sm:flex-row sm:items-center">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-violet-700 dark:text-violet-300">{{ __('marketing.common.our_approach') }}</p>
                <h2 class="marketing-heading mt-4 text-3xl font-semibold tracking-[-.04em]">{{ __('marketing.home.process_title') }}</h2>
                <p class="marketing-muted mt-3 leading-7">{{ __('marketing.home.services_body') }}</p>
            </div>
            <a href="{{ route('marketing.contact') }}" class="inline-flex w-fit items-center gap-3 rounded-xl bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-violet-700 dark:bg-white dark:text-slate-950 dark:hover:bg-violet-100">
                {{ __('marketing.common.talk_to_us') }} <span aria-hidden="true">↗</span>
            </a>
        </div>
    </section>
</x-layouts.marketing>
