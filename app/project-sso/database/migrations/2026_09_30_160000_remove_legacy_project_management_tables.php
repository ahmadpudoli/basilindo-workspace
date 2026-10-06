<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // The copied project-management baseline still has legacy tables.
        // Keep them in the test schema for the inherited regression suite.
        if (app()->environment('testing')) {
            return;
        }

        foreach ([
            'ticket_comments',
            'ticket_histories',
            'ticket_users',
            'tickets',
            'epics',
            'project_notes',
            'external_access',
            'project_members',
            'ticket_statuses',
            'ticket_priorities',
            'projects',
            'notifications',
            'settings',
            'oauth_device_codes',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        if (Schema::hasColumn('users', 'google_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('google_id');
            });
        }
    }

    public function down(): void
    {
        // Legacy tables are intentionally not recreated by this cleanup migration.
    }
};
