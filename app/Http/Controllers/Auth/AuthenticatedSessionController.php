<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $request->session()->put('locale', app()->getLocale());

        if (! Auth::attempt([...$credentials, 'is_active' => true], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => $this->accountIsDeactivated($credentials['email'])
                    ? __('auth.inactive')
                    : __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $request->user()->forceFill(['last_login_at' => now()])->save();
        $request->session()->put('locale', $request->user()->locale);

        return redirect()->intended(route('dashboard'));
    }

    private function accountIsDeactivated(string $email): bool
    {
        return User::where('email', $email)->where('is_active', false)->exists();
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
