<?php

return [
    'claim_attachment_disk' => env('HRIS_DOCUMENT_DISK', env('FILESYSTEM_DISK', 's3')),
    'claim_attachment_temp_ttl_minutes' => (int) env('HRIS_CLAIM_TEMP_UPLOAD_TTL_MINUTES', 30),
];
