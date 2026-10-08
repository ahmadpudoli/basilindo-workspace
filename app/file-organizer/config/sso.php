<?php

return [
    'enabled' => (bool) env('SSO_ENABLED', false),
    'issuer_url' => env('SSO_ISSUER_URL', 'http://localhost:8000'),
    'client_id' => env('SSO_CLIENT_ID', 'file-organizer'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_uri' => env('SSO_REDIRECT_URI'),
    'scopes' => ['openid', 'profile', 'email'],
    'role_map' => [
        'member' => 'member',
        'admin' => 'admin',
        'super_admin' => 'super_admin',
        'finance_admin' => 'finance_admin',
        'finance_reviewer' => 'finance_reviewer',
        'document_admin' => 'document_admin',
        'crm_admin' => 'crm_admin',
        'crm_member' => 'crm_member',
        'project_admin' => 'project_admin',
        'project_member' => 'project_member',
    ],
    'provider' => env('SSO_PROVIDER', 'project-sso'),
    // Local recovery account linking must be explicitly enabled; keep this
    // disabled in production.
    'allow_system_account_linking' => (bool) env('SSO_ALLOW_SYSTEM_ACCOUNT_LINKING', false),
    'access_sync_enabled' => (bool) env('SSO_ACCESS_SYNC_ENABLED', false),
    'access_sync_client_id' => env('SSO_ACCESS_SYNC_CLIENT_ID'),
    'access_sync_client_secret' => env('SSO_ACCESS_SYNC_CLIENT_SECRET'),
    'access_sync_application' => env('SSO_ACCESS_SYNC_APPLICATION', 'file-organizer'),
];
