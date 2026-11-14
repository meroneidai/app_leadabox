<x-layouts.app :title="__('projects.edit')">
    <x-page-header :title="__('projects.edit')" :subtitle="$project->name" />

    <form method="POST" action="{{ route('projects.update', $project) }}" class="max-w-2xl">
        @csrf
        @method('PUT')

        <x-card>
            <div class="space-y-5">
                <div>
                    <x-label for="name" :value="__('projects.name')" />
                    <x-input name="name" :value="$project->name" required autofocus />
                    <x-input-error for="name" />
                </div>

                <div>
                    <x-label for="description" :value="__('projects.description')" :hint="__('common.optional')" />
                    <x-textarea name="description" :value="$project->description" />
                    <x-input-error for="description" />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-label for="status" :value="__('common.status')" />
                        <x-select name="status">
                            @foreach (\App\Enums\ProjectStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $project->status->value) === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input-error for="status" />
                    </div>

                    <div>
                        <x-label for="due_date" :value="__('projects.due_date')" :hint="__('common.optional')" />
                        <x-input name="due_date" type="date" :value="$project->due_date?->format('Y-m-d')" />
                        <x-input-error for="due_date" />
                    </div>
                </div>
            </div>
        </x-card>

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('common.save_changes') }}</x-button>
            <x-button :href="route('projects.show', $project)" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>

    @can('delete', $project)
        <div class="mt-8 max-w-2xl rounded-xl border border-rose-200 bg-rose-50 p-6 dark:border-rose-500/30 dark:bg-rose-500/10">
            <h2 class="font-semibold text-rose-900 dark:text-rose-200">{{ __('projects.delete_title') }}</h2>
            <p class="mt-1 text-sm text-rose-700 dark:text-rose-300">{{ __('projects.delete_warning') }}</p>

            <form method="POST" action="{{ route('projects.destroy', $project) }}" class="mt-4"
                  onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">{{ __('projects.delete_title') }}</x-button>
            </form>
        </div>
    @endcan
</x-layouts.app>
