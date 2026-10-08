<?php

namespace App\Services\Health;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HealthCheckService
{
    /**
     * @return array{status: string, checks: array<string, array<string, mixed>>, checked_at: string}
     */
    public function report(): array
    {
        $checks = [
            'database' => $this->database(),
            'queue' => $this->queue(),
            'storage' => $this->storage(),
        ];

        $healthy = collect($checks)->every(fn (array $check): bool => $check['status'] !== 'failed');

        return [
            'status' => $healthy ? 'ok' : 'failed',
            'checks' => $checks,
            'checked_at' => now()->toIso8601String(),
        ];
    }

    private function database(): array
    {
        try {
            DB::connection()->select('select 1');
            return ['status' => 'ok'];
        } catch (Throwable) {
            return ['status' => 'failed'];
        }
    }

    private function queue(): array
    {
        $connection = (string) config('queue.default');
        $queue = (string) config("queue.connections.$connection.queue", 'default');
        $check = ['status' => 'ok', 'connection' => $connection, 'queue' => $queue];

        try {
            if ($connection === 'redis') {
                Redis::connection(config('queue.connections.redis.connection', 'default'))->ping();
            }

            $queueConnection = Queue::connection($connection);
            $check['depth'] = method_exists($queueConnection, 'size') ? (int) $queueConnection->size($queue) : null;

            if (Schema::hasTable('failed_jobs')) {
                $check['failed_jobs'] = (int) DB::table('failed_jobs')->count();
            }

            return $check;
        } catch (Throwable) {
            return ['status' => 'failed', 'connection' => $connection, 'queue' => $queue];
        }
    }

    private function storage(): array
    {
        $diskName = (string) config('filesystems.health_disk', 's3');

        try {
            $disk = Storage::disk($diskName);
            $driver = $disk->getDriver();

            if (method_exists($driver, 'getClient')) {
                $bucket = (string) config("filesystems.disks.$diskName.bucket");
                $driver->getClient()->headBucket(['Bucket' => $bucket]);
            }

            return ['status' => 'ok', 'disk' => $diskName];
        } catch (Throwable) {
            return ['status' => 'failed', 'disk' => $diskName];
        }
    }
}
