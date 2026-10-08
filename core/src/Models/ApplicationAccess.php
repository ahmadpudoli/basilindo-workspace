<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationAccess extends Model
{
    protected $connection = 'core';

    protected $table = 'application_access';

    protected $fillable = [
        'application_code', 'subject', 'user_id', 'status', 'synced_at',
    ];

    protected function casts(): array
    {
        return ['synced_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
