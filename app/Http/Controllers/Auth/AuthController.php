<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = new User($request->safe()->except('promo_emails') + [
            'promo_emails' => $request->boolean('promo_emails'),
        ]);
        $user->role()->associate(Role::firstOrCreate(['name' => Role::USER]));
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('The provided credentials do not match our records.')]);
        }

        if (Auth::user()->is_blocked) {
            Auth::logout();

            return back()->withErrors(['email' => __('Your account has been blocked. Please contact support.')]);
        }

        $request->session()->regenerate();

        return redirect()->intended(match (true) {
            Auth::user()->isAdmin() => route('admin.dashboard'),
            Auth::user()->isWorker() => route('worker.appointments.index'),
            default => route('dashboard'),
        });
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
