<?php

namespace App\Modules\CRM\Policies;

use Core\Models\User;

class CrmContactPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || ($user->can('access_crm') && $user->companies()->exists()); }
    public function view(User $user, object $contact): bool { return $user->hasRole('super_admin') || $user->can('view_crm_account'); }
    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_crm_account'); }
    public function update(User $user, object $contact): bool { return $user->hasRole('super_admin') || $user->can('update_crm_account'); }
    public function delete(User $user, object $contact): bool { return $user->hasRole('super_admin') || $user->can('delete_crm_account'); }
}
