<?php

namespace App\Policies;

use App\Models\Bundle;
use App\Models\User;

class BundlePolicy
{
    public function view(User $user, Bundle $bundle): bool
    {
        return $user->companies()->whereKey($bundle->company_id)->exists();
    }

    public function download(User $user, Bundle $bundle): bool
    {
        return $this->view($user, $bundle) && $bundle->status === 'completed' && $bundle->expires_at?->isFuture();
    }
}
