<?php

return [
    'enabled' => (bool) env('SSO_ENABLED', false),
    'issuer_url' => env('SSO_ISSUER_URL'),
    'client_id' => env('SSO_CLIENT_ID', 'file-organizer'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_uri' => env('SSO_REDIRECT_URI'),
    'scopes' => ['openid', 'profile', 'email'],
    'role_map' => [
        'admin' => 'admin',
        'super_admin' => 'super_admin',
        'finance_admin' => 'finance_admin',
        'finance_reviewer' => 'finance_reviewer',
        'document_admin' => 'document_admin',
    ],
    'provider' => env('SSO_PROVIDER', 'project-sso'),
    // Local recovery account linking must be explicitly enabled; keep this
    // disabled in production.
    'allow_system_account_linking' => (bool) env('SSO_ALLOW_SYSTEM_ACCOUNT_LINKING', false),
];
