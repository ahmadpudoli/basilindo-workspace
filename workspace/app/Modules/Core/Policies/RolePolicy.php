<?php

declare(strict_types=1);

namespace App\Modules\Core\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    private function canManageSettings(AuthUser $authUser): bool
    {
        return $authUser->hasRole('super_admin');
    }
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('view_any_role');
    }

    public function view(AuthUser $authUser, Role $role): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('view_role');
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('create_role');
    }

    public function update(AuthUser $authUser, Role $role): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('update_role');
    }

    public function delete(AuthUser $authUser, Role $role): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('delete_role');
    }

    public function restore(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('restore_role');
    }

    public function forceDelete(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('force_delete_role');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_role');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_role');
    }

    public function replicate(AuthUser $authUser, Role $role): bool
    {
        return $authUser->can('replicate_role');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_role');
    }

}
