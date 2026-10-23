<?php

namespace App\Modules\Company\Policies;

use Core\Models\Company;
use Core\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || ($user->can('access_company') && $user->companies()->exists()); }
    public function view(User $user, Company $company): bool { return ($user->hasRole('super_admin') || $user->can('view_company')) && Company::query()->whereKey($company->getKey())->visibleTo($user)->exists(); }
    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_company'); }
    public function update(User $user, Company $company): bool { return ($user->hasRole('super_admin') || $user->can('update_company')) && $this->view($user, $company); }
    public function delete(User $user, Company $company): bool { return ($user->hasRole('super_admin') || $user->can('delete_company')) && $this->view($user, $company); }
}
