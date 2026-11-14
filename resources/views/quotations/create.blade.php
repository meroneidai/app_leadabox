<x-layouts.app :title="__('quotations.new')">
    <x-page-header :title="__('quotations.new')">
        <x-slot:breadcrumbs>
            <a href="{{ route('quotations.index') }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ __('quotations.title') }}
            </a>
        </x-slot:breadcrumbs>
    </x-page-header>

    <form method="POST" action="{{ route('quotations.store') }}" class="max-w-5xl">
        @csrf

        <x-quotation-form
            :customers="$customers"
            :services="$services"
            :company="$company"
            :issue-date="$issueDate"
            :valid-until="$validUntil"
        />

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('common.create') }}</x-button>
            <x-button :href="route('quotations.index')" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>
</x-layouts.app>
