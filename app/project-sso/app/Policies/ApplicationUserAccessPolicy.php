<?php

namespace App\Policies;

use App\Models\ApplicationUserAccess;
use App\Models\User;

class ApplicationUserAccessPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allowed($user, 'view_any_application::access');
    }

    public function view(User $user, ApplicationUserAccess $access): bool
    {
        return $this->allowed($user, 'view_application::access');
    }

    public function create(User $user): bool
    {
        return $this->allowed($user, 'create_application::access');
    }

    public function update(User $user, ApplicationUserAccess $access): bool
    {
        return $this->allowed($user, 'update_application::access');
    }

    public function delete(User $user, ApplicationUserAccess $access): bool
    {
        return $this->allowed($user, 'delete_application::access');
    }

    private function allowed(User $user, string $permission): bool
    {
        return $user->hasRole('super_admin') || $user->can($permission);
    }
}
