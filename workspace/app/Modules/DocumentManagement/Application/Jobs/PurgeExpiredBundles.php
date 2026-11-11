<?php

namespace App\Modules\DocumentManagement\Application\Jobs;

use App\Modules\DocumentManagement\Domain\Models\Bundle;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PurgeExpiredBundles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('document-cleanup');
    }

    public function handle(): void
    {
        Bundle::where('status', 'completed')->whereNotNull('expires_at')->where('expires_at', '<=', now())->each(function (Bundle $bundle) {
            if ($bundle->object_key) {
                Storage::disk($bundle->disk)->delete($bundle->object_key);
            }
            $bundle->newQuery()->whereKey($bundle->getKey())->where('status', 'completed')->update([
                'status' => 'expired',
                'object_key' => null,
            ]);
        });
    }
}
