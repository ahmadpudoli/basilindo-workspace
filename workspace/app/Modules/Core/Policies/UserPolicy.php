<?php

namespace App\Modules\Core\Policies;

use Core\Models\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Core\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    private function canManageSettings(AuthUser $authUser): bool
    {
        return $authUser->hasRole('super_admin');
    }
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('view_any_user');
    }

    public function view(AuthUser $authUser, ?User $target = null): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('view_user');
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('create_user');
    }

    public function update(AuthUser $authUser, ?User $target = null): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('update_user');
    }

    public function delete(AuthUser $authUser, User $target): bool
    {
        return ! $target->isProtectedSystemAccount() && ($this->canManageSettings($authUser) || $authUser->can('delete_user'));
    }

    public function restore(AuthUser $authUser): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('restore_user');
    }

    public function forceDelete(AuthUser $authUser, User $target): bool
    {
        return ! $target->isProtectedSystemAccount() && ($this->canManageSettings($authUser) || $authUser->can('force_delete_user'));
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('force_delete_any_user');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('restore_any_user');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('replicate_user');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->canManageSettings($authUser) || $authUser->can('reorder_user');
    }

}
