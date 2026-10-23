<?php

namespace App\Modules\CRM\Policies;

use App\Modules\CRM\Domain\Models\CrmLead;
use Core\Models\User;

class CrmLeadPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || ($user->can('access_crm') && $user->companies()->exists()); }

    public function view(User $user, CrmLead $lead): bool
    {
        return ($user->hasRole('super_admin') || $user->can('view_crm_lead')) && $user->companies()->whereKey($lead->company_id)->exists();
    }

    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_crm_lead'); }
    public function update(User $user, CrmLead $lead): bool { return ($user->hasRole('super_admin') || $user->can('update_crm_lead')) && $this->view($user, $lead); }
    public function delete(User $user, CrmLead $lead): bool { return ($user->hasRole('super_admin') || $user->can('delete_crm_lead')) && $this->view($user, $lead); }
}
