<?php

use Core\Models\Permission;
use Core\Models\Role;
use Core\Models\User;
use App\Modules\Core\Domain\Models\QueueJobLog;
use Illuminate\Support\Str;

it('separates access to general job logs from email queue logs', function () {
    $user = User::factory()->create();
    $user->givePermissionTo([
        Permission::firstOrCreate(['name' => 'access_settings', 'guard_name' => 'web']),
        Permission::firstOrCreate(['name' => 'view_queue_jobs', 'guard_name' => 'web']),
    ]);

    $this->actingAs($user)
        ->get(route('workspace.core.queue-jobs'))
        ->assertOk()
        ->assertSee('Log eksekusi job')
        ->assertDontSee(route('workspace.core.email-queue'), false);

    $this->actingAs($user)
        ->get(route('workspace.core.email-queue'))
        ->assertForbidden();
});

it('allows email queue access independently from general job logs', function () {
    $user = User::factory()->create();
    $user->givePermissionTo([
        Permission::firstOrCreate(['name' => 'access_settings', 'guard_name' => 'web']),
        Permission::firstOrCreate(['name' => 'view_email_queue', 'guard_name' => 'web']),
    ]);

    $this->actingAs($user)
        ->get(route('workspace.core.email-queue'))
        ->assertOk()
        ->assertSee('Antrean Email Notifikasi')
        ->assertDontSee(route('workspace.core.queue-jobs'), false);

    $this->actingAs($user)
        ->get(route('workspace.core.queue-jobs'))
        ->assertForbidden();
});

it('does not give the email queue to the general admin role by default', function () {
    $user = User::factory()->create();
    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $adminRole->syncPermissions([
        Permission::firstOrCreate(['name' => 'access_settings', 'guard_name' => 'web']),
        Permission::firstOrCreate(['name' => 'view_queue_jobs', 'guard_name' => 'web']),
    ]);
    $user->assignRole($adminRole);

    $this->actingAs($user)
        ->get(route('workspace.core.queue-jobs'))
        ->assertOk()
        ->assertSee('Log eksekusi job')
        ->assertDontSee(route('workspace.core.email-queue'), false);

    $this->actingAs($user)
        ->get(route('workspace.core.email-queue'))
        ->assertForbidden();
});

it('filters general queue logs by keyword, queue, status, and queued date', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'view_queue_jobs', 'guard_name' => 'web']));
    QueueJobLog::create([
        'job_id' => 'job-email-123', 'connection_name' => 'redis', 'queue_name' => 'email-notifications',
        'job_name' => 'App\\Modules\\Core\\Notifications\\Jobs\\SendEmailNotification', 'status' => 'succeeded',
        'attempts' => 1, 'queued_at' => now()->startOfDay()->addHours(8),
    ]);
    QueueJobLog::create([
        'job_id' => 'job-push-456', 'connection_name' => 'redis', 'queue_name' => 'notifications',
        'job_name' => 'App\\Modules\\Core\\Notifications\\Jobs\\SendWebPushNotification', 'status' => 'failed',
        'attempts' => 3, 'queued_at' => now()->startOfDay()->addHours(9),
    ]);

    $response = $this->actingAs($user)->get(route('workspace.core.queue-jobs', [
        'q' => 'SendEmailNotification', 'queue' => 'email-notifications', 'status' => 'succeeded',
        'from' => now()->toDateString(), 'until' => now()->toDateString(), 'per_page' => 25,
    ]));

    $response->assertOk()->assertSee('SendEmailNotification')->assertDontSee('SendWebPushNotification');
    expect($response->viewData('jobs')->total())->toBe(1)
        ->and($response->viewData('perPage'))->toBe(25)
        ->and($response->viewData('filters')['queue'])->toBe('email-notifications');
});

it('shows queue job details and only renders retry action for retry-authorized users', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'view_queue_jobs', 'guard_name' => 'web']));
    $failedUuid = (string) Str::uuid();
    $payload = json_encode([
        'uuid' => $failedUuid,
        'id' => 'failed-job-001',
        'job' => 'App\\Modules\\Integration\\Application\\Jobs\\RunOdooReferenceSync',
        'displayName' => 'App\\Modules\\Integration\\Application\\Jobs\\RunOdooReferenceSync',
        'data' => ['commandName' => 'App\\Modules\\Integration\\Application\\Jobs\\RunOdooReferenceSync'],
    ], JSON_THROW_ON_ERROR);
    app('queue.failer')->log('redis', 'odoo-sync', $payload, new RuntimeException('Connection failed'));
    $log = QueueJobLog::create([
        'job_id' => 'failed-job-001', 'connection_name' => 'redis', 'queue_name' => 'odoo-sync',
        'job_name' => 'App\\Modules\\Integration\\Application\\Jobs\\RunOdooReferenceSync',
        'status' => 'failed', 'attempts' => (int) config('workers.max_attempts', 3), 'max_attempts' => (int) config('workers.max_attempts', 3),
        'failed_job_uuid' => $failedUuid, 'error_message' => 'Connection failed', 'queued_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('workspace.core.queue-jobs'))
        ->assertOk()
        ->assertSee('Detail job')
        ->assertSee('queueJobDetailModal', false)
        ->assertDontSee('Antrikan ulang');

    $this->actingAs($user)
        ->post(route('workspace.core.queue-jobs.retry', $log))
        ->assertForbidden();

    $user->givePermissionTo(Permission::firstOrCreate(['name' => 'retry_queue_jobs', 'guard_name' => 'web']));
    $this->actingAs($user)
        ->get(route('workspace.core.queue-jobs'))
        ->assertOk()
        ->assertSee('Antrikan ulang');
});
