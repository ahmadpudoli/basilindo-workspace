<?php

return [
    'enabled' => (bool) env('SSO_ENABLED', false),
    'issuer_url' => env('SSO_ISSUER_URL'),
    'client_id' => env('SSO_CLIENT_ID', 'project-management'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_uri' => env('SSO_REDIRECT_URI'),
    'scopes' => ['openid', 'profile', 'email'],
    'role_map' => [
        'member' => 'member',
        'admin' => 'admin',
        'super_admin' => 'super_admin',
    ],
    'allow_system_account_linking' => (bool) env('SSO_ALLOW_SYSTEM_ACCOUNT_LINKING', false),
];
