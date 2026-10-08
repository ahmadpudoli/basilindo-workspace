<?php

namespace App\Policies;

use App\Models\CrmActivity;
use Core\Models\User;

class CrmActivityPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->companies()->exists(); }
    public function view(User $user, CrmActivity $activity): bool { return $user->hasRole('super_admin') || $user->companies()->whereKey($activity->company_id)->exists(); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function update(User $user, CrmActivity $activity): bool { return $this->view($user, $activity); }
    public function delete(User $user, CrmActivity $activity): bool { return $this->view($user, $activity); }
}
