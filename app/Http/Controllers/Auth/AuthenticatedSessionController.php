<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view. The admin dashboard gets its own branded
     * view, but both post to the same `login` route/controller below.
     */
    public function create(Request $request): View
    {
        return view($request->routeIs('admin.login') ? 'auth.admin-login' : 'auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        $default = $user->hasAnyRole(['admin', 'staff'])
            ? route('admin.dashboard', absolute: false)
            : route('account.dashboard', absolute: false);

        return redirect()->intended($default);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
