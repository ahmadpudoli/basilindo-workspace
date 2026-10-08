<?php

use App\Jobs\GenerateDocumentBundle;

it('defines bounded retry and deterministic idempotency for bundle generation', function () {
    $job = new GenerateDocumentBundle('bundle-id');

    expect($job->tries)->toBe(3)
        ->and($job->backoff)->toBe([30, 120, 300])
        ->and($job->bundleId)->toBe('bundle-id');
});
