<x-layouts.app :title="__('invoices.edit')">
    <x-page-header :title="__('invoices.edit')" :subtitle="$invoice->reference()">
        <x-slot:breadcrumbs>
            <a href="{{ route('invoices.show', $invoice) }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ $invoice->reference() }}
            </a>
        </x-slot:breadcrumbs>
    </x-page-header>

    <form method="POST" action="{{ route('invoices.update', $invoice) }}" class="max-w-5xl">
        @csrf
        @method('PUT')

        <x-invoice-form
            :invoice="$invoice"
            :customers="$customers"
            :services="$services"
            :projects="$projects"
            :company="$company"
        />

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('common.save_changes') }}</x-button>
            <x-button :href="route('invoices.show', $invoice)" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>
</x-layouts.app>
