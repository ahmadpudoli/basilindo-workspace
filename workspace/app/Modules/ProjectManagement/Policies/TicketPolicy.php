<?php

namespace App\Modules\ProjectManagement\Policies;

use App\Modules\ProjectManagement\Domain\Models\Ticket;
use Core\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || ($user->can('access_project_management') && $user->projects()->exists()); }

    public function view(User $user, Ticket $ticket): bool
    {
        return ($user->hasRole('super_admin') || $user->can('view_ticket')) && $ticket->project?->members()->whereKey($user->getKey())->exists();
    }

    public function create(User $user): bool { return $user->hasRole('super_admin') || $user->can('create_ticket'); }
    public function update(User $user, Ticket $ticket): bool { return ($user->hasRole('super_admin') || $user->can('update_ticket')) && $this->view($user, $ticket); }
    public function delete(User $user, Ticket $ticket): bool { return ($user->hasRole('super_admin') || $user->can('delete_ticket')) && $this->view($user, $ticket); }
}
