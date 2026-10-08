<?php

namespace App\Policies;

use App\Models\CrmAccount;
use Core\Models\User;

class CrmAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->companies()->exists();
    }

    public function view(User $user, CrmAccount $account): bool
    {
        return $user->hasRole('super_admin') || $user->companies()->whereKey($account->company_id)->exists();
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, CrmAccount $account): bool
    {
        return $this->view($user, $account);
    }

    public function delete(User $user, CrmAccount $account): bool
    {
        return $this->view($user, $account) && $user->hasAnyRole(['super_admin', 'crm_admin']);
    }
}
