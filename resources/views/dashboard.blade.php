<x-layouts.app :title="__('dashboard.title')">
    <x-page-header
        :title="__('dashboard.welcome', ['name' => auth()->user()->name])"
        :subtitle="__('dashboard.subtitle')"
    />

    {{-- Quick actions --}}
    <div class="mb-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @can('manage quotations')
            <a href="{{ route('quotations.create') }}"
               class="group flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-xs transition
                      hover:-translate-y-0.5 hover:border-navy-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-navy-500/50">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-navy-50 text-navy-600 transition group-hover:bg-navy-600 group-hover:text-white dark:bg-navy-500/10 dark:text-navy-300">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </span>
                <span class="text-sm font-semibold">{{ __('quotations.new') }}</span>
            </a>
        @endcan

        @can('manage customers')
            <a href="{{ route('customers.create') }}"
               class="group flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-xs transition
                      hover:-translate-y-0.5 hover:border-navy-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-navy-500/50">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-600 group-hover:text-white dark:bg-emerald-500/10 dark:text-emerald-400">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                    </svg>
                </span>
                <span class="text-sm font-semibold">{{ __('customers.new') }}</span>
            </a>
        @endcan

        @can('manage services')
            <a href="{{ route('services.create') }}"
               class="group flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-xs transition
                      hover:-translate-y-0.5 hover:border-navy-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-navy-500/50">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition group-hover:bg-amber-600 group-hover:text-white dark:bg-amber-500/10 dark:text-amber-400">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.260m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                    </svg>
                </span>
                <span class="text-sm font-semibold">{{ __('services.new') }}</span>
            </a>
        @endcan

        @can('manage projects')
            <a href="{{ route('projects.create') }}"
               class="group flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-xs transition
                      hover:-translate-y-0.5 hover:border-navy-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-navy-500/50">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600 transition group-hover:bg-violet-600 group-hover:text-white dark:bg-violet-500/10 dark:text-violet-400">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                    </svg>
                </span>
                <span class="text-sm font-semibold">{{ __('projects.new') }}</span>
            </a>
        @endcan
    </div>

    {{-- Key numbers --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @if ($stats['customers'] !== null)
            <x-stat :label="__('nav.customers')" :value="$stats['customers']" tone="navy" />
        @endif

        @if ($stats['open_quotations'] !== null)
            <x-stat :label="__('dashboard.pending_quotations')" :value="$stats['open_quotations']" tone="amber">
                <x-slot:meta>{{ $stats['open_quotations_value'] }}</x-slot:meta>
            </x-stat>
        @endif

        <x-stat :label="__('dashboard.open_projects')" :value="$stats['open_projects']" tone="green" />
        <x-stat :label="__('dashboard.overdue')" :value="$stats['overdue_tasks']" tone="red" />
    </div>

    {{-- Needs attention --}}
    @if ($overdueInvoices->isNotEmpty() || $unreadMessages->isNotEmpty())
        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50/60 p-5 dark:border-amber-500/30 dark:bg-amber-500/5">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-amber-800 dark:text-amber-300">
                {{ __('invoices.needs_attention') }}
            </h2>

            <div class="space-y-2">
                @foreach ($overdueInvoices as $invoice)
                    <a href="{{ route('invoices.show', $invoice) }}"
                       class="flex items-center justify-between gap-4 rounded-lg bg-white px-4 py-3 text-sm transition hover:shadow-xs dark:bg-slate-900">
                        <span class="min-w-0">
                            <span class="font-medium">{{ $invoice->reference() }}</span>
                            <span class="text-slate-500 dark:text-slate-400"> · {{ $invoice->customer->name }}</span>
                        </span>
                        <span class="flex shrink-0 items-center gap-3">
                            <span class="tabular-nums">{{ \App\Support\Money::format($invoice->amountDue(), $invoice->currency) }}</span>
                            <x-badge color="red">{{ __('invoices.overdue_days', ['days' => $invoice->daysOverdue()]) }}</x-badge>
                        </span>
                    </a>
                @endforeach

                @foreach ($unreadMessages as $message)
                    @php
                        $target = $message->commentable;
                        $url = match (true) {
                            $target instanceof \App\Models\Project => route('projects.show', $target),
                            $target instanceof \App\Models\Quotation => route('quotations.show', $target),
                            $target instanceof \App\Models\Invoice => route('invoices.show', $target),
                            default => null,
                        };
                    @endphp

                    @if ($url)
                        <a href="{{ $url }}"
                           class="flex items-start justify-between gap-4 rounded-lg bg-white px-4 py-3 text-sm transition hover:shadow-xs dark:bg-slate-900">
                            <span class="min-w-0">
                                <span class="font-medium">{{ $message->authorName() }}</span>
                                <span class="block truncate text-slate-500 dark:text-slate-400">{{ $message->body }}</span>
                            </span>
                            <x-badge color="navy">{{ __('comments.title') }}</x-badge>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        @can('view quotations')
            <x-card :title="__('quotations.title')" padding="p-0">
                <x-slot:actions>
                    <a href="{{ route('quotations.index') }}" class="text-sm font-medium text-navy-600 transition hover:text-navy-500 dark:text-navy-300">
                        {{ __('dashboard.view_all') }}
                    </a>
                </x-slot:actions>

                @forelse ($recentQuotations as $quotation)
                    <a href="{{ route('quotations.show', $quotation) }}"
                       class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-3.5 transition last:border-0 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50">
                        <div class="min-w-0">
                            <p class="truncate font-medium">{{ $quotation->customer->name }}</p>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                {{ $quotation->reference() }}
                                @if ($quotation->title) · {{ $quotation->title }} @endif
                            </p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <span class="text-sm font-medium tabular-nums">
                                {{ \App\Support\Money::format((string) $quotation->total, $quotation->currency) }}
                            </span>
                            <x-badge :badge="$quotation->status" />
                        </div>
                    </a>
                @empty
                    <x-empty-state :title="__('quotations.empty_title')" :description="__('quotations.empty_description')">
                        @can('manage quotations')
                            <x-slot:action>
                                <x-button :href="route('quotations.create')">{{ __('quotations.new') }}</x-button>
                            </x-slot:action>
                        @endcan
                    </x-empty-state>
                @endforelse
            </x-card>
        @endcan

        <x-card :title="__('dashboard.my_tasks')" padding="p-0">
            @forelse ($myTasks as $task)
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-3.5 transition last:border-0 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/50">
                    <div class="min-w-0">
                        <a href="{{ route('tasks.edit', $task) }}" class="block truncate font-medium transition hover:text-navy-600 dark:hover:text-navy-300">
                            {{ $task->title }}
                        </a>
                        <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">
                            {{ $task->project->name }}
                            @if ($task->due_date) · {{ $task->due_date->isoFormat('D MMM') }} @endif
                        </p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        @if ($task->isOverdue())
                            <x-badge color="red">{{ __('dashboard.overdue') }}</x-badge>
                        @endif
                        <x-badge :badge="$task->priority" />
                    </div>
                </div>
            @empty
                <x-empty-state :title="__('dashboard.no_tasks')" />
            @endforelse
        </x-card>
    </div>

    @if ($projects->isNotEmpty())
        <x-card class="mt-6" :title="__('dashboard.recent_projects')" padding="p-0">
            <x-slot:actions>
                <a href="{{ route('projects.index') }}" class="text-sm font-medium text-navy-600 transition hover:text-navy-500 dark:text-navy-300">
                    {{ __('dashboard.view_all') }}
                </a>
            </x-slot:actions>

            <div class="grid gap-px bg-slate-100 sm:grid-cols-2 lg:grid-cols-4 dark:bg-slate-800">
                @foreach ($projects as $project)
                    @php
                        $percent = $project->tasks_count > 0
                            ? (int) round($project->completed_tasks_count / $project->tasks_count * 100)
                            : 0;
                    @endphp
                    <a href="{{ route('projects.show', $project) }}" class="bg-white p-5 transition hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800/50">
                        <div class="flex items-start justify-between gap-2">
                            <span class="truncate font-medium">{{ $project->name }}</span>
                            <x-badge :badge="$project->status" />
                        </div>
                        <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-full rounded-full bg-navy-600 transition-all duration-500 dark:bg-navy-400" style="width: {{ $percent }}%"></div>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                            {{ __('dashboard.tasks_done', ['done' => $project->completed_tasks_count, 'total' => $project->tasks_count]) }}
                        </p>
                    </a>
                @endforeach
            </div>
        </x-card>
    @endif
</x-layouts.app>
