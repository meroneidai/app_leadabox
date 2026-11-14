<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private function project(): Project
    {
        return Project::factory()->for($this->staff(Role::Manager), 'owner')->create();
    }

    public function test_member_can_add_a_task(): void
    {
        $project = $this->project();

        $this->actingAs($project->owner)
            ->post(route('projects.tasks.store', $project), [
                'title' => 'Write the brief',
                'status' => TaskStatus::Todo->value,
                'priority' => TaskPriority::High->value,
                'assigned_to' => $project->owner->id,
            ])
            ->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title' => 'Write the brief',
            'assigned_to' => $project->owner->id,
        ]);
    }

    public function test_task_cannot_be_assigned_to_a_non_member(): void
    {
        $project = $this->project();
        $outsider = User::factory()->create();

        $this->actingAs($project->owner)
            ->post(route('projects.tasks.store', $project), [
                'title' => 'Nope',
                'status' => TaskStatus::Todo->value,
                'priority' => TaskPriority::Low->value,
                'assigned_to' => $outsider->id,
            ])
            ->assertSessionHasErrors('assigned_to');
    }

    public function test_outsider_cannot_add_a_task(): void
    {
        $project = $this->project();

        $this->actingAs($this->staff(Role::Manager))
            ->post(route('projects.tasks.store', $project), [
                'title' => 'Intruder',
                'status' => TaskStatus::Todo->value,
                'priority' => TaskPriority::Low->value,
            ])
            ->assertForbidden();
    }

    public function test_toggling_marks_the_task_done_and_back(): void
    {
        $project = $this->project();
        $task = Task::factory()->for($project)->create([
            'status' => TaskStatus::Todo,
            'completed_at' => null,
        ]);

        $this->actingAs($project->owner)->patch(route('tasks.toggle', $task));
        $task->refresh();
        $this->assertSame(TaskStatus::Done, $task->status);
        $this->assertNotNull($task->completed_at);

        $this->actingAs($project->owner)->patch(route('tasks.toggle', $task));
        $task->refresh();
        $this->assertSame(TaskStatus::Todo, $task->status);
        $this->assertNull($task->completed_at);
    }

    public function test_task_can_be_updated_and_deleted(): void
    {
        $project = $this->project();
        $task = Task::factory()->for($project)->create();

        $this->actingAs($project->owner)->put(route('tasks.update', $task), [
            'title' => 'Renamed task',
            'status' => TaskStatus::InProgress->value,
            'priority' => TaskPriority::Medium->value,
        ])->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Renamed task']);

        $this->actingAs($project->owner)->delete(route('tasks.destroy', $task));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_deleting_a_project_deletes_its_tasks(): void
    {
        $project = $this->project();
        $task = Task::factory()->for($project)->create();

        $this->actingAs($project->owner)->delete(route('projects.destroy', $project));

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
