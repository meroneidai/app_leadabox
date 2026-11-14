<x-layouts.app :title="__('projects.title')">
    <x-page-header :title="__('projects.title')" :subtitle="__('projects.subtitle', ['count' => $projects->total()])">
        @can('manage projects')
            <x-slot:actions>
                <x-button :href="route('projects.create')">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('projects.new') }}
                </x-button>
            </x-slot:actions>
        @endcan
    </x-page-header>

    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('projects.index') }}"
           class="rounded-full px-3.5 py-1.5 text-sm font-medium transition
                  {{ $activeStatus
                      ? 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800'
                      : 'bg-navy-600 text-white shadow-xs' }}">
            {{ __('common.all') }}
        </a>

        @foreach (\App\Enums\ProjectStatus::cases() as $status)
            <a href="{{ route('projects.index', ['status' => $status->value]) }}"
               class="rounded-full px-3.5 py-1.5 text-sm font-medium transition
                      {{ $activeStatus === $status->value
                          ? 'bg-navy-600 text-white shadow-xs'
                          : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800' }}">
                {{ $status->label() }}
            </a>
        @endforeach
    </div>

    @if ($projects->isEmpty())
        <x-card padding="p-0">
            <x-empty-state :title="__('projects.empty_title')" :description="__('projects.empty_description')">
                @can('manage projects')
                    <x-slot:action>
                        <x-button :href="route('projects.create')">{{ __('projects.new') }}</x-button>
                    </x-slot:action>
                @endcan
            </x-empty-state>
        </x-card>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($projects as $project)
                @php
                    $percent = $project->tasks_count > 0
                        ? (int) round($project->completed_tasks_count / $project->tasks_count * 100)
                        : 0;
                @endphp

                <a href="{{ route('projects.show', $project) }}"
                   class="flex flex-col rounded-xl border border-slate-200 bg-white p-5 shadow-xs transition duration-200
                          hover:-translate-y-0.5 hover:border-navy-300 hover:shadow-md
                          dark:border-slate-800 dark:bg-slate-900 dark:hover:border-navy-500/50">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-semibold leading-tight">{{ $project->name }}</h2>
                        <x-badge :badge="$project->status" />
                    </div>

                    <p class="mt-2 line-clamp-2 grow text-sm text-slate-600 dark:text-slate-400">
                        {{ $project->description ?: '—' }}
                    </p>

                    <div class="mt-4">
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-full rounded-full bg-navy-600 transition-all duration-500 dark:bg-navy-400" style="width: {{ $percent }}%"></div>
                        </div>

                        <div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                            <span>{{ __('projects.tasks_count', ['done' => $project->completed_tasks_count, 'total' => $project->tasks_count]) }}</span>
                            @if ($project->due_date)
                                <span>{{ $project->due_date->isoFormat('D MMM YYYY') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $projects->links() }}</div>
    @endif
</x-layouts.app>
