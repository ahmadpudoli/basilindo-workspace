<?php

namespace App\Modules\HRIS\Application\Services;

use App\Modules\HRIS\Domain\Models\EmployeeDocument;
use Core\Models\User;

class EmployeeDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('view_employee_documents') || $user->can('view_own_employee_documents');
    }

    public function view(User $user, EmployeeDocument $document): bool
    {
        return $this->inScope($user, $document) && ($user->hasRole('super_admin') || $user->can('view_employee_documents') || $this->isOwner($user, $document) && $user->can('view_own_employee_documents'));
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('upload_employee_documents');
    }

    public function download(User $user, EmployeeDocument $document): bool
    {
        return $this->inScope($user, $document) && ($user->hasRole('super_admin') || $user->can('download_employee_documents') || $user->can('view_employee_documents') || $this->isOwner($user, $document) && $user->can('view_own_employee_documents'));
    }

    public function delete(User $user, EmployeeDocument $document): bool
    {
        return $this->inScope($user, $document) && ($user->hasRole('super_admin') || $user->can('manage_employee_documents'));
    }

    private function inScope(User $user, EmployeeDocument $document): bool
    {
        return $user->hasRole('super_admin') || $user->companies()->whereKey($document->company_id)->exists();
    }

    private function isOwner(User $user, EmployeeDocument $document): bool
    {
        return $document->employee?->user_id === $user->getKey();
    }
}
