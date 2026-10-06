<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();
        
        // project-sso is the internal identity provider; Google is not used as the employee login path.
    }
}
