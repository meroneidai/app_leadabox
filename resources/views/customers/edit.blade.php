<x-layouts.app :title="__('customers.edit')">
    <x-page-header :title="__('customers.edit')" :subtitle="$customer->name">
        <x-slot:breadcrumbs>
            <a href="{{ route('customers.show', $customer) }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ $customer->name }}
            </a>
        </x-slot:breadcrumbs>
    </x-page-header>

    <form method="POST" action="{{ route('customers.update', $customer) }}" class="max-w-3xl">
        @csrf
        @method('PUT')

        <x-customer-form :customer="$customer" :company="\App\Models\Company::current()" />

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('common.save_changes') }}</x-button>
            <x-button :href="route('customers.show', $customer)" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>

    @can('delete', $customer)
        <div class="mt-8 max-w-3xl rounded-xl border border-rose-200 bg-rose-50 p-6 dark:border-rose-500/30 dark:bg-rose-500/10">
            <h2 class="font-semibold text-rose-900 dark:text-rose-200">{{ __('customers.delete_title') }}</h2>
            <p class="mt-1 text-sm text-rose-700 dark:text-rose-300">{{ __('customers.delete_warning') }}</p>

            <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="mt-4"
                  onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">{{ __('customers.delete_title') }}</x-button>
            </form>
        </div>
    @endcan
</x-layouts.app>
