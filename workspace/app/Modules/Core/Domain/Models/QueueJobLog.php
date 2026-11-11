<?php

namespace App\Modules\Core\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class QueueJobLog extends Model
{
    protected $connection = 'core';
    protected $table = 'queue_job_logs';
    protected $guarded = [];
    protected function casts(): array
    {
        return ['queued_at' => 'datetime', 'started_at' => 'datetime', 'finished_at' => 'datetime'];
    }
}
