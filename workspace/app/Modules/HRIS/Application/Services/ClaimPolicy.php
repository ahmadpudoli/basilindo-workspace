<?php
namespace App\Modules\HRIS\Application\Services;
use Core\Models\User;
class ClaimPolicy { public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->can('view_any_claim'); } public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_claim'); } }
