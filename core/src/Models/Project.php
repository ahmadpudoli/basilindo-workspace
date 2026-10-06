<?php

namespace Core\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $connection = 'core';

    protected $fillable = [
        'company_id', 'name', 'description', 'ticket_prefix', 'color', 'start_date', 'end_date', 'pinned_date',
    ];

    protected $casts = [
        'start_date' => 'date', 'end_date' => 'date', 'pinned_date' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')->withTimestamps();
    }

    public function parties(): HasMany
    {
        return $this->hasMany(config('core.models.project_party', ProjectParty::class));
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'project_parties')
            ->wherePivot('role', 'client')
            ->withPivot('role', 'is_primary')
            ->withTimestamps();
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'project_parties')
            ->wherePivot('role', 'vendor')
            ->withPivot('role', 'is_primary')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->members();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(config('core.models.document'));
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(config('core.models.ticket'));
    }

    public function ticketStatuses(): HasMany
    {
        return $this->hasMany(config('core.models.ticket_status'));
    }

    public function epics(): HasMany
    {
        return $this->hasMany(config('core.models.epic'));
    }

    public function notes(): HasMany
    {
        return $this->hasMany(config('core.models.project_note'));
    }

    public function externalAccess(): HasOne
    {
        return $this->hasOne(config('core.models.external_access'));
    }

    public function getProgressPercentageAttribute(): float
    {
        $totalTickets = $this->tickets()->count();
        if ($totalTickets === 0) {
            return 0.0;
        }

        $completedTickets = $this->tickets()
            ->whereHas('status', fn ($query) => $query->where('is_completed', true))
            ->count();

        return round(($completedTickets / $totalTickets) * 100, 1);
    }

    public function generateExternalAccess(): object
    {
        $this->externalAccess()?->delete();

        $model = config('core.models.external_access');
        return $model::generateForProject($this->getKey());
    }

    public function getIsPinnedAttribute(): bool
    {
        return $this->pinned_date !== null;
    }

    public function pin(): void
    {
        $this->update(['pinned_date' => now()]);
    }

    public function unpin(): void
    {
        $this->update(['pinned_date' => null]);
    }

    public function getRemainingDaysAttribute(): ?int
    {
        if (! $this->end_date) {
            return null;
        }

        $today = Carbon::today();
        $endDate = Carbon::parse($this->end_date);

        return $today->gt($endDate) ? 0 : $today->diffInDays($endDate);
    }
}
