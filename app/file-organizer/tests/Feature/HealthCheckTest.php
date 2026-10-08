<?php

use App\Services\Health\HealthCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('reports readiness without exposing connection or business data', function () {
    Storage::fake('s3');

    $response = $this->getJson(route('health.ready'));

    $response->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonStructure([
            'status',
            'checks' => ['database' => ['status'], 'queue' => ['status'], 'storage' => ['status']],
            'checked_at',
        ])
        ->assertJsonMissing(['password', 'secret', 'token']);
});

it('returns service unavailable when a critical health check fails', function () {
    $this->app->instance(HealthCheckService::class, new class extends HealthCheckService
    {
        public function report(): array
        {
            return [
                'status' => 'failed',
                'checks' => ['database' => ['status' => 'failed']],
                'checked_at' => now()->toIso8601String(),
            ];
        }
    });

    $this->getJson(route('health.ready'))
        ->assertStatus(503)
        ->assertJsonPath('status', 'failed');
});
