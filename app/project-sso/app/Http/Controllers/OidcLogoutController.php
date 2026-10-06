<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OidcLogoutController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $redirect = $request->string('post_logout_redirect_uri')->toString();
        $allowed = config('services.project_sso.allowed_logout_redirects', []);
        return in_array($redirect, $allowed, true) ? redirect()->away($redirect) : redirect('/');
    }
}
