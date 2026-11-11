<?php

namespace App\Modules\Integration\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class IntegrationSyncRun extends Model
{
    use HasUuids;

    protected $connection = 'core';
    protected $table = 'integration_sync_runs';
    protected $fillable = ['source_system', 'sync_type', 'status', 'started_at', 'finished_at', 'processed_count', 'created_count', 'updated_count', 'failed_count', 'message', 'metadata'];
    protected function casts(): array { return ['started_at' => 'datetime', 'finished_at' => 'datetime', 'metadata' => 'array']; }
}
