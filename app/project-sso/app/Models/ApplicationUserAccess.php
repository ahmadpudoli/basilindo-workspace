<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApplicationUserAccess extends Model
{
    protected $table = 'application_user_access';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['application_id', 'user_id', 'role_code', 'status'];

    protected static function booted(): void
    {
        static::created(fn (self $access) => $access->audit('created'));
        static::updated(function (self $access): void {
            $access->audit($access->wasChanged('status') ? 'status_changed' : 'updated');
        });
        static::deleted(fn (self $access) => $access->audit('deleted'));
    }

    private function audit(string $action): void
    {
        Log::info('application_access_changed', [
            'actor_id' => Auth::id(),
            'access_id' => $this->getKey(),
            'action' => $action,
            'status' => $this->status,
        ]);
    }

    public function application() { return $this->belongsTo(SsoApplication::class); }
    public function user() { return $this->belongsTo(User::class); }
}
