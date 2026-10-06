<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        abort_unless(config('sso.enabled'), 503, 'SSO perusahaan belum dikonfigurasi.');
        $this->redirectRoute('auth.sso');
    }
}
