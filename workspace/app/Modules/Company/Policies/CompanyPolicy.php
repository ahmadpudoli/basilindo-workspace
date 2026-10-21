<?php

namespace App\Modules\Company\Policies;

use Core\Models\Company;
use Core\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->companies()->exists(); }
    public function view(User $user, Company $company): bool { return Company::query()->whereKey($company->getKey())->visibleTo($user)->exists(); }
    public function create(User $user): bool { return $user->hasRole('super_admin'); }
    public function update(User $user, Company $company): bool { return $user->hasRole('super_admin') || $this->view($user, $company); }
    public function delete(User $user, Company $company): bool { return $user->hasRole('super_admin'); }
}
