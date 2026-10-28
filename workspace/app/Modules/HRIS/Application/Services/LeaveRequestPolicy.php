<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\LeaveRequest;
use Core\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->can('view_any_leave_request') || $user->can('access_hris'); }
    public function view(User $user, LeaveRequest $request): bool
    {
        if ($user->hasRole('super_admin')) return true;
        if ($user->can('view_leave_request') && $user->companies()->whereKey($request->company_id)->exists()) return true;
        return (int) $request->created_by === (int) $user->getKey() || (int) $request->employee?->user_id === (int) $user->getKey();
    }
    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_leave_request'); }
    public function update(User $user, LeaveRequest $request): bool
    {
        return $request->status === 'needs_revision'
            && ((int) $request->requester_user_id === (int) $user->getKey() || (int) $request->created_by === (int) $user->getKey());
    }
}
