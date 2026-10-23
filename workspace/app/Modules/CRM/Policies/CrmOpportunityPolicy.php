<?php

namespace App\Modules\CRM\Policies;

use App\Modules\CRM\Domain\Models\CrmOpportunity;
use Core\Models\User;

class CrmOpportunityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || ($user->can('access_crm') && $user->companies()->exists());
    }

    public function view(User $user, CrmOpportunity $opportunity): bool
    {
        return ($user->hasRole('super_admin') || $user->can('view_crm_opportunity')) && $user->companies()->whereKey($opportunity->account?->company_id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('create_crm_opportunity');
    }

    public function update(User $user, CrmOpportunity $opportunity): bool
    {
        return ($user->hasRole('super_admin') || $user->can('update_crm_opportunity')) && $this->view($user, $opportunity);
    }

    public function delete(User $user, CrmOpportunity $opportunity): bool
    {
        return ($user->hasRole('super_admin') || $user->can('delete_crm_opportunity')) && $this->view($user, $opportunity);
    }

    public function convertToProject(User $user, CrmOpportunity $opportunity): bool
    {
        return $this->view($user, $opportunity)
            && $opportunity->project_id === null
            && $opportunity->stage !== 'lost';
    }
}
