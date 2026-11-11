<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('workspace:cleanup-temp-uploads')->hourly();
Schedule::command('integration:sync-odoo')
    ->everyMinute()
    ->withoutOverlapping()
    ->when(fn (): bool => (bool) env('ODOO_SYNC_ENABLED', false));

