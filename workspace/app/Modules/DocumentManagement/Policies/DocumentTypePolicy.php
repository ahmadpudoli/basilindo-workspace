<?php

namespace App\Modules\DocumentManagement\Policies;

use Core\Models\User;

class DocumentTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin')
            || ($user->can('access_documents') && $user->companies()->exists());
    }

    public function view(User $user, object $type): bool
    {
        return $user->hasRole('super_admin')
            || ($user->can('view_document_type') && $user->companies()->exists());
    }

    public function viewAudit(User $user, object $type): bool
    {
        return $this->view($user, $type)
            && ($user->hasRole('super_admin') || $user->can('view_document_audit_trail'));
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin')
            || ($user->can('create_document_type') && $user->companies()->exists());
    }

    public function update(User $user, object $type): bool
    {
        return $this->view($user, $type)
            && ($user->hasRole('super_admin') || $user->can('update_document_type'));
    }

    public function delete(User $user, object $type): bool
    {
        return $this->view($user, $type)
            && ($user->hasRole('super_admin') || $user->can('delete_document_type'));
    }
}
