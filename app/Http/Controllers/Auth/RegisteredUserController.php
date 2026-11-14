<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * First-run setup only. Once an account exists, staff are invited from the team module.
 */
class RegisteredUserController extends Controller
{
    public function create(): View
    {
        abort_if(User::query()->exists(), 403);

        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if(User::query()->exists(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            ...$validated,
            'company_id' => Company::current()?->id,
            'is_active' => true,
        ]);

        $user->assignRole(Role::Admin->value);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
