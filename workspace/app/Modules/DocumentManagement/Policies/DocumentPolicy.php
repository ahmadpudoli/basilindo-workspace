<?php

namespace App\Modules\DocumentManagement\Policies;

use App\Modules\DocumentManagement\Domain\Models\Document;
use App\Modules\DocumentManagement\Application\Services\Documents\DocumentCompanyScope;
use Core\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || ($user->can('access_documents') && $user->companies()->exists()); }
    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_document'); }
    public function review(User $user, Document $document): bool
    {
        return $this->view($user, $document)
            && ($user->hasRole('super_admin') || $user->can('update_document'));
    }
    public function view(User $user, Document $document): bool
    {
        return ($user->hasRole('super_admin') || $user->can('view_document'))
            && app(DocumentCompanyScope::class)->canAccess($user, (int) $document->company_id);
    }

    public function viewAudit(User $user, Document $document): bool
    {
        return $this->view($user, $document)
            && ($user->hasRole('super_admin') || $user->can('view_document_audit_trail'));
    }

    public function download(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }

    public function preview(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }

    public function delete(User $user, Document $document): bool
    {
        return ($user->hasRole('super_admin') || $user->can('delete_document')) && $this->view($user, $document);
    }

    public function update(User $user, Document $document): bool { return ($user->hasRole('super_admin') || $user->can('update_document')) && $this->view($user, $document); }
}
