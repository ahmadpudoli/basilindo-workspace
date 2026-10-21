<?php

namespace App\Modules\ProjectManagement\Domain\Models;

use Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $connection = 'core';
    protected $table = 'notifications';
    protected $fillable = ['user_id', 'type', 'title', 'message', 'data', 'read_at'];

    protected function casts(): array { return ['data' => 'array', 'read_at' => 'datetime']; }
    public function scopeUnread(Builder $query): Builder { return $query->whereNull('read_at'); }
    public function scopeRead(Builder $query): Builder { return $query->whereNotNull('read_at'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function markAsRead(): bool { $this->read_at = now(); return $this->save(); }
}
