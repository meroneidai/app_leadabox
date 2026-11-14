<x-layouts.app :title="__('projects.tasks.edit')">
    <x-page-header :title="__('projects.tasks.edit')">
        <x-slot:breadcrumbs>
            <a href="{{ route('projects.show', $project) }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ $project->name }}
            </a>
        </x-slot:breadcrumbs>
    </x-page-header>

    <form method="POST" action="{{ route('tasks.update', $task) }}" class="max-w-2xl">
        @csrf
        @method('PUT')

        <x-card>
            <div class="space-y-5">
                <div>
                    <x-label for="title" :value="__('projects.tasks.name')" />
                    <x-input name="title" :value="$task->title" required autofocus />
                    <x-input-error for="title" />
                </div>

                <div>
                    <x-label for="description" :value="__('projects.tasks.details')" :hint="__('common.optional')" />
                    <x-textarea name="description" :value="$task->description" />
                    <x-input-error for="description" />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-label for="status" :value="__('common.status')" />
                        <x-select name="status">
                            @foreach (\App\Enums\TaskStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $task->status->value) === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input-error for="status" />
                    </div>

                    <div>
                        <x-label for="priority" :value="__('projects.tasks.priority')" />
                        <x-select name="priority">
                            @foreach (\App\Enums\TaskPriority::cases() as $priority)
                                <option value="{{ $priority->value }}" @selected(old('priority', $task->priority->value) === $priority->value)>
                                    {{ $priority->label() }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input-error for="priority" />
                    </div>

                    <div>
                        <x-label for="assigned_to" :value="__('projects.tasks.assignee')" />
                        <x-select name="assigned_to">
                            <option value="">{{ __('projects.tasks.unassigned') }}</option>
                            @foreach ($assignableUsers as $user)
                                <option value="{{ $user->id }}" @selected(old('assigned_to', $task->assigned_to) == $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input-error for="assigned_to" />
                    </div>

                    <div>
                        <x-label for="due_date" :value="__('projects.due_date')" :hint="__('common.optional')" />
                        <x-input name="due_date" type="date" :value="$task->due_date?->format('Y-m-d')" />
                        <x-input-error for="due_date" />
                    </div>
                </div>
            </div>
        </x-card>

        <div class="mt-5 flex items-center gap-3">
            <x-button type="submit">{{ __('projects.tasks.save') }}</x-button>
            <x-button :href="route('projects.show', $project)" variant="ghost">{{ __('common.cancel') }}</x-button>
        </div>
    </form>
</x-layouts.app>
