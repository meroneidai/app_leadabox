<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\Role;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_project(): void
    {
        $user = $this->staff(Role::Manager);

        $response = $this->actingAs($user)->post(route('projects.store'), [
            'name' => 'Website relaunch',
            'description' => 'Ship the new marketing site.',
            'status' => ProjectStatus::Active->value,
            'due_date' => now()->addMonth()->toDateString(),
        ]);

        $project = Project::firstOrFail();

        $response->assertRedirect(route('projects.show', $project));
        $this->assertSame($user->id, $project->user_id);
        $this->assertSame($user->company_id, $project->company_id);
    }

    public function test_project_name_is_required(): void
    {
        $this->actingAs($this->staff(Role::Manager))
            ->post(route('projects.store'), ['name' => '', 'status' => ProjectStatus::Active->value])
            ->assertSessionHasErrors('name');
    }

    public function test_index_only_lists_accessible_projects(): void
    {
        $user = $this->staff(Role::Manager);
        $other = $this->staff(Role::Manager);

        $owned = Project::factory()->for($user, 'owner')->create(['name' => 'My project']);
        $shared = Project::factory()->for($other, 'owner')->create(['name' => 'Shared project']);
        $shared->members()->attach($user);
        Project::factory()->for($other, 'owner')->create(['name' => 'Unrelated project']);

        $this->actingAs($user)
            ->get(route('projects.index'))
            ->assertOk()
            ->assertSee($owned->name)
            ->assertSee($shared->name)
            ->assertDontSee('Unrelated project');
    }

    public function test_outsider_cannot_view_project(): void
    {
        $project = Project::factory()->for($this->staff(Role::Manager), 'owner')->create();

        $this->actingAs($this->staff(Role::Manager))
            ->get(route('projects.show', $project))
            ->assertForbidden();
    }

    public function test_user_without_project_permission_is_blocked(): void
    {
        // Finance has no project access at all.
        $this->actingAs($this->staff(Role::Finance))
            ->get(route('projects.index'))
            ->assertForbidden();
    }

    public function test_member_can_view_but_only_owner_can_delete(): void
    {
        $owner = $this->staff(Role::Manager);
        $member = $this->staff(Role::Support);
        $project = Project::factory()->for($owner, 'owner')->create();
        $project->members()->attach($member);

        $this->actingAs($member)->get(route('projects.show', $project))->assertOk();
        $this->actingAs($member)->delete(route('projects.destroy', $project))->assertForbidden();

        $this->actingAs($owner)->delete(route('projects.destroy', $project))->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_owner_can_add_and_remove_members(): void
    {
        $owner = $this->staff(Role::Manager);
        $project = Project::factory()->for($owner, 'owner')->create();
        $teammate = User::factory()->create(['company_id' => $owner->company_id]);

        $this->actingAs($owner)
            ->post(route('projects.members.store', $project), ['email' => $teammate->email]);

        $this->assertTrue($project->members()->whereKey($teammate->id)->exists());

        $this->actingAs($owner)
            ->delete(route('projects.members.destroy', [$project, $teammate]));

        $this->assertFalse($project->members()->whereKey($teammate->id)->exists());
    }
}
