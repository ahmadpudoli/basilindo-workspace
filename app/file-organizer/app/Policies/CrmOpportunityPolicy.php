<?php

namespace App\Policies;

use App\Models\CrmOpportunity;
use Core\Models\User;

class CrmOpportunityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->companies()->exists();
    }

    public function view(User $user, CrmOpportunity $opportunity): bool
    {
        return $user->hasRole('super_admin') || $user->companies()->whereKey($opportunity->account?->company_id)->exists();
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, CrmOpportunity $opportunity): bool
    {
        return $this->view($user, $opportunity);
    }

    public function delete(User $user, CrmOpportunity $opportunity): bool
    {
        return $this->view($user, $opportunity) && $user->hasAnyRole(['super_admin', 'crm_admin']);
    }

    public function convertToProject(User $user, CrmOpportunity $opportunity): bool
    {
        return $this->view($user, $opportunity)
            && $opportunity->project_id === null
            && $opportunity->stage !== 'lost';
    }
}
