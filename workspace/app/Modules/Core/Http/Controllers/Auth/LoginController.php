<?php

namespace App\Modules\Core\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Modules\Core\Application\Services\Audit\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, AuditLogger $audit): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [], [
            'email' => 'email',
            'password' => 'kata sandi',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $audit->record('auth.login_failed', null, null, ['reason' => 'invalid_credentials']);

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['authentication' => 'Email atau kata sandi tidak ditemukan.']);
        }

        $request->session()->regenerate();
        $audit->record('auth.login_succeeded', $request->user());

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request, AuditLogger $audit): RedirectResponse
    {
        $audit->record('auth.logout', $request->user());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
