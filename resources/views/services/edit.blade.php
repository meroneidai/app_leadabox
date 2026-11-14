<x-layouts.app :title="__('services.edit')">
    <x-page-header :title="__('services.edit')" :subtitle="$service->name">
        <x-slot:breadcrumbs>
            <a href="{{ route('services.index') }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ __('services.title') }}
            </a>
        </x-slot:breadcrumbs>
    </x-page-header>

    <form method="POST" action="{{ route('services.update', $service) }}" class="max-w-3xl">
        @csrf
        @method('PUT')

        <x-service-form :service="$service" :categories="$categories" :company="$company" />

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('common.save_changes') }}</x-button>
            <x-button :href="route('services.index')" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>
</x-layouts.app>
