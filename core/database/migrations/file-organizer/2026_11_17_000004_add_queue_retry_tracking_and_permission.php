<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::connection('core')->hasTable('queue_job_logs')
            && ! Schema::connection('core')->hasColumn('queue_job_logs', 'failed_job_uuid')) {
            Schema::connection('core')->table('queue_job_logs', function (Blueprint $table): void {
                $table->string('failed_job_uuid')->nullable()->index();
            });
        }

        if (! Schema::connection('core')->hasTable('permissions')) {
            return;
        }

        $db = DB::connection('core');
        $now = now();
        $db->table('permissions')->updateOrInsert(
            ['name' => 'retry_queue_jobs', 'guard_name' => 'web'],
            ['updated_at' => $now, 'created_at' => $now],
        );

        if (Schema::connection('core')->hasTable('roles')
            && Schema::connection('core')->hasTable('role_has_permissions')) {
            $permissionId = $db->table('permissions')
                ->where('name', 'retry_queue_jobs')->where('guard_name', 'web')->value('id');
            $superAdminId = $db->table('roles')
                ->where('name', 'super_admin')->where('guard_name', 'web')->value('id');

            if ($permissionId && $superAdminId) {
                $db->table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $superAdminId,
                ]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Preserve retry history and permission assignments on rollback.
    }
};
