<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreTeamMemberRequest;
use App\Http\Requests\UpdateTeamMemberRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $members = User::query()
            ->with('roles')
            ->where('company_id', $request->user()->company_id)
            ->orderBy('name')
            ->paginate(15);

        return view('team.index', ['members' => $members]);
    }

    public function create(): View
    {
        return view('team.create', ['roles' => Role::cases()]);
    }

    public function store(StoreTeamMemberRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $password = Str::password(16);

        $user = User::create([
            'company_id' => $request->user()->company_id ?? Company::current()?->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'job_title' => $validated['job_title'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => $password,
            'is_active' => true,
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('team.index')
            ->with('status', __('team.member_created', ['name' => $user->name]))
            // Shown once so the admin can hand the credentials over.
            ->with('temporary_password', $password);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('team.edit', [
            'member' => $user->load('roles'),
            'roles' => Role::cases(),
        ]);
    }

    public function update(UpdateTeamMemberRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'job_title' => $validated['job_title'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($request->user()->isNot($user)) {
            $this->guardLastAdmin($user, $validated['role']);
            $user->syncRoles([$validated['role']]);
        }

        return redirect()
            ->route('team.index')
            ->with('status', __('team.member_updated', ['name' => $user->name]));
    }

    /** Accounts are deactivated rather than deleted so historical records keep their author. */
    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        if ($request->user()->is($user)) {
            throw ValidationException::withMessages(['user' => __('team.cannot_deactivate_self')]);
        }

        if ($user->is_active) {
            $this->guardLastAdmin($user, null);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('status', $user->is_active
            ? __('team.member_activated', ['name' => $user->name])
            : __('team.member_deactivated', ['name' => $user->name]));
    }

    /** Prevent locking everyone out of the administration module. */
    private function guardLastAdmin(User $user, ?string $newRole): void
    {
        if (! $user->isAdmin() || $newRole === Role::Admin->value) {
            return;
        }

        $remainingAdmins = User::role(Role::Admin->value)
            ->where('is_active', true)
            ->where('id', '!=', $user->id)
            ->count();

        if ($remainingAdmins === 0) {
            throw ValidationException::withMessages(['role' => __('team.last_admin')]);
        }
    }
}
