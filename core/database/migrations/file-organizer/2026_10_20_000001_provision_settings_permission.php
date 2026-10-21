<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('core')->hasTable('permissions')) {
            return;
        }

        DB::connection('core')->table('permissions')->insertOrIgnore([
            'name' => 'access_settings',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! Schema::connection('core')->hasTable('permissions')) {
            return;
        }

        $permissionId = DB::connection('core')->table('permissions')->where('name', 'access_settings')->where('guard_name', 'web')->value('id');
        if ($permissionId) {
            DB::connection('core')->table('role_has_permissions')->where('permission_id', $permissionId)->delete();
            DB::connection('core')->table('model_has_permissions')->where('permission_id', $permissionId)->delete();
            DB::connection('core')->table('permissions')->where('id', $permissionId)->delete();
        }
    }
};
