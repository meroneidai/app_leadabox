<x-layouts.app :title="__('services.categories.title')">
    <x-page-header :title="__('services.categories.title')" :subtitle="__('services.categories.subtitle')">
        <x-slot:breadcrumbs>
            <a href="{{ route('services.index') }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ __('services.title') }}
            </a>
        </x-slot:breadcrumbs>
    </x-page-header>

    <div class="grid max-w-4xl gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2" :title="__('services.categories.title')" padding="p-0">
            @if ($categories->isEmpty())
                <x-empty-state :title="__('services.categories.empty')" />
            @else
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($categories as $category)
                        <li class="px-6 py-4" x-data="{ editing: false }">
                            <div class="flex flex-wrap items-center justify-between gap-3" x-show="!editing">
                                <div>
                                    <p class="font-medium">{{ $category->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ trans_choice('services.categories.count', $category->services_count, ['count' => $category->services_count]) }}
                                    </p>
                                </div>

                                @can('create', \App\Models\Service::class)
                                    <div class="flex items-center gap-2">
                                        <x-button type="button" variant="ghost" size="sm" @click="editing = true">
                                            {{ __('common.edit') }}
                                        </x-button>

                                        @if ($category->services_count === 0)
                                            <form method="POST" action="{{ route('service-categories.destroy', $category) }}"
                                                  onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-medium text-slate-400 transition hover:text-rose-600">
                                                    {{ __('common.delete') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endcan
                            </div>

                            <form x-show="editing" x-cloak method="POST" action="{{ route('service-categories.update', $category) }}"
                                  class="flex flex-wrap items-end gap-3">
                                @csrf
                                @method('PUT')

                                <div class="min-w-48 flex-1">
                                    <x-label for="name" :value="__('services.categories.name')" />
                                    <x-input name="name" :value="$category->name" required />
                                </div>

                                <div class="w-28">
                                    <x-label for="sort_order" :value="__('services.sort_order')" />
                                    <x-input name="sort_order" type="number" min="0" :value="$category->sort_order" />
                                </div>

                                <x-button type="submit" size="sm">{{ __('common.save') }}</x-button>
                                <x-button type="button" variant="ghost" size="sm" @click="editing = false">{{ __('common.cancel') }}</x-button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        @can('create', \App\Models\Service::class)
            <x-card :title="__('services.categories.add')">
                <form method="POST" action="{{ route('service-categories.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-label for="name" :value="__('services.categories.name')" />
                        <x-input name="name" required />
                        <x-input-error for="name" />
                    </div>

                    <div>
                        <x-label for="sort_order" :value="__('services.sort_order')" :hint="__('common.optional')" />
                        <x-input name="sort_order" type="number" min="0" value="0" />
                        <x-input-error for="sort_order" />
                    </div>

                    <x-input-error for="category" />

                    <x-button type="submit" class="w-full">{{ __('common.create') }}</x-button>
                </form>
            </x-card>
        @endcan
    </div>
</x-layouts.app>
