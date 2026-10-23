<?php

namespace App\Modules\CRM\Policies;

use App\Modules\CRM\Domain\Models\CrmAccount;
use Core\Models\User;

class CrmAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || ($user->can('access_crm') && $user->companies()->exists());
    }

    public function view(User $user, CrmAccount $account): bool
    {
        return ($user->hasRole('super_admin') || $user->can('view_crm_account')) && $user->companies()->whereKey($account->company_id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('create_crm_account');
    }

    public function update(User $user, CrmAccount $account): bool
    {
        return ($user->hasRole('super_admin') || $user->can('update_crm_account')) && $this->view($user, $account);
    }

    public function delete(User $user, CrmAccount $account): bool
    {
        return ($user->hasRole('super_admin') || $user->can('delete_crm_account')) && $this->view($user, $account);
    }
}
