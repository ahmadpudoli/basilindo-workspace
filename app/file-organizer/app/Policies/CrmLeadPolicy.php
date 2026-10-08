<?php

namespace App\Policies;

use App\Models\CrmLead;
use Core\Models\User;

class CrmLeadPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->companies()->exists(); }

    public function view(User $user, CrmLead $lead): bool
    {
        return $user->hasRole('super_admin') || $user->companies()->whereKey($lead->company_id)->exists();
    }

    public function create(User $user): bool { return $this->viewAny($user); }
    public function update(User $user, CrmLead $lead): bool { return $this->view($user, $lead); }
    public function delete(User $user, CrmLead $lead): bool { return $this->view($user, $lead) && $user->hasAnyRole(['super_admin', 'crm_admin']); }
}
