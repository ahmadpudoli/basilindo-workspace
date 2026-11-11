<?php

return [
    'models' => [
        'document' => App\Modules\DocumentManagement\Domain\Models\Document::class,
        // Core exposes configurable model bindings so feature modules can own
        // their domain models without falling back to the removed legacy
        // App\Models namespace.
        'ticket' => App\Modules\ProjectManagement\Domain\Models\Ticket::class,
        'ticket_status' => App\Modules\ProjectManagement\Domain\Models\TicketStatus::class,
        'ticket_priority' => App\Modules\ProjectManagement\Domain\Models\TicketPriority::class,
        'epic' => App\Modules\ProjectManagement\Domain\Models\Epic::class,
        'project_note' => App\Modules\ProjectManagement\Domain\Models\ProjectNote::class,
        'notification' => App\Modules\ProjectManagement\Domain\Models\Notification::class,
    ],
];
