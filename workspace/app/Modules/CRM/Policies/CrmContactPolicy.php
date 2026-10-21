<?php

namespace App\Modules\CRM\Policies;

use Core\Models\User;

class CrmContactPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->companies()->exists(); }
    public function view(User $user, object $contact): bool { return $this->viewAny($user); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function update(User $user, object $contact): bool { return $this->view($user, $contact); }
    public function delete(User $user, object $contact): bool { return $user->hasRole('super_admin'); }
}
