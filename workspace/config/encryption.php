<?php

return [
    /* Shared payload encryption for application data; Laravel APP_KEY stays separate. */
    'payload' => [
        // Legacy environment fallback preserves decryption of already queued email bodies.
        'key' => env('APP_DATA_ENCRYPTION_KEY') ?: env('NOTIFICATION_EMAIL_SHARED_SECRET'),
        'previous_keys' => array_filter([
            ...explode(',', env('APP_DATA_ENCRYPTION_PREVIOUS_KEYS', '')),
            env('NOTIFICATION_EMAIL_SHARED_SECRET'),
        ]),
        'cipher' => 'aes-256-gcm',
        'iv_bytes' => 12,
        'tag_bytes' => 16,
    ],
];
