<?php

return [
    'application_prefixes' => [
        'document_management' => env('STORAGE_PREFIX_DOCUMENT_MANAGEMENT', 'app-document-management'),
        'project_management' => env('STORAGE_PREFIX_PROJECT_MANAGEMENT', 'app-project-management'),
        'crm' => env('STORAGE_PREFIX_CRM', 'app-crm'),
        'core' => env('STORAGE_PREFIX_CORE', 'app-core'),
    ],
];
