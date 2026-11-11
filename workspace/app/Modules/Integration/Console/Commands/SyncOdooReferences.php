<?php

namespace App\Modules\Integration\Console\Commands;

use App\Modules\Integration\Application\Jobs\RunOdooReferenceSync;
use Illuminate\Console\Command;

class SyncOdooReferences extends Command
{
    protected $signature = 'integration:sync-odoo {--full : Ambil seluruh projection referensi}';
    protected $description = 'Sinkronkan referensi Odoo secara read-only ke Workspace';

    public function handle(): int
    {
        RunOdooReferenceSync::dispatch((bool) $this->option('full'));
        $this->info('Job sinkronisasi Odoo berhasil dimasukkan ke queue.');

        return self::SUCCESS;
    }
}
