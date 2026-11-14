<x-layouts.app :title="__('projects.new')">
    <x-page-header :title="__('projects.new')" />

    <form method="POST" action="{{ route('projects.store') }}" class="max-w-2xl">
        @csrf

        <x-card>
            <div class="space-y-5">
                <div>
                    <x-label for="name" :value="__('projects.name')" />
                    <x-input name="name" required autofocus />
                    <x-input-error for="name" />
                </div>

                <div>
                    <x-label for="description" :value="__('projects.description')" :hint="__('common.optional')" />
                    <x-textarea name="description" />
                    <x-input-error for="description" />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-label for="status" :value="__('common.status')" />
                        <x-select name="status">
                            @foreach (\App\Enums\ProjectStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected(old('status', 'active') === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input-error for="status" />
                    </div>

                    <div>
                        <x-label for="due_date" :value="__('projects.due_date')" :hint="__('common.optional')" />
                        <x-input name="due_date" type="date" />
                        <x-input-error for="due_date" />
                    </div>
                </div>
            </div>
        </x-card>

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('common.create') }}</x-button>
            <x-button :href="route('projects.index')" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>
</x-layouts.app>
