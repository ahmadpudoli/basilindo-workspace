<?php

use App\Modules\Integration\Application\Jobs\RunOdooReferenceSync;
use Illuminate\Support\Facades\Queue;

it('dispatches incremental Odoo sync to the queue', function () {
    Queue::fake();

    $this->artisan('integration:sync-odoo')
        ->expectsOutput('Job sinkronisasi Odoo berhasil dimasukkan ke queue.')
        ->assertSuccessful();

    Queue::assertPushed(RunOdooReferenceSync::class, fn (RunOdooReferenceSync $job): bool => $job->full === false);
});

it('dispatches full Odoo sync to the queue', function () {
    Queue::fake();

    $this->artisan('integration:sync-odoo --full')
        ->assertSuccessful();

    Queue::assertPushed(RunOdooReferenceSync::class, fn (RunOdooReferenceSync $job): bool => $job->full === true);
});
