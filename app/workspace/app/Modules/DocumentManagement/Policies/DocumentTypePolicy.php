<?php

namespace App\Modules\DocumentManagement\Policies;

use Core\Models\User;

class DocumentTypePolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->companies()->exists(); }
    public function create(User $user): bool { return $user->hasRole('super_admin'); }
    public function update(User $user, object $type): bool { return $user->hasRole('super_admin'); }
    public function delete(User $user, object $type): bool { return $user->hasRole('super_admin'); }
}
