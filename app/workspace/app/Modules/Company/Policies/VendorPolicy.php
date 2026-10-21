<?php

namespace App\Modules\Company\Policies;

use Core\Models\User;
use Core\Models\Vendor;

class VendorPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->companies()->exists(); }
    public function view(User $user, Vendor $vendor): bool { return $user->hasRole('super_admin') || $vendor->operationalCompanies()->whereIn('companies.id', $user->companies()->pluck('companies.id'))->exists() || ($vendor->company_id && $user->companies()->whereKey($vendor->company_id)->exists()); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function update(User $user, Vendor $vendor): bool { return $this->view($user, $vendor); }
    public function delete(User $user, Vendor $vendor): bool { return $user->hasRole('super_admin') && $this->view($user, $vendor); }
}
