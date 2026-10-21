<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use SoftDeletes;

    protected $connection = 'core';

    protected $fillable = [
        'name', 'code', 'entity_type', 'classification', 'roles', 'parent_company_id', 'status',
        'logo_disk', 'logo_object_key', 'logo_mime_type', 'logo_file_size',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $company): void {
            if (blank($company->storage_key)) {
                $company->storage_key = (string) Str::ulid();
            }
        });

        static::updating(function (self $company): void {
            if ($company->isDirty('storage_key')) {
                $company->storage_key = $company->getOriginal('storage_key');
            }
        });
    }

    protected function casts(): array
    {
        return ['roles' => 'array'];
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('scope_role')->withTimestamps();
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_company_id');
    }

    public function subsidiaries()
    {
        return $this->hasMany(self::class, 'parent_company_id');
    }

    public function operationalVisibility()
    {
        return $this->belongsToMany(self::class, 'company_operational_visibility', 'company_id', 'operational_company_id')
            ->withTimestamps();
    }

    public function visibleCompanies()
    {
        return $this->belongsToMany(self::class, 'company_operational_visibility', 'operational_company_id', 'company_id')
            ->withTimestamps();
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        $allowedCompanyIds = $user->companies()->pluck('companies.id');

        if ($allowedCompanyIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $visibleQuery) use ($allowedCompanyIds): void {
            $visibleQuery
                ->whereIn($visibleQuery->qualifyColumn('id'), $allowedCompanyIds)
                ->orWhereDoesntHave('operationalVisibility')
                ->orWhereHas('operationalVisibility', function (Builder $operationalQuery) use ($allowedCompanyIds): void {
                    $operationalQuery->whereIn($operationalQuery->qualifyColumn('id'), $allowedCompanyIds);
                });
        });
    }

    public function scopeOperationalFor(Builder $query, User $user): Builder
    {
        $query
            ->where('entity_type', 'company')
            ->where('classification', 'internal')
            ->where('status', 'active')
            ->whereJsonContains('roles', 'operating_company');

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        return $query->whereIn($query->qualifyColumn('id'), $user->companies()->pluck('companies.id'));
    }

    public function projectParties()
    {
        return $this->hasMany(ProjectParty::class);
    }

    public function clientProjects()
    {
        return $this->belongsToMany(Project::class, 'project_parties')
            ->wherePivot('role', 'client')
            ->withPivot('role', 'is_primary')
            ->withTimestamps();
    }

    public function vendorProjects()
    {
        return $this->belongsToMany(Project::class, 'project_parties')
            ->wherePivot('role', 'vendor')
            ->withPivot('role', 'is_primary')
            ->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(config('core.models.document'));
    }
}
