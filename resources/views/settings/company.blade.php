<x-layouts.app :title="__('settings.title')">
    <x-page-header :title="__('settings.title')" :subtitle="__('settings.subtitle')" />

    <form method="POST" action="{{ route('settings.company.update') }}" class="max-w-4xl space-y-6">
        @csrf
        @method('PUT')

        <x-card :title="__('settings.identity')">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-label for="name" :value="__('settings.name')" />
                    <x-input name="name" :value="$company->name" required />
                    <x-input-error for="name" />
                </div>

                <div>
                    <x-label for="legal_name" :value="__('settings.legal_name')" />
                    <x-input name="legal_name" :value="$company->legal_name" />
                    <x-input-error for="legal_name" />
                </div>

                <div>
                    <x-label for="org_number" :value="__('settings.org_number')" />
                    <x-input name="org_number" :value="$company->org_number" placeholder="556677-8899" />
                    <x-input-error for="org_number" />
                </div>

                <div>
                    <x-label for="vat_number" :value="__('settings.vat_number')" />
                    <x-input name="vat_number" :value="$company->vat_number" placeholder="SE556677889901" />
                    <x-input-error for="vat_number" />
                </div>

                <label class="flex items-center gap-2.5 text-sm text-slate-700 sm:col-span-2 dark:text-slate-300">
                    <input type="checkbox" name="has_f_skatt" value="1" @checked(old('has_f_skatt', $company->has_f_skatt))
                           class="size-4 rounded border-slate-300 text-navy-600 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800">
                    {{ __('settings.has_f_skatt') }}
                </label>
            </div>
        </x-card>

        <x-card :title="__('settings.contact')">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-label for="email" :value="__('common.email')" />
                    <x-input name="email" type="email" :value="$company->email" />
                    <x-input-error for="email" />
                </div>

                <div>
                    <x-label for="phone" :value="__('common.phone')" />
                    <x-input name="phone" :value="$company->phone" />
                    <x-input-error for="phone" />
                </div>

                <div class="sm:col-span-2">
                    <x-label for="website" :value="__('settings.website')" />
                    <x-input name="website" type="url" :value="$company->website" placeholder="https://" />
                    <x-input-error for="website" />
                </div>

                <div class="sm:col-span-2">
                    <x-label for="address_line1" :value="__('settings.address_line1')" />
                    <x-input name="address_line1" :value="$company->address_line1" />
                    <x-input-error for="address_line1" />
                </div>

                <div class="sm:col-span-2">
                    <x-label for="address_line2" :value="__('settings.address_line2')" :hint="__('common.optional')" />
                    <x-input name="address_line2" :value="$company->address_line2" />
                </div>

                <div>
                    <x-label for="postal_code" :value="__('settings.postal_code')" />
                    <x-input name="postal_code" :value="$company->postal_code" />
                </div>

                <div>
                    <x-label for="city" :value="__('settings.city')" />
                    <x-input name="city" :value="$company->city" />
                </div>

                <div>
                    <x-label for="country" :value="__('settings.country')" />
                    <x-input name="country" :value="$company->country" maxlength="2" class="uppercase" />
                    <x-input-error for="country" />
                </div>
            </div>
        </x-card>

        <x-card :title="__('settings.financial')">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-label for="currency" :value="__('settings.currency')" />
                    <x-input name="currency" :value="$company->currency" maxlength="3" class="uppercase" />
                    <x-input-error for="currency" />
                </div>

                <div>
                    <x-label for="locale" :value="__('settings.locale')" />
                    <x-select name="locale">
                        @foreach ($locales as $code => $label)
                            <option value="{{ $code }}" @selected(old('locale', $company->locale) === $code)>{{ $label }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="locale" />
                </div>

                <div>
                    <x-label for="default_vat_rate" :value="__('settings.default_vat_rate')" />
                    <x-input name="default_vat_rate" type="number" step="0.01" min="0" max="100" :value="$company->default_vat_rate" />
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ __('settings.default_vat_rate_hint') }}</p>
                    <x-input-error for="default_vat_rate" />
                </div>

                <div>
                    <x-label for="payment_terms_days" :value="__('settings.payment_terms_days')" />
                    <x-input name="payment_terms_days" type="number" min="0" max="365" :value="$company->payment_terms_days" />
                    <x-input-error for="payment_terms_days" />
                </div>
            </div>
        </x-card>

        <x-card :title="__('settings.numbering')">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-label for="quotation_prefix" :value="__('settings.quotation_prefix')" />
                    <x-input name="quotation_prefix" :value="$company->quotation_prefix" />
                    <x-input-error for="quotation_prefix" />
                </div>

                <div>
                    <x-label for="quotation_next_number" :value="__('settings.quotation_next_number')" />
                    <x-input name="quotation_next_number" type="number" min="1" :value="$company->quotation_next_number" />
                    <x-input-error for="quotation_next_number" />
                </div>

                <div>
                    <x-label for="quotation_valid_days" :value="__('settings.quotation_valid_days')" />
                    <x-input name="quotation_valid_days" type="number" min="1" max="365" :value="$company->quotation_valid_days" />
                    <x-input-error for="quotation_valid_days" />
                </div>

                <div>
                    <x-label for="invoice_prefix" :value="__('settings.invoice_prefix')" />
                    <x-input name="invoice_prefix" :value="$company->invoice_prefix" />
                    <x-input-error for="invoice_prefix" />
                </div>

                <div>
                    <x-label for="invoice_next_number" :value="__('settings.invoice_next_number')" />
                    <x-input name="invoice_next_number" type="number" min="1" :value="$company->invoice_next_number" />
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ __('settings.invoice_next_number_hint') }}</p>
                    <x-input-error for="invoice_next_number" />
                </div>
            </div>
        </x-card>

        <x-card :title="__('settings.banking')">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-label for="bankgiro" :value="__('settings.bankgiro')" />
                    <x-input name="bankgiro" :value="$company->bankgiro" />
                    <x-input-error for="bankgiro" />
                </div>

                <div>
                    <x-label for="plusgiro" :value="__('invoices.plusgiro')" />
                    <x-input name="plusgiro" :value="$company->plusgiro" />
                    <x-input-error for="plusgiro" />
                </div>

                <div>
                    <x-label for="bank_name" :value="__('settings.bank_name')" />
                    <x-input name="bank_name" :value="$company->bank_name" />
                </div>

                <div>
                    <x-label for="iban" :value="__('settings.iban')" />
                    <x-input name="iban" :value="$company->iban" />
                </div>

                <div>
                    <x-label for="bic" :value="__('settings.bic')" />
                    <x-input name="bic" :value="$company->bic" />
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-start gap-2.5 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" name="payment_qr_enabled" value="1" @checked(old('payment_qr_enabled', $company->payment_qr_enabled))
                               class="mt-0.5 size-4 rounded border-slate-300 text-navy-600 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800">
                        <span>
                            {{ __('invoices.payment_qr_enabled') }}
                            <span class="block text-xs text-slate-500 dark:text-slate-400">{{ __('invoices.payment_qr_hint') }}</span>
                        </span>
                    </label>
                </div>

                <div class="sm:col-span-2">
                    <x-label for="invoice_footer" :value="__('settings.invoice_footer')" :hint="__('common.optional')" />
                    <x-textarea name="invoice_footer" rows="3" :value="$company->invoice_footer" />
                    <x-input-error for="invoice_footer" />
                </div>
            </div>
        </x-card>

        <div class="flex items-center gap-3">
            <x-button type="submit">{{ __('common.save_changes') }}</x-button>
        </div>
    </form>
</x-layouts.app>
