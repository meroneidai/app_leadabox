<x-layouts.app :title="$project->name">
    <x-page-header :title="$project->name" :subtitle="$project->description">
        <x-slot:breadcrumbs>
            <a href="{{ route('projects.index') }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                &larr; {{ __('projects.title') }}
            </a>
        </x-slot:breadcrumbs>

        <x-slot:actions>
            <x-badge :badge="$project->status" />
            @can('update', $project)
                <x-button :href="route('projects.edit', $project)" variant="secondary">{{ __('common.edit') }}</x-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @can('update', $project)
                <x-card :title="__('projects.tasks.add')">
                    <form method="POST" action="{{ route('projects.tasks.store', $project) }}" class="space-y-5">
                        @csrf

                        <div>
                            <x-label for="title" :value="__('projects.tasks.name')" />
                            <x-input name="title" required :placeholder="__('projects.tasks.placeholder')" />
                            <x-input-error for="title" />
                        </div>

                        <div>
                            <x-label for="description" :value="__('projects.tasks.details')" :hint="__('common.optional')" />
                            <x-textarea name="description" rows="2" />
                            <x-input-error for="description" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <x-label for="status" :value="__('common.status')" />
                                <x-select name="status">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}" @selected(old('status', 'todo') === $status->value)>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>

                            <div>
                                <x-label for="priority" :value="__('projects.tasks.priority')" />
                                <x-select name="priority">
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->value }}" @selected(old('priority', 'medium') === $priority->value)>
                                            {{ $priority->label() }}
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>

                            <div>
                                <x-label for="assigned_to" :value="__('projects.tasks.assignee')" />
                                <x-select name="assigned_to">
                                    <option value="">{{ __('projects.tasks.unassigned') }}</option>
                                    @foreach ($assignableUsers as $user)
                                        <option value="{{ $user->id }}" @selected(old('assigned_to') == $user->id)>{{ $user->name }}</option>
                                    @endforeach
                                </x-select>
                                <x-input-error for="assigned_to" />
                            </div>

                            <div>
                                <x-label for="due_date" :value="__('projects.due_date')" />
                                <x-input name="due_date" type="date" />
                                <x-input-error for="due_date" />
                            </div>
                        </div>

                        <x-button type="submit">{{ __('projects.tasks.add_button') }}</x-button>
                    </form>
                </x-card>
            @endcan

            <x-card :title="__('projects.tasks.title').' ('.$tasks->count().')'" padding="p-0">
                <x-slot:actions>
                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ route('projects.show', $project) }}"
                           class="rounded-full px-3 py-1 text-xs font-medium transition
                                  {{ $activeStatus ? 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400' : 'bg-navy-600 text-white' }}">
                            {{ __('common.all') }}
                        </a>
                        @foreach ($statuses as $status)
                            <a href="{{ route('projects.show', ['project' => $project, 'status' => $status->value]) }}"
                               class="rounded-full px-3 py-1 text-xs font-medium transition
                                      {{ $activeStatus === $status->value ? 'bg-navy-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400' }}">
                                {{ $status->label() }}
                            </a>
                        @endforeach
                    </div>
                </x-slot:actions>

                @forelse ($tasks as $task)
                    <div class="flex items-start gap-3 border-b border-slate-100 px-6 py-4 transition last:border-0 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50">
                        @can('update', $task)
                            <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="pt-0.5">
                                @csrf
                                @method('PATCH')
                                <button type="submit" aria-label="{{ __('projects.tasks.completed') }}"
                                        class="flex size-5 items-center justify-center rounded-md border transition
                                               {{ $task->isDone()
                                                   ? 'border-emerald-600 bg-emerald-600 text-white'
                                                   : 'border-slate-300 bg-white hover:border-navy-500 dark:border-slate-600 dark:bg-slate-800' }}">
                                    @if ($task->isDone())
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        @endcan

                        <div class="min-w-0 grow">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('tasks.edit', $task) }}"
                                   class="font-medium transition hover:text-navy-600 dark:hover:text-navy-300 {{ $task->isDone() ? 'text-slate-400 line-through dark:text-slate-500' : '' }}">
                                    {{ $task->title }}
                                </a>
                                <x-badge :badge="$task->priority" />
                                <x-badge :badge="$task->status" />
                                @if ($task->isOverdue())
                                    <x-badge color="red">{{ __('dashboard.overdue') }}</x-badge>
                                @endif
                            </div>

                            @if ($task->description)
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $task->description }}</p>
                            @endif

                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-500">
                                {{ $task->assignee?->name ?? __('projects.tasks.unassigned') }}
                                @if ($task->due_date)
                                    · {{ $task->due_date->isoFormat('D MMM YYYY') }}
                                @endif
                            </p>
                        </div>

                        @can('delete', $task)
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-slate-400 transition hover:text-rose-600">
                                    {{ __('common.delete') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                @empty
                    <x-empty-state :title="__('projects.tasks.empty')" />
                @endforelse
            </x-card>
        </div>

        <div class="space-y-6">
            @can('update', $project)
                <x-card :title="__('projects.customer_link')">
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('projects.customer_link_hint') }}</p>

                    <div class="mt-3" x-data="{ copied: false, link: '{{ route('public.projects.show', $project->public_token) }}' }">
                        <input type="text" readonly :value="link"
                               class="block w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-xs
                                      dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <x-button type="button" variant="secondary" size="sm" class="mt-2 w-full"
                                  @click="navigator.clipboard.writeText(link); copied = true; setTimeout(() => copied = false, 2000)">
                            <span x-text="copied ? '✓' : '{{ __('quotations.copy') }}'"></span>
                        </x-button>
                    </div>
                </x-card>
            @endcan

            <x-card :title="__('projects.progress')">
                @php $percent = $project->progress(); @endphp
                <p class="text-4xl font-semibold tracking-tight text-navy-600 dark:text-navy-300">{{ $percent }}%</p>
                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                    <div class="h-full rounded-full bg-navy-600 transition-all duration-700 dark:bg-navy-400" style="width: {{ $percent }}%"></div>
                </div>
                @if ($project->due_date)
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                        {{ __('projects.due_date') }}: {{ $project->due_date->isoFormat('D MMM YYYY') }}
                    </p>
                @endif
            </x-card>

            <x-card :title="__('projects.team')">
                <ul class="space-y-3">
                    <li class="flex items-center justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-2.5">
                            <x-avatar :user="$project->owner" size="size-7" />
                            <span class="truncate text-sm">{{ $project->owner->name }}</span>
                        </div>
                        <span class="shrink-0 text-xs text-slate-500 dark:text-slate-400">{{ __('projects.owner_label') }}</span>
                    </li>

                    @foreach ($project->members as $member)
                        <li class="flex items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-2.5">
                                <x-avatar :user="$member" size="size-7" />
                                <span class="truncate text-sm">{{ $member->name }}</span>
                            </div>

                            @can('manageMembers', $project)
                                <form method="POST" action="{{ route('projects.members.destroy', [$project, $member]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="shrink-0 text-xs font-medium text-slate-400 transition hover:text-rose-600">
                                        {{ __('common.remove') }}
                                    </button>
                                </form>
                            @endcan
                        </li>
                    @endforeach
                </ul>

                @can('manageMembers', $project)
                    <form method="POST" action="{{ route('projects.members.store', $project) }}" class="mt-5 border-t border-slate-200 pt-5 dark:border-slate-800">
                        @csrf
                        <x-label for="email" :value="__('projects.invite_by_email')" />
                        <x-input name="email" type="email" placeholder="name@example.com" required />
                        <x-input-error for="email" />
                        <x-button type="submit" variant="secondary" class="mt-3 w-full">{{ __('projects.add_member') }}</x-button>
                    </form>
                @endcan
            </x-card>
        </div>
    </div>
</x-layouts.app>
