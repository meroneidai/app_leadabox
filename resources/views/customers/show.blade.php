<x-layouts.app :title="$customer->name">
    <x-page-header :title="$customer->name" :subtitle="'#'.$customer->number">
        <x-slot:breadcrumbs>
            <a href="{{ route('customers.index') }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ __('customers.title') }}
            </a>
        </x-slot:breadcrumbs>

        <x-slot:actions>
            <x-badge :badge="$customer->status" />
            @can('update', $customer)
                <x-button :href="route('customers.edit', $customer)" variant="secondary">{{ __('common.edit') }}</x-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card :title="__('customers.contacts.title')" :subtitle="__('customers.contacts.subtitle')" padding="p-0">
                @forelse ($customer->contacts as $contact)
                    <div class="border-b border-slate-100 px-6 py-4 last:border-0 dark:border-slate-800">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-medium">{{ $contact->name }}</p>
                                    @if ($contact->is_primary)
                                        <x-badge color="navy">{{ __('customers.contacts.is_primary') }}</x-badge>
                                    @endif
                                </div>
                                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $contact->job_title }}
                                    @if ($contact->job_title && $contact->email) · @endif
                                    {{ $contact->email }}
                                    @if ($contact->phone) · {{ $contact->phone }} @endif
                                </p>

                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach ([
                                        'receives_quotations' => __('customers.contacts.receives_quotations'),
                                        'receives_invoices' => __('customers.contacts.receives_invoices'),
                                        'receives_project_updates' => __('customers.contacts.receives_project_updates'),
                                        'receives_renewals' => __('customers.contacts.receives_renewals'),
                                    ] as $field => $label)
                                        @if ($contact->{$field})
                                            <x-badge color="slate">{{ $label }}</x-badge>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @can('update', $customer)
                                <div x-data="{ open: false }" class="shrink-0">
                                    <x-button type="button" variant="ghost" size="sm" @click="open = !open">
                                        {{ __('common.edit') }}
                                    </x-button>

                                    <div x-show="open" x-cloak class="mt-3 w-full">
                                        <form method="POST" action="{{ route('contacts.update', $contact) }}" class="space-y-3 rounded-lg border border-slate-200 p-4 dark:border-slate-800">
                                            @csrf
                                            @method('PUT')
                                            <x-input name="name" :value="$contact->name" required />
                                            <x-input name="email" type="email" :value="$contact->email" />
                                            <x-input name="phone" :value="$contact->phone" />
                                            <x-input name="job_title" :value="$contact->job_title" />

                                            <div class="space-y-1.5">
                                                @foreach ([
                                                    'is_primary' => __('customers.contacts.primary'),
                                                    'receives_quotations' => __('customers.contacts.receives_quotations'),
                                                    'receives_invoices' => __('customers.contacts.receives_invoices'),
                                                    'receives_project_updates' => __('customers.contacts.receives_project_updates'),
                                                    'receives_renewals' => __('customers.contacts.receives_renewals'),
                                                ] as $field => $label)
                                                    <label class="flex items-center gap-2 text-sm">
                                                        <input type="checkbox" name="{{ $field }}" value="1" @checked($contact->{$field})
                                                               class="size-4 rounded border-slate-300 text-navy-600 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800">
                                                        {{ $label }}
                                                    </label>
                                                @endforeach
                                            </div>

                                            <x-button type="submit" size="sm">{{ __('common.save') }}</x-button>
                                        </form>

                                        <form method="POST" action="{{ route('contacts.destroy', $contact) }}" class="mt-2"
                                              onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-slate-400 transition hover:text-rose-600">
                                                {{ __('common.remove') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                @empty
                    <x-empty-state :title="__('customers.contacts.empty')" />
                @endforelse

                @can('update', $customer)
                    <div class="border-t border-slate-200 px-6 py-5 dark:border-slate-800" x-data="{ open: false }">
                        <x-button type="button" variant="secondary" size="sm" @click="open = !open">
                            {{ __('customers.contacts.add') }}
                        </x-button>

                        <form x-show="open" x-cloak method="POST" action="{{ route('customers.contacts.store', $customer) }}" class="mt-4 space-y-4">
                            @csrf

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <x-label for="name" :value="__('customers.contacts.name')" />
                                    <x-input name="name" required />
                                    <x-input-error for="name" />
                                </div>
                                <div>
                                    <x-label for="email" :value="__('common.email')" />
                                    <x-input name="email" type="email" />
                                    <x-input-error for="email" />
                                </div>
                                <div>
                                    <x-label for="phone" :value="__('common.phone')" />
                                    <x-input name="phone" />
                                </div>
                                <div>
                                    <x-label for="job_title" :value="__('customers.contacts.job_title')" />
                                    <x-input name="job_title" />
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-4">
                                @foreach ([
                                    'is_primary' => __('customers.contacts.primary'),
                                    'receives_quotations' => __('customers.contacts.receives_quotations'),
                                    'receives_invoices' => __('customers.contacts.receives_invoices'),
                                    'receives_project_updates' => __('customers.contacts.receives_project_updates'),
                                    'receives_renewals' => __('customers.contacts.receives_renewals'),
                                ] as $field => $label)
                                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                        <input type="checkbox" name="{{ $field }}" value="1"
                                               class="size-4 rounded border-slate-300 text-navy-600 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>

                            <x-button type="submit" size="sm">{{ __('customers.contacts.add') }}</x-button>
                        </form>
                    </div>
                @endcan
            </x-card>

            <x-card :title="__('customers.projects')" padding="p-0">
                @forelse ($customer->projects as $project)
                    <a href="{{ route('projects.show', $project) }}" class="flex items-center justify-between gap-3 border-b border-slate-100 px-6 py-3.5 transition last:border-0 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50">
                        <span class="truncate font-medium">{{ $project->name }}</span>
                        <x-badge :badge="$project->status" />
                    </a>
                @empty
                    <x-empty-state :title="__('customers.no_projects')" />
                @endforelse
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card :title="__('customers.billing')">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('customers.org_number') }}</dt>
                        <dd class="text-right font-medium">{{ $customer->org_number ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('customers.vat_number') }}</dt>
                        <dd class="text-right font-medium">{{ $customer->vat_number ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('customers.payment_terms_days') }}</dt>
                        <dd class="text-right font-medium">{{ $customer->paymentTermsDays() }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500 dark:text-slate-400">{{ __('customers.vat_rate') }}</dt>
                        <dd class="text-right font-medium">
                            {{ $customer->vatRate() }}%
                            @if ($customer->reverse_charge)
                                <span class="block text-xs font-normal text-slate-500">{{ __('customers.reverse_charge') }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                @if ($customer->addressLines())
                    <address class="mt-5 border-t border-slate-200 pt-4 text-sm not-italic text-slate-600 dark:border-slate-800 dark:text-slate-400">
                        @foreach ($customer->addressLines() as $line)
                            <span class="block">{{ $line }}</span>
                        @endforeach
                    </address>
                @endif

                @if ($customer->email || $customer->phone || $customer->website)
                    <div class="mt-4 space-y-1 border-t border-slate-200 pt-4 text-sm dark:border-slate-800">
                        @if ($customer->email)
                            <a href="mailto:{{ $customer->email }}" class="block truncate text-navy-600 hover:underline dark:text-navy-300">{{ $customer->email }}</a>
                        @endif
                        @if ($customer->phone)
                            <span class="block text-slate-600 dark:text-slate-400">{{ $customer->phone }}</span>
                        @endif
                        @if ($customer->website)
                            <a href="{{ $customer->website }}" target="_blank" rel="noopener" class="block truncate text-navy-600 hover:underline dark:text-navy-300">{{ $customer->website }}</a>
                        @endif
                    </div>
                @endif
            </x-card>

            @if ($customer->notes)
                <x-card :title="__('customers.notes')">
                    <p class="whitespace-pre-line text-sm text-slate-600 dark:text-slate-400">{{ $customer->notes }}</p>
                </x-card>
            @endif
        </div>
    </div>
</x-layouts.app>
