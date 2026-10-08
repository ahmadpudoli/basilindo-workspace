<?php

namespace App\Policies;

use App\Models\Ticket;
use Core\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole('super_admin') || $user->projects()->exists(); }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('super_admin') || $ticket->project?->members()->whereKey($user->getKey())->exists();
    }

    public function create(User $user): bool { return $this->viewAny($user); }
    public function update(User $user, Ticket $ticket): bool { return $this->view($user, $ticket); }
    public function delete(User $user, Ticket $ticket): bool { return $this->view($user, $ticket) && $user->hasRole('super_admin'); }
}
