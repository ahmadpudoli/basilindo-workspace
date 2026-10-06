<?php

namespace App\Jobs;

use App\Models\Bundle;
use App\Services\Bundles\DocumentBundleService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateDocumentBundle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(public string $bundleId) {}

    public function handle(DocumentBundleService $service): void
    {
        $bundle = Bundle::findOrFail($this->bundleId);
        $service->generate($bundle);
    }
}
