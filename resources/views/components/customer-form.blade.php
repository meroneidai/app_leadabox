@props(['customer' => null, 'company' => null])

@php $c = $customer; @endphp

<div class="space-y-6">
    <x-card :title="__('customers.identity')">
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-label for="name" :value="__('customers.name')" />
                <x-input name="name" :value="$c?->name" required autofocus />
                <x-input-error for="name" />
            </div>

            <div>
                <x-label for="type" :value="__('customers.type')" />
                <x-select name="type">
                    @foreach (\App\Enums\CustomerType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(old('type', $c?->type?->value ?? 'company') === $type->value)>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </x-select>
                <x-input-error for="type" />
            </div>

            <div>
                <x-label for="status" :value="__('common.status')" />
                <x-select name="status">
                    @foreach (\App\Enums\CustomerStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $c?->status?->value ?? 'prospect') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </x-select>
                <x-input-error for="status" />
            </div>

            <div>
                <x-label for="org_number" :value="__('customers.org_number')" />
                <x-input name="org_number" :value="$c?->org_number" placeholder="556677-8899" />
                <x-input-error for="org_number" />
            </div>

            <div>
                <x-label for="vat_number" :value="__('customers.vat_number')" />
                <x-input name="vat_number" :value="$c?->vat_number" placeholder="SE556677889901" />
                <x-input-error for="vat_number" />
            </div>
        </div>
    </x-card>

    <x-card :title="__('customers.contact_details')">
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <x-label for="email" :value="__('common.email')" />
                <x-input name="email" type="email" :value="$c?->email" />
                <x-input-error for="email" />
            </div>

            <div>
                <x-label for="phone" :value="__('common.phone')" />
                <x-input name="phone" :value="$c?->phone" />
                <x-input-error for="phone" />
            </div>

            <div class="sm:col-span-2">
                <x-label for="website" :value="__('customers.website')" :hint="__('common.optional')" />
                <x-input name="website" type="url" :value="$c?->website" placeholder="https://" />
                <x-input-error for="website" />
            </div>

            <div class="sm:col-span-2">
                <x-label for="address_line1" :value="__('customers.address_line1')" />
                <x-input name="address_line1" :value="$c?->address_line1" />
                <x-input-error for="address_line1" />
            </div>

            <div class="sm:col-span-2">
                <x-label for="address_line2" :value="__('customers.address_line2')" :hint="__('common.optional')" />
                <x-input name="address_line2" :value="$c?->address_line2" />
            </div>

            <div>
                <x-label for="postal_code" :value="__('customers.postal_code')" />
                <x-input name="postal_code" :value="$c?->postal_code" />
            </div>

            <div>
                <x-label for="city" :value="__('customers.city')" />
                <x-input name="city" :value="$c?->city" />
            </div>

            <div>
                <x-label for="country" :value="__('customers.country')" />
                <x-input name="country" :value="$c?->country ?? 'SE'" maxlength="2" class="uppercase" />
                <x-input-error for="country" />
            </div>
        </div>
    </x-card>

    <x-card :title="__('customers.billing')">
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <x-label for="payment_terms_days" :value="__('customers.payment_terms_days')" />
                <x-input name="payment_terms_days" type="number" min="0" max="365" :value="$c?->payment_terms_days" />
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                    {{ __('customers.payment_terms_hint', ['days' => $company?->payment_terms_days ?? 30]) }}
                </p>
                <x-input-error for="payment_terms_days" />
            </div>

            <div>
                <x-label for="vat_rate" :value="__('customers.vat_rate')" />
                <x-input name="vat_rate" type="number" step="0.01" min="0" max="100" :value="$c?->vat_rate" />
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                    {{ __('customers.vat_rate_hint', ['rate' => $company?->default_vat_rate ?? 25]) }}
                </p>
                <x-input-error for="vat_rate" />
            </div>

            <div class="sm:col-span-2">
                <label class="flex items-start gap-2.5 text-sm text-slate-700 dark:text-slate-300">
                    <input type="checkbox" name="reverse_charge" value="1" @checked(old('reverse_charge', $c?->reverse_charge))
                           class="mt-0.5 size-4 rounded border-slate-300 text-navy-600 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800">
                    <span>
                        {{ __('customers.reverse_charge') }}
                        <span class="block text-xs text-slate-500 dark:text-slate-400">{{ __('customers.reverse_charge_hint') }}</span>
                    </span>
                </label>
            </div>

            <div class="sm:col-span-2">
                <x-label for="notes" :value="__('customers.notes')" :hint="__('common.optional')" />
                <x-textarea name="notes" rows="3" :value="$c?->notes" />
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ __('customers.notes_hint') }}</p>
                <x-input-error for="notes" />
            </div>
        </div>
    </x-card>

    {{ $slot }}
</div>
