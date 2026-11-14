@props(['invoice' => null, 'customers', 'services', 'projects', 'company', 'issueDate' => null, 'paymentTerms' => 30])

@php
    $i = $invoice;
    $defaultVat = (string) ($company?->default_vat_rate ?? '25.00');

    $existingLines = old('lines', $i?->lines->map(fn ($line) => [
        'service_id' => $line->service_id,
        'name' => $line->name,
        'description' => $line->description,
        'unit' => $line->unit,
        'quantity' => (string) $line->quantity,
        'unit_price' => (string) $line->unit_price,
        'discount_percent' => (string) $line->discount_percent,
        'vat_rate' => (string) $line->vat_rate,
    ])->all() ?? []);

    if (empty($existingLines)) {
        $existingLines = [[
            'service_id' => '', 'name' => '', 'description' => '', 'unit' => 'item',
            'quantity' => '1', 'unit_price' => '0.00', 'discount_percent' => '0', 'vat_rate' => $defaultVat,
        ]];
    }

    $catalogue = $services->map(fn ($service) => [
        'id' => $service->id,
        'name' => $service->name,
        'description' => $service->description,
        'unit' => $service->unit,
        'price' => (string) $service->price,
        'vat_rate' => $service->vatRate(),
    ])->values();

    $contactsByCustomer = $customers->mapWithKeys(fn ($customer) => [
        $customer->id => $customer->contacts->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'email' => $c->email])->values(),
    ]);
@endphp

<div
    class="space-y-6"
    x-data="documentForm({
        lines: {{ Js::from(array_values($existingLines)) }},
        catalogue: {{ Js::from($catalogue) }},
        contacts: {{ Js::from($contactsByCustomer) }},
        defaultVat: '{{ $defaultVat }}',
        customerId: '{{ old('customer_id', $i?->customer_id) }}',
        contactId: '{{ old('contact_id', $i?->contact_id) }}',
    })"
>
    <x-card :title="__('invoices.customer')">
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <x-label for="customer_id" :value="__('invoices.customer')" />
                <x-select name="customer_id" x-model="customerId" required>
                    <option value="">—</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="customer_id" />
            </div>

            <div>
                <x-label for="contact_id" :value="__('invoices.contact')" />
                <select name="contact_id" id="contact_id" x-model="contactId"
                        class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-xs transition
                               focus:border-navy-500 focus:outline-none focus:ring-2 focus:ring-navy-500/20
                               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                    <option value="">—</option>
                    <template x-for="contact in availableContacts()" :key="contact.id">
                        <option :value="contact.id" x-text="contact.name + (contact.email ? ' · ' + contact.email : '')"></option>
                    </template>
                </select>
                <x-input-error for="contact_id" />
            </div>

            <div>
                <x-label for="issue_date" :value="__('invoices.issue_date')" />
                <x-input name="issue_date" type="date" :value="$i?->issue_date?->format('Y-m-d') ?? $issueDate" required />
                <x-input-error for="issue_date" />
            </div>

            <div>
                <x-label for="payment_terms_days" :value="__('invoices.payment_terms_days')" />
                <x-input name="payment_terms_days" type="number" min="0" max="365"
                         :value="$i?->payment_terms_days ?? $paymentTerms" required />
                <x-input-error for="payment_terms_days" />
            </div>

            <div>
                <x-label for="title" :value="__('invoices.invoice_title')" :hint="__('common.optional')" />
                <x-input name="title" :value="$i?->title" />
                <x-input-error for="title" />
            </div>

            <div>
                <x-label for="customer_reference" :value="__('invoices.customer_reference')" :hint="__('common.optional')" />
                <x-input name="customer_reference" :value="$i?->customer_reference" />
                <x-input-error for="customer_reference" />
            </div>

            <div class="sm:col-span-2">
                <x-label for="project_id" :value="__('invoices.project')" :hint="__('common.optional')" />
                <x-select name="project_id">
                    <option value="">—</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected(old('project_id', $i?->project_id) == $project->id)>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </x-select>
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ __('invoices.project_hint') }}</p>
                <x-input-error for="project_id" />
            </div>

            <div class="sm:col-span-2">
                <x-label for="intro" :value="__('invoices.intro')" :hint="__('common.optional')" />
                <x-textarea name="intro" rows="2" :value="$i?->intro" />
                <x-input-error for="intro" />
            </div>
        </div>
    </x-card>

    <x-card :title="__('invoices.lines')" padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full min-w-4xl text-left text-sm">
                <thead class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-semibold">{{ __('invoices.line_name') }}</th>
                        <th scope="col" class="w-24 px-3 py-3 text-right font-semibold">{{ __('invoices.line_qty') }}</th>
                        <th scope="col" class="w-28 px-3 py-3 font-semibold">{{ __('invoices.line_unit') }}</th>
                        <th scope="col" class="w-36 px-3 py-3 text-right font-semibold">{{ __('invoices.line_price') }}</th>
                        <th scope="col" class="w-28 px-3 py-3 text-right font-semibold">{{ __('invoices.line_discount') }}</th>
                        <th scope="col" class="w-24 px-3 py-3 text-right font-semibold">{{ __('invoices.line_vat') }}</th>
                        <th scope="col" class="w-32 px-3 py-3 text-right font-semibold">{{ __('invoices.line_total') }}</th>
                        <th scope="col" class="w-12 px-3 py-3"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <template x-for="(line, index) in lines" :key="index">
                        <tr class="align-top">
                            <td class="px-4 py-3">
                                <select
                                    @change="applyService(index, $event.target.value)"
                                    class="mb-2 block w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-600
                                           dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                                >
                                    <option value="">{{ __('invoices.pick_service') }}</option>
                                    <template x-for="service in catalogue" :key="service.id">
                                        <option :value="service.id" x-text="service.name" :selected="line.service_id == service.id"></option>
                                    </template>
                                </select>

                                <input type="hidden" :name="`lines[${index}][service_id]`" :value="line.service_id ?? ''">

                                <input type="text" :name="`lines[${index}][name]`" x-model="line.name" required
                                       placeholder="{{ __('invoices.line_name') }}"
                                       class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                                              focus:border-navy-500 focus:outline-none focus:ring-2 focus:ring-navy-500/20
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">

                                <textarea :name="`lines[${index}][description]`" x-model="line.description" rows="2"
                                          class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs
                                                 focus:border-navy-500 focus:outline-none focus:ring-2 focus:ring-navy-500/20
                                                 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
                            </td>

                            <td class="px-3 py-3">
                                <input type="number" step="0.01" :name="`lines[${index}][quantity]`" x-model="line.quantity" required
                                       class="block w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-right text-sm tabular-nums
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            </td>

                            <td class="px-3 py-3">
                                <select :name="`lines[${index}][unit]`" x-model="line.unit"
                                        class="block w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm
                                               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                    @foreach (['item', 'hour', 'month', 'year'] as $unit)
                                        <option value="{{ $unit }}">{{ __('services.unit_'.$unit) }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="px-3 py-3">
                                <input type="number" step="0.01" min="0" :name="`lines[${index}][unit_price]`" x-model="line.unit_price" required
                                       class="block w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-right text-sm tabular-nums
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            </td>

                            <td class="px-3 py-3">
                                <input type="number" step="0.01" min="0" max="100" :name="`lines[${index}][discount_percent]`" x-model="line.discount_percent"
                                       class="block w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-right text-sm tabular-nums
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            </td>

                            <td class="px-3 py-3">
                                <input type="number" step="0.01" min="0" max="100" :name="`lines[${index}][vat_rate]`" x-model="line.vat_rate" required
                                       class="block w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-right text-sm tabular-nums
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            </td>

                            <td class="px-3 py-3 text-right text-sm font-medium tabular-nums" x-text="formatMoney(lineTotal(line))"></td>

                            <td class="px-3 py-3 text-right">
                                <button type="button" @click="removeLine(index)" x-show="lines.length > 1"
                                        class="rounded-md p-1.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-start justify-between gap-6 border-t border-slate-200 px-6 py-5 dark:border-slate-800">
            <x-button type="button" variant="secondary" size="sm" @click="addLine()">
                {{ __('invoices.add_line') }}
            </x-button>

            <dl class="w-full max-w-xs space-y-2 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500 dark:text-slate-400">{{ __('invoices.subtotal') }}</dt>
                    <dd class="tabular-nums" x-text="formatMoney(subtotal())"></dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500 dark:text-slate-400">{{ __('invoices.vat_total') }}</dt>
                    <dd class="tabular-nums" x-text="formatMoney(vatTotal())"></dd>
                </div>
                <div class="flex justify-between gap-4 border-t border-slate-200 pt-2 text-base font-semibold dark:border-slate-800">
                    <dt>{{ __('invoices.total') }}</dt>
                    <dd class="tabular-nums" x-text="formatMoney(grandTotal()) + ' {{ $company?->currency }}'"></dd>
                </div>
            </dl>
        </div>

        @error('lines')
            <p class="px-6 pb-5 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
        @enderror
    </x-card>

    <x-card :title="__('invoices.terms')">
        <div class="space-y-5">
            <div>
                <x-label for="terms" :value="__('invoices.terms')" :hint="__('common.optional')" />
                <x-textarea name="terms" rows="3" :value="$i?->terms" />
                <x-input-error for="terms" />
            </div>

            <div>
                <x-label for="internal_notes" :value="__('invoices.internal_notes')" :hint="__('common.optional')" />
                <x-textarea name="internal_notes" rows="2" :value="$i?->internal_notes" />
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ __('invoices.internal_notes_hint') }}</p>
                <x-input-error for="internal_notes" />
            </div>
        </div>
    </x-card>
</div>
