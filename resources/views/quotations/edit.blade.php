<x-layouts.app :title="__('quotations.edit')">
    <x-page-header :title="__('quotations.edit')" :subtitle="$quotation->reference()">
        <x-slot:breadcrumbs>
            <a href="{{ route('quotations.show', $quotation) }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ $quotation->reference() }}
            </a>
        </x-slot:breadcrumbs>
    </x-page-header>

    <form method="POST" action="{{ route('quotations.update', $quotation) }}" class="max-w-5xl">
        @csrf
        @method('PUT')

        <x-quotation-form
            :quotation="$quotation"
            :customers="$customers"
            :services="$services"
            :company="$company"
        />

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('common.save_changes') }}</x-button>
            <x-button :href="route('quotations.show', $quotation)" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>
</x-layouts.app>
