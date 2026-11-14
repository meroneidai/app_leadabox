<x-layouts.app :title="__('invoices.new')">
    <x-page-header :title="__('invoices.new')">
        <x-slot:breadcrumbs>
            <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ __('invoices.title') }}
            </a>
        </x-slot:breadcrumbs>
    </x-page-header>

    <form method="POST" action="{{ route('invoices.store') }}" class="max-w-5xl">
        @csrf

        <x-invoice-form
            :customers="$customers"
            :services="$services"
            :projects="$projects"
            :company="$company"
            :issue-date="$issueDate"
            :payment-terms="$paymentTerms"
        />

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('common.create') }}</x-button>
            <x-button :href="route('invoices.index')" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>
</x-layouts.app>
