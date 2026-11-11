<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class OrganizationUnit extends Model
{
    use SoftDeletes;

    protected $connection = 'core';
    protected $table = 'organization_units';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['company_id', 'parent_id', 'code', 'name', 'type', 'status', 'sort_order'];
    protected function casts(): array { return ['sort_order' => 'integer']; }
    protected static function booted(): void { static::creating(fn (self $unit) => $unit->id ??= (string) Str::ulid()); }
    public function company() { return $this->belongsTo(Company::class); }
    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('name'); }
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->hasRole('super_admin') ? $query : $query->whereIn('company_id', $user->companies()->pluck('companies.id'));
    }
}
