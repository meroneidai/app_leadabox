@props(['service' => null, 'categories', 'company' => null])

@php $s = $service; @endphp

<div
    class="space-y-6"
    x-data="{ billingType: '{{ old('billing_type', $s?->billing_type?->value ?? 'one_time') }}' }"
>
    <x-card :title="__('services.singular')">
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-label for="name" :value="__('services.name')" />
                <x-input name="name" :value="$s?->name" required autofocus />
                <x-input-error for="name" />
            </div>

            <div>
                <x-label for="code" :value="__('services.code')" :hint="__('common.optional')" />
                <x-input name="code" :value="$s?->code" placeholder="WEB-DEV" class="uppercase" />
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ __('services.code_hint') }}</p>
                <x-input-error for="code" />
            </div>

            <div>
                <x-label for="service_category_id" :value="__('services.category')" />
                <x-select name="service_category_id">
                    <option value="">{{ __('services.uncategorised') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('service_category_id', $s?->service_category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </x-select>
                <x-input-error for="service_category_id" />
            </div>

            <div class="sm:col-span-2">
                <x-label for="description" :value="__('services.description')" :hint="__('common.optional')" />
                <x-textarea name="description" rows="3" :value="$s?->description" />
                <x-input-error for="description" />
            </div>
        </div>
    </x-card>

    <x-card :title="__('services.pricing')">
        <div class="grid gap-5 sm:grid-cols-3">
            <div>
                <x-label for="price" :value="__('services.price')" />
                <x-input name="price" type="number" step="0.01" min="0" :value="$s?->price ?? '0.00'" required />
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ __('services.price_hint') }}</p>
                <x-input-error for="price" />
            </div>

            <div>
                <x-label for="cost" :value="__('services.cost')" :hint="__('common.optional')" />
                <x-input name="cost" type="number" step="0.01" min="0" :value="$s?->cost" />
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ __('services.cost_hint') }}</p>
                <x-input-error for="cost" />
            </div>

            <div>
                <x-label for="unit" :value="__('services.unit')" />
                <x-select name="unit">
                    @foreach (['item', 'hour', 'month', 'year'] as $unit)
                        <option value="{{ $unit }}" @selected(old('unit', $s?->unit ?? 'item') === $unit)>
                            {{ __('services.unit_'.$unit) }}
                        </option>
                    @endforeach
                </x-select>
                <x-input-error for="unit" />
            </div>

            <div class="sm:col-span-3">
                <x-label for="vat_rate" :value="__('services.vat_rate')" :hint="__('common.optional')" />
                <x-input name="vat_rate" type="number" step="0.01" min="0" max="100" :value="$s?->vat_rate" class="sm:max-w-48" />
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                    {{ __('services.vat_rate_hint', ['rate' => $company?->default_vat_rate ?? 25]) }}
                </p>
                <x-input-error for="vat_rate" />
            </div>
        </div>
    </x-card>

    <x-card :title="__('services.billing')">
        <div class="space-y-5">
            <div>
                <x-label for="billing_type" :value="__('services.billing_type')" />
                <div class="mt-2 grid gap-3 sm:grid-cols-2">
                    @foreach (\App\Enums\BillingType::cases() as $type)
                        <label
                            class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition"
                            :class="billingType === '{{ $type->value }}'
                                ? 'border-navy-500 bg-navy-50 dark:border-navy-400 dark:bg-navy-500/10'
                                : 'border-slate-200 hover:border-slate-300 dark:border-slate-800'"
                        >
                            <input type="radio" name="billing_type" value="{{ $type->value }}"
                                   x-model="billingType"
                                   class="mt-0.5 size-4 border-slate-300 text-navy-600 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800">
                            <span>
                                <span class="block text-sm font-medium">{{ $type->label() }}</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $type->description() }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                <x-input-error for="billing_type" />
            </div>

            <div x-show="billingType === 'recurring'" x-cloak x-transition>
                <x-label for="billing_interval" :value="__('services.billing_interval')" />
                <x-select name="billing_interval" class="sm:max-w-64">
                    @foreach (\App\Enums\BillingInterval::cases() as $interval)
                        <option value="{{ $interval->value }}"
                                @selected(old('billing_interval', $s?->billing_interval?->value ?? 'yearly') === $interval->value)>
                            {{ $interval->label() }}
                        </option>
                    @endforeach
                </x-select>
                <x-input-error for="billing_interval" />

                <label class="mt-4 flex items-start gap-2.5 text-sm text-slate-700 dark:text-slate-300">
                    <input type="checkbox" name="tracks_expiry" value="1" @checked(old('tracks_expiry', $s?->tracks_expiry))
                           class="mt-0.5 size-4 rounded border-slate-300 text-navy-600 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800">
                    <span>
                        {{ __('services.tracks_expiry') }}
                        <span class="block text-xs text-slate-500 dark:text-slate-400">{{ __('services.tracks_expiry_hint') }}</span>
                    </span>
                </label>
            </div>
        </div>
    </x-card>

    <x-card :title="__('services.visibility')">
        <div class="grid gap-5 sm:grid-cols-2">
            <label class="flex items-center gap-2.5 text-sm text-slate-700 dark:text-slate-300">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $s?->is_active ?? true))
                       class="size-4 rounded border-slate-300 text-navy-600 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800">
                {{ __('services.is_active') }}
            </label>

            <div>
                <x-label for="sort_order" :value="__('services.sort_order')" />
                <x-input name="sort_order" type="number" min="0" max="9999" :value="$s?->sort_order ?? 0" />
                <x-input-error for="sort_order" />
            </div>
        </div>
    </x-card>
</div>
