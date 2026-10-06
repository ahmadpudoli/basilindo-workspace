<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsoApplication extends Model
{
    protected $fillable = ['code', 'name', 'url', 'oauth_client_id', 'icon', 'sort_order', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function users() { return $this->belongsToMany(User::class, 'application_user_access', 'application_id', 'user_id')->withPivot(['role_code', 'status'])->withTimestamps(); }
    public function accesses() { return $this->hasMany(ApplicationUserAccess::class, 'application_id'); }
}
