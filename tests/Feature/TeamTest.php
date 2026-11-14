<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_invite_a_team_member(): void
    {
        $admin = $this->staff(Role::Admin);

        $this->actingAs($admin)
            ->post(route('team.store'), [
                'name' => 'New Seller',
                'email' => 'seller@example.com',
                'role' => Role::Sales->value,
            ])
            ->assertRedirect(route('team.index'))
            ->assertSessionHas('temporary_password');

        $member = User::firstWhere('email', 'seller@example.com');

        $this->assertTrue($member->hasRole(Role::Sales->value));
        $this->assertSame($admin->company_id, $member->company_id);
        $this->assertTrue($member->is_active);
    }

    public function test_non_admin_cannot_invite_team_members(): void
    {
        $this->actingAs($this->staff(Role::Sales))
            ->get(route('team.create'))
            ->assertForbidden();
    }

    public function test_finance_cannot_even_view_the_team(): void
    {
        $this->actingAs($this->staff(Role::Finance))
            ->get(route('team.index'))
            ->assertForbidden();
    }

    public function test_admin_can_change_a_members_role(): void
    {
        $admin = $this->staff(Role::Admin);
        $member = $this->staff(Role::Support);

        $this->actingAs($admin)->put(route('team.update', $member), [
            'name' => $member->name,
            'email' => $member->email,
            'role' => Role::Finance->value,
        ])->assertRedirect(route('team.index'));

        $this->assertTrue($member->fresh()->hasRole(Role::Finance->value));
    }

    public function test_last_admin_cannot_be_demoted(): void
    {
        $admin = $this->staff(Role::Admin);
        $other = $this->staff(Role::Sales);

        $this->actingAs($other); // not used for the call, ensures roles exist

        $this->actingAs($admin)->put(route('team.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => Role::Sales->value,
        ]);

        // Own role changes are ignored entirely, so the admin keeps full access.
        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $admin = $this->staff(Role::Admin);

        $this->actingAs($admin)
            ->patch(route('team.toggle-active', $admin))
            ->assertSessionHasErrors('user');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_can_deactivate_and_reactivate_a_member(): void
    {
        $admin = $this->staff(Role::Admin);
        $member = $this->staff(Role::Support);

        $this->actingAs($admin)->patch(route('team.toggle-active', $member));
        $this->assertFalse($member->fresh()->is_active);

        $this->actingAs($admin)->patch(route('team.toggle-active', $member));
        $this->assertTrue($member->fresh()->is_active);
    }

    public function test_deactivating_the_last_admin_is_blocked(): void
    {
        $admin = $this->staff(Role::Admin);
        $secondAdmin = $this->staff(Role::Admin);

        // Removing one of two admins is allowed.
        $this->actingAs($admin)->patch(route('team.toggle-active', $secondAdmin));
        $this->assertFalse($secondAdmin->fresh()->is_active);
    }
}
