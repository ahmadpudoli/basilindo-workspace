<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\HrisHoliday;
use Core\Models\User;

class HrisHolidayPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('view_hris_holidays');
    }

    public function view(User $user, HrisHoliday $holiday): bool
    {
        if (! $this->viewAny($user)) {
            return false;
        }

        return $user->hasRole('super_admin') || $holiday->is_global || $holiday->companyIds()->intersect($user->companies()->pluck('companies.id'))->isNotEmpty();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('create_hris_holiday');
    }

    public function update(User $user, HrisHoliday $holiday): bool
    {
        return ($user->hasRole('super_admin') || (! $holiday->is_global && $user->can('update_hris_holiday'))) && $this->view($user, $holiday);
    }

    public function delete(User $user, HrisHoliday $holiday): bool
    {
        return ($user->hasRole('super_admin') || (! $holiday->is_global && $user->can('delete_hris_holiday'))) && $this->view($user, $holiday);
    }
}
