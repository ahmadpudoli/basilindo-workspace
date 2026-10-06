<?php

namespace Core\Models;

use Core\Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, HasRoles, MustVerifyEmailTrait, Notifiable;

    protected $connection = 'core';

    protected $fillable = [
        'name', 'email', 'email_verified_at', 'password', 'google_id', 'is_system_account',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected static function booted(): void
    {
        static::deleting(function (self $user): void {
            if ($user->isProtectedSystemAccount()) {
                throw new \LogicException('System account cannot be deleted.');
            }
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_system_account' => 'boolean',
        ];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function isProtectedSystemAccount(): bool
    {
        return (bool) $this->is_system_account || strtolower((string) $this->email) === 'admin@example.com';
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_members')->withTimestamps();
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)->withPivot('scope_role')->withTimestamps();
    }

    public function externalIdentities(): HasMany
    {
        return $this->hasMany(ExternalIdentity::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(config('core.models.ticket'));
    }

    public function assignedTickets(): BelongsToMany
    {
        return $this->belongsToMany(config('core.models.ticket'), 'ticket_users');
    }

    public function createdTickets(): HasMany
    {
        return $this->hasMany(config('core.models.ticket'), 'created_by');
    }

    public function isAssignedToTicket(object $ticket): bool
    {
        return $this->assignedTickets()->where('ticket_id', $ticket->getKey())->exists();
    }

    public function assignToTicket(object $ticket): void
    {
        $this->assignedTickets()->syncWithoutDetaching($ticket->getKey());
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(config('core.models.notification'))->orderByDesc('created_at');
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(config('core.models.notification'))
            ->unread()
            ->orderByDesc('created_at');
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail() && $this->roles()->exists();
    }
}
