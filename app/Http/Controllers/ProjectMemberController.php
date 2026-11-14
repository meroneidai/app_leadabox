<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProjectMemberController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('manageMembers', $project);

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'No Ledabox account uses that email address.',
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        if ($project->isOwnedBy($user)) {
            throw ValidationException::withMessages([
                'email' => 'The project owner already has full access.',
            ]);
        }

        $project->members()->syncWithoutDetaching([$user->id]);

        return back()->with('status', "{$user->name} was added to the project.");
    }

    public function destroy(Project $project, User $user): RedirectResponse
    {
        $this->authorize('manageMembers', $project);

        $project->members()->detach($user->id);

        $project->tasks()->where('assigned_to', $user->id)->update(['assigned_to' => null]);

        return back()->with('status', "{$user->name} was removed from the project.");
    }
}
