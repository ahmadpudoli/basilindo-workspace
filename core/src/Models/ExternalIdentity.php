<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalIdentity extends Model
{
    protected $connection = 'core';

    protected $fillable = ['user_id', 'issuer', 'subject', 'provider', 'claims', 'last_login_at'];

    protected function casts(): array
    {
        return ['claims' => 'array', 'last_login_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
