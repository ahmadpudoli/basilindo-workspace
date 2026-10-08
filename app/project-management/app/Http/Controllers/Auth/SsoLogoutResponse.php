<?php

namespace App\Http\Controllers\Auth;

use Filament\Auth\Http\Responses\LogoutResponse as BaseLogoutResponse;
use Illuminate\Http\RedirectResponse;

class SsoLogoutResponse extends BaseLogoutResponse
{
    public function toResponse($request): RedirectResponse
    {
        return redirect('/');
    }
}
