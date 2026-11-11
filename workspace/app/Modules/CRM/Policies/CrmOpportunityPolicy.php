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
        $companyId = $opportunity->company_id ?: $opportunity->account?->company_id;
        return ($user->hasRole('super_admin') || $user->can('view_crm_opportunity')) && $companyId !== null && $user->companies()->whereKey($companyId)->exists();
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
        return ! $this->isIntegrated($opportunity) && ($user->hasRole('super_admin') || $user->can('delete_crm_opportunity')) && $this->view($user, $opportunity);
    }

    public function convertToProject(User $user, CrmOpportunity $opportunity): bool
    {
        return $this->view($user, $opportunity)
            && $opportunity->project_id === null
            && $opportunity->closingStatus?->is_lost !== true;
    }

    private function isIntegrated(CrmOpportunity $opportunity): bool
    {
        return (bool) $opportunity->is_integrated || filled($opportunity->source_system);
    }
}
