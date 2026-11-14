<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $status = $request->query('status');

        $projects = Project::accessibleBy($request->user())
            ->when(
                in_array($status, ProjectStatus::values(), true),
                fn ($query) => $query->where('status', $status)
            )
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($query) => $query->where('status', TaskStatus::Done),
            ])
            ->with('owner')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('projects.index', [
            'projects' => $projects,
            'activeStatus' => $status,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);

        return view('projects.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $request->user()->ownedProjects()->create([
            ...$request->validated(),
            'company_id' => $request->user()->company_id,
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('projects.created'));
    }

    public function show(Request $request, Project $project): View
    {
        $this->authorize('view', $project);

        $status = $request->query('status');

        $tasks = $project->tasks()
            ->with('assignee')
            ->when(
                in_array($status, TaskStatus::values(), true),
                fn ($query) => $query->where('status', $status)
            )
            ->orderByRaw("case priority when 'high' then 1 when 'medium' then 2 else 3 end")
            ->orderByRaw('due_date is null, due_date asc')
            ->latest('id')
            ->get();

        $project->load('owner', 'members');

        return view('projects.show', [
            'project' => $project,
            'tasks' => $tasks,
            'activeStatus' => $status,
            'assignableUsers' => $project->assignableUsers(),
            'priorities' => TaskPriority::cases(),
            'statuses' => TaskStatus::cases(),
        ]);
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        return view('projects.edit', ['project' => $project]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('projects.updated'));
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('status', __('projects.deleted'));
    }
}
