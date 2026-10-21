<?php

namespace App\Modules\DocumentManagement\Policies;

use App\Modules\DocumentManagement\Domain\Models\Bundle;
use Core\Models\User;

class BundlePolicy
{
    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->companies()->exists(); }
    public function view(User $user, Bundle $bundle): bool
    {
        return $user->companies()->whereKey($bundle->company_id)->exists();
    }

    public function download(User $user, Bundle $bundle): bool
    {
        return $this->view($user, $bundle) && $bundle->status === 'completed' && $bundle->expires_at?->isFuture();
    }
}
