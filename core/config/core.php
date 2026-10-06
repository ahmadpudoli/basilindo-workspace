<?php

return [
    'models' => [
        'document' => env('CORE_DOCUMENT_MODEL', 'App\\Models\\Document'),
        'ticket' => env('CORE_TICKET_MODEL', 'App\\Models\\Ticket'),
        'ticket_status' => env('CORE_TICKET_STATUS_MODEL', 'App\\Models\\TicketStatus'),
        'ticket_priority' => env('CORE_TICKET_PRIORITY_MODEL', 'App\\Models\\TicketPriority'),
        'epic' => env('CORE_EPIC_MODEL', 'App\\Models\\Epic'),
        'project_note' => env('CORE_PROJECT_NOTE_MODEL', 'App\\Models\\ProjectNote'),
        'notification' => env('CORE_NOTIFICATION_MODEL', 'App\\Models\\Notification'),
        'external_access' => env('CORE_EXTERNAL_ACCESS_MODEL', 'App\\Models\\ExternalAccess'),
        'project_party' => env('CORE_PROJECT_PARTY_MODEL', 'Core\\Models\\ProjectParty'),
    ],
];
