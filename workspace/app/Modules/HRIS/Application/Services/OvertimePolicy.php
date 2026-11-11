<?php
namespace App\Modules\HRIS\Application\Services;
use Core\Models\User;
class OvertimePolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->can('view_any_overtime'); }
    public function view(User $user, \App\Modules\HRIS\Domain\Models\OvertimeRequest $request): bool { return $user->hasRole('super_admin') || (int) $request->requester_user_id === (int) $user->getKey() || ($user->can('view_overtime') && $user->companies()->whereKey($request->company_id)->exists()); }
    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_overtime'); }
    public function update(User $user, \App\Modules\HRIS\Domain\Models\OvertimeRequest $request): bool { return in_array($request->status, ['draft', 'needs_revision'], true) && ((int) $request->requester_user_id === (int) $user->getKey() || (int) $request->created_by === (int) $user->getKey()); }
}
