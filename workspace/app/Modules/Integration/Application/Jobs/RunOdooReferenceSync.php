<?php

namespace App\Modules\Integration\Application\Jobs;

use App\Modules\Integration\Application\Services\OdooReferenceSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunOdooReferenceSync implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 3600;
    public int $tries = 3;
    public array $backoff = [60, 180];

    public function __construct(public bool $full = false)
    {
        $this->onQueue('odoo-sync');
    }

    public function uniqueId(): string
    {
        return $this->full
            ? 'integration:odoo-reference-sync:full'
            : 'integration:odoo-reference-sync:incremental';
    }

    public function handle(OdooReferenceSyncService $service): void
    {
        $service->run($this->full);
    }
}
