<?php

namespace App\Modules\Core\Policies;

use Core\Models\OrganizationUnit;
use Core\Models\User;

class OrganizationUnitPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->can('view_any_organization_unit'); }
    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_organization_unit'); }
    public function update(User $user, OrganizationUnit $unit): bool
    {
        return $user->hasRole('super_admin') || ($user->can('update_organization_unit') && $user->companies()->whereKey($unit->company_id)->exists());
    }
    public function delete(User $user, OrganizationUnit $unit): bool
    {
        return $user->hasRole('super_admin') || ($user->can('delete_organization_unit') && $user->companies()->whereKey($unit->company_id)->exists());
    }
}
