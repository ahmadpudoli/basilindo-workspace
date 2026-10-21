<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

Redis::set('file-organizer:phase0', 'ok');

if (Redis::get('file-organizer:phase0') !== 'ok') {
    throw new RuntimeException('Redis round-trip failed.');
}

$disk = Storage::disk('s3');
$object = 'health-check/phase-0.txt';

$disk->put($object, 'file-organizer-ok');

if (! $disk->exists($object)) {
    throw new RuntimeException('MinIO object was not found after upload.');
}

$disk->delete($object);

echo "redis=minio=passed\n";
