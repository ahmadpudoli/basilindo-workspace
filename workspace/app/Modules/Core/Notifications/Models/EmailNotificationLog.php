<?php

namespace App\Modules\Core\Notifications\Models;

use Illuminate\Database\Eloquent\Model;

class EmailNotificationLog extends Model
{
    protected $connection = 'core';
    protected $table = 'email_notification_logs';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['queued_at' => 'datetime', 'sent_at' => 'datetime'];
    }
}
