<?php

namespace App\Modules\CRM\Policies;

use App\Modules\CRM\Domain\Models\CrmActivity;
use Core\Models\User;

class CrmActivityPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || ($user->can('access_crm') && $user->companies()->exists()); }
    public function view(User $user, CrmActivity $activity): bool { return ($user->hasRole('super_admin') || $user->can('view_crm_activity')) && $user->companies()->whereKey($activity->company_id)->exists(); }
    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_crm_activity'); }
    public function update(User $user, CrmActivity $activity): bool { return ($user->hasRole('super_admin') || $user->can('update_crm_activity')) && $this->view($user, $activity); }
    public function delete(User $user, CrmActivity $activity): bool { return ($user->hasRole('super_admin') || $user->can('delete_crm_activity')) && $this->view($user, $activity); }
}
