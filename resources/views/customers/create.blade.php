<x-layouts.app :title="__('customers.new')">
    <x-page-header :title="__('customers.new')">
        <x-slot:breadcrumbs>
            <a href="{{ route('customers.index') }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ __('customers.title') }}
            </a>
        </x-slot:breadcrumbs>
    </x-page-header>

    <form method="POST" action="{{ route('customers.store') }}" class="max-w-3xl">
        @csrf

        <x-customer-form :company="\App\Models\Company::current()">
            <x-card :title="__('customers.contacts.first_contact')" :subtitle="__('customers.contacts.first_contact_hint')">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-label for="contact_name" :value="__('customers.contacts.name')" />
                        <x-input name="contact_name" />
                        <x-input-error for="contact_name" />
                    </div>

                    <div>
                        <x-label for="contact_email" :value="__('common.email')" />
                        <x-input name="contact_email" type="email" />
                        <x-input-error for="contact_email" />
                    </div>

                    <div>
                        <x-label for="contact_phone" :value="__('common.phone')" />
                        <x-input name="contact_phone" />
                    </div>

                    <div>
                        <x-label for="contact_job_title" :value="__('customers.contacts.job_title')" />
                        <x-input name="contact_job_title" />
                    </div>
                </div>
            </x-card>
        </x-customer-form>

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('common.create') }}</x-button>
            <x-button :href="route('customers.index')" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>
</x-layouts.app>
