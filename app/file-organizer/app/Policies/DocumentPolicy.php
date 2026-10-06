<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool { return $user->companies()->exists() || $user->hasRole('super_admin'); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function view(User $user, Document $document): bool
    {
        return $user->companies()->whereKey($document->company_id)->exists();
    }

    public function download(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->hasRole('super_admin') && $this->view($user, $document);
    }

    public function update(User $user, Document $document): bool { return $this->view($user, $document); }
}
