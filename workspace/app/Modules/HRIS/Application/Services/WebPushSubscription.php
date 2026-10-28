<?php

namespace App\Modules\HRIS\Application\Services;

use Illuminate\Database\Eloquent\Model;

class WebPushSubscription extends Model
{
    protected $table = 'web_push_subscriptions';

    protected $fillable = ['user_id', 'endpoint', 'public_key', 'auth_token', 'content_encoding', 'user_agent', 'last_used_at'];

    protected function casts(): array
    {
        return ['last_used_at' => 'datetime'];
    }
}
