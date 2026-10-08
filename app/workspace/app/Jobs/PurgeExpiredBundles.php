<?php

namespace App\Jobs;

use App\Models\Bundle;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PurgeExpiredBundles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

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
