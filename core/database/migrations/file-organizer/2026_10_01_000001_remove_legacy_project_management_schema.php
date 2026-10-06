<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Legacy project-management tables are no longer part of File Organizer.
        // Drop children before parents to satisfy PostgreSQL foreign keys.
        foreach ([
            'ticket_comments',
            'ticket_histories',
            'ticket_users',
            'tickets',
            'epics',
            'ticket_statuses',
            'ticket_priorities',
            'project_notes',
            'external_access',
            'notifications',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        $legacyPermissions = [
            'ticket',
            'ticket_priority',
            'ticket_comment',
            'notification',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', collect($legacyPermissions)
                ->flatMap(fn (string $resource): array => collect(['view', 'view_any', 'create', 'update', 'delete'])
                    ->map(fn (string $action): string => $action.'_'.$resource)
                    ->all())
                ->all())
            ->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
            DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        }
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Legacy project-management schema was removed intentionally. Restore it only from a reviewed backup/schema migration.'
        );
    }
};
