<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\Employee;
use Core\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->can('view_any_employee'); }
    public function view(User $user, Employee $employee): bool { return ($user->hasRole('super_admin') || $user->can('view_employee')) && $this->scope($user, $employee); }
    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_employee'); }
    public function update(User $user, Employee $employee): bool { return ($user->hasRole('super_admin') || $user->can('update_employee')) && $this->scope($user, $employee); }
    public function delete(User $user, Employee $employee): bool { return ($user->hasRole('super_admin') || $user->can('delete_employee')) && $this->scope($user, $employee); }
    private function scope(User $user, Employee $employee): bool { return $user->hasRole('super_admin') || $user->companies()->whereKey($employee->company_id)->exists(); }
}
