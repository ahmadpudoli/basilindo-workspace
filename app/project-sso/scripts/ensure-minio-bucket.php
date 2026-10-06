<?php

require __DIR__.'/../vendor/autoload.php';

Dotenv\Dotenv::createUnsafeImmutable(__DIR__.'/..')->safeLoad();

$client = new Aws\S3\S3Client([
    'version' => 'latest',
    'region' => getenv('AWS_DEFAULT_REGION') ?: 'us-east-1',
    'endpoint' => getenv('AWS_ENDPOINT'),
    'use_path_style_endpoint' => filter_var(getenv('AWS_USE_PATH_STYLE_ENDPOINT'), FILTER_VALIDATE_BOOL),
    'credentials' => [
        'key' => getenv('AWS_ACCESS_KEY_ID'),
        'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
    ],
]);

$bucket = getenv('AWS_BUCKET');

try {
    $client->headBucket(['Bucket' => $bucket]);
} catch (Throwable) {
    $client->createBucket(['Bucket' => $bucket]);
}

echo "bucket={$bucket}=ready\n";
