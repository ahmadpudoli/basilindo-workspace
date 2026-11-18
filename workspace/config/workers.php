<?php

use App\Modules\Core\Notifications\Jobs\SendEmailNotification;
use App\Modules\Core\Notifications\Jobs\SendWebPushNotification;
use App\Modules\DocumentManagement\Application\Jobs\GenerateDocumentBundle;
use App\Modules\DocumentManagement\Application\Jobs\PurgeExpiredBundles;
use App\Modules\Integration\Application\Jobs\RunOdooReferenceSync;

/* Central inventory for queue workers. Add every queued job here. */
return [
    'max_attempts' => (int) env('QUEUE_WORKER_MAX_ATTEMPTS', 3),
    'groups' => [
        'notifications' => [
            'queues' => ['notifications'],
            'jobs' => [SendWebPushNotification::class],
            'dispatched_by' => ['App\\Modules\\HRIS\\Application\\Services\\HrisWorkflowService::notify'],
        ],
        'email' => [
            'queues' => ['email-notifications'],
            'jobs' => [SendEmailNotification::class],
            'dispatched_by' => ['App\\Providers\\AppServiceProvider notification-created listener -> EmailNotificationDispatcher'],
        ],
        'documents' => [
            'queues' => ['document-bundles', 'document-cleanup'],
            'jobs' => [GenerateDocumentBundle::class, PurgeExpiredBundles::class],
            'dispatched_by' => [
                'App\\Modules\\DocumentManagement\\Http\\Controllers\\DocumentManagementController::requestBundle',
                'workspace/routes/console.php scheduled PurgeExpiredBundles',
            ],
        ],
        'integration' => [
            'queues' => ['odoo-sync'],
            'jobs' => [RunOdooReferenceSync::class],
            'dispatched_by' => [
                'App\\Modules\\Integration\\Console\\Commands\\SyncOdooReferences',
                'App\\Modules\\Integration\\Http\\Controllers\\IntegrationController',
            ],
        ],
        'default' => [
            'queues' => ['default'],
            'jobs' => [],
        ],
    ],
];
