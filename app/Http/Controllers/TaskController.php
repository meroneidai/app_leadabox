<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request, Project $project): RedirectResponse
    {
        $data = $request->validated();
        $data['completed_at'] = $data['status'] === TaskStatus::Done->value ? now() : null;

        $project->tasks()->create($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('projects.tasks.added'));
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        $task->load('project');

        return view('tasks.edit', [
            'task' => $task,
            'project' => $task->project,
            'assignableUsers' => $task->project->assignableUsers(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $data = $request->validated();
        $data['completed_at'] = $data['status'] === TaskStatus::Done->value ? ($task->completed_at ?? now()) : null;

        $task->update($data);

        return redirect()
            ->route('projects.show', $task->project)
            ->with('status', __('projects.tasks.updated'));
    }

    /** Cycle a task between done and to-do straight from the project board. */
    public function toggle(Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $done = $task->isDone();

        $task->update([
            'status' => $done ? TaskStatus::Todo : TaskStatus::Done,
            'completed_at' => $done ? null : now(),
        ]);

        return back()->with('status', $done ? __('projects.tasks.reopened') : __('projects.tasks.completed'));
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $project = $task->project;
        $task->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('projects.tasks.deleted'));
    }
}
