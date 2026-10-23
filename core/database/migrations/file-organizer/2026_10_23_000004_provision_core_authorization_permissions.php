<?php

use Core\Models\Permission;
use Core\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permissionNames = [
            'view_role', 'view_any_role', 'create_role', 'update_role', 'delete_role',
            'view_audit_trail',
        ];

        foreach ($permissionNames as $permissionName) {
            Permission::query()->firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $superAdmin = Role::query()->where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(Permission::query()->whereIn('name', $permissionNames)->get());
        }
    }

    public function down(): void
    {
        // Permission cleanup is intentionally not automatic to preserve existing authorization history.
    }
};
