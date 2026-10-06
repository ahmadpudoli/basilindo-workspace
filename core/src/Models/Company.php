<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $connection = 'core';

    protected $fillable = [
        'name', 'code', 'entity_type', 'classification', 'roles', 'parent_company_id', 'status',
    ];

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
