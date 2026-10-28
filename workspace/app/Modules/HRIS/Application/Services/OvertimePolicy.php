<?php
namespace App\Modules\HRIS\Application\Services;
use Core\Models\User;
class OvertimePolicy { public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->can('view_any_overtime'); } public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_overtime'); } }
