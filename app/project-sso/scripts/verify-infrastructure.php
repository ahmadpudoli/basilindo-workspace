<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

Redis::set('project-sso:phase', 'ok');
if (Redis::get('project-sso:phase') !== 'ok') {
    throw new RuntimeException('Redis round-trip failed.');
}

$disk = Storage::disk('s3');
$object = 'health-check/project-sso.txt';
$disk->put($object, 'project-sso-ok');
if (! $disk->exists($object)) {
    throw new RuntimeException('MinIO object was not found after upload.');
}
$disk->delete($object);

echo "redis=minio=passed\n";
