<?php

namespace App\Modules\Company\Policies;

use Core\Models\User;
use Core\Models\Vendor;

class VendorPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || ($user->can('access_company') && $user->companies()->exists()); }
    public function view(User $user, Vendor $vendor): bool { return ($user->hasRole('super_admin') || $user->can('view_vendor')) && ($vendor->operationalCompanies()->whereIn('companies.id', $user->companies()->pluck('companies.id'))->exists() || ($vendor->company_id && $user->companies()->whereKey($vendor->company_id)->exists())); }
    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_vendor'); }
    public function update(User $user, Vendor $vendor): bool { return ($user->hasRole('super_admin') || $user->can('update_vendor')) && $this->view($user, $vendor); }
    public function delete(User $user, Vendor $vendor): bool { return ($user->hasRole('super_admin') || $user->can('delete_vendor')) && $this->view($user, $vendor); }
}
