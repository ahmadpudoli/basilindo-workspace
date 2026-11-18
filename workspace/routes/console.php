<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Modules\DocumentManagement\Application\Jobs\PurgeExpiredBundles;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('workspace:queue-work {groups}', function (string $groups): int {
    $groups = (array) config('workers.groups', []);
    $requestedGroups = array_values(array_unique(array_filter(array_map('trim', explode(',', (string) $this->argument('groups'))))));

    if ($requestedGroups === []) {
        $this->error('Sebutkan minimal satu worker group dari config/workers.php.');
        return 1;
    }

    $unknownGroups = array_values(array_diff($requestedGroups, array_keys($groups)));
    if ($unknownGroups !== []) {
        $this->error('Worker group tidak ditemukan di config/workers.php: '.implode(', ', $unknownGroups));
        return 1;
    }

    $queues = collect($requestedGroups)
        ->flatMap(fn (string $group): array => $groups[$group]['queues'] ?? [])
        ->unique()
        ->values()
        ->all();

    if ($queues === []) {
        $this->error('Worker group tidak memiliki queue.');
        return 1;
    }

    return $this->call('queue:work', [
        'connection' => config('queue.default'),
        '--queue' => implode(',', $queues),
        '--tries' => (int) config('workers.max_attempts', 3),
        '--timeout' => 300,
        '--sleep' => 3,
    ]);
})->purpose('Menjalankan worker queue sesuai config/workers.php');

Schedule::command('workspace:cleanup-temp-uploads')->hourly();
Schedule::command('hris:cleanup-claim-temp-uploads')->everyTenMinutes()->withoutOverlapping();
Schedule::command('integration:sync-odoo')
    ->everyMinute()
    ->withoutOverlapping()
    ->when(fn (): bool => (bool) env('ODOO_SYNC_ENABLED', false));
Schedule::job(new PurgeExpiredBundles())->hourly()->withoutOverlapping();

