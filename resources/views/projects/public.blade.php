@php
    $brand = $company?->name ?? config('app.name');
    $done = $tasks->where('status', \App\Enums\TaskStatus::Done)->count();
    $percent = $tasks->count() > 0 ? (int) round($done / $tasks->count() * 100) : 0;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/leadabox-mark.svg') }}">
    <title>{{ $project->name }} · {{ $brand }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        <header class="mb-8 flex items-center gap-2.5">
            <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-9" width="36" height="36">
            <span class="text-lg font-semibold tracking-tight">{{ $brand }}</span>
        </header>

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        {{-- Progress --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
            <p class="text-sm text-slate-500">{{ __('projects.public_intro', ['company' => $brand]) }}</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">{{ $project->name }}</h1>

            @if ($project->description)
                <p class="mt-2 text-sm text-slate-600">{{ $project->description }}</p>
            @endif

            <div class="mt-6 flex items-end justify-between gap-4">
                <div>
                    <p class="text-4xl font-semibold tracking-tight text-navy-600">{{ $percent }}%</p>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ __('dashboard.tasks_done', ['done' => $done, 'total' => $tasks->count()]) }}
                    </p>
                </div>
                <x-badge :badge="$project->status" />
            </div>

            <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-navy-600 transition-all duration-700" style="width: {{ $percent }}%"></div>
            </div>

            @if ($project->due_date)
                <p class="mt-3 text-sm text-slate-500">
                    {{ __('projects.due_date') }}: {{ $project->due_date->isoFormat('D MMMM YYYY') }}
                </p>
            @endif
        </div>

        {{-- Step by step --}}
        <div class="mt-6 rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-6 py-4 sm:px-8">
                <h2 class="font-semibold">{{ __('projects.public_steps') }}</h2>
            </div>

            @forelse ($tasks as $task)
                @php $isDone = $task->status === \App\Enums\TaskStatus::Done; @endphp
                <div class="flex items-start gap-3 border-b border-slate-100 px-6 py-4 last:border-0 sm:px-8">
                    <span class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full
                                 {{ $isDone ? 'bg-emerald-500 text-white' : 'border-2 border-slate-200 bg-white' }}">
                        @if ($isDone)
                            <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        @endif
                    </span>

                    <div class="min-w-0 grow">
                        <p class="font-medium {{ $isDone ? 'text-slate-400 line-through' : '' }}">{{ $task->title }}</p>
                        @if ($task->description)
                            <p class="mt-0.5 text-sm text-slate-500">{{ $task->description }}</p>
                        @endif
                    </div>

                    <x-badge :badge="$task->status" />
                </div>
            @empty
                <p class="px-6 py-6 text-sm text-slate-500 sm:px-8">{{ __('projects.public_no_steps') }}</p>
            @endforelse
        </div>

        {{-- Messages --}}
        <div class="mt-6">
            <x-comment-thread
                :comments="$comments"
                :action="route('public.projects.comment', $project->public_token)"
            />
        </div>

        <footer class="mt-10 text-center text-xs text-slate-500">
            {{ $company?->legal_name ?? $brand }}
            @if ($company?->email) · {{ $company->email }} @endif
            @if ($company?->phone) · {{ $company->phone }} @endif
        </footer>
    </div>
</body>
</html>
