<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('core');
        $tables = config('permission.table_names');
        $columns = config('permission.column_names');
        $modelKey = $columns['model_morph_key'] ?? 'model_id';
        $roleKey = $columns['role_pivot_key'] ?? 'role_id';
        $permissionKey = $columns['permission_pivot_key'] ?? 'permission_id';

        if (! $schema->hasTable($tables['permissions'])) {
            $schema->create($tables['permissions'], function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            });
        }

        if (! $schema->hasTable($tables['roles'])) {
            $schema->create($tables['roles'], function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            });
        }

        if (! $schema->hasTable($tables['model_has_permissions'])) {
            $schema->create($tables['model_has_permissions'], function (Blueprint $table) use ($permissionKey, $modelKey): void {
                $table->unsignedBigInteger($permissionKey);
                $table->string('model_type');
                $table->unsignedBigInteger($modelKey);
                $table->index([$modelKey, 'model_type']);
                $table->foreign($permissionKey)->references('id')->on('permissions')->onDelete('cascade');
                $table->primary([$permissionKey, $modelKey, 'model_type']);
            });
        }

        if (! $schema->hasTable($tables['model_has_roles'])) {
            $schema->create($tables['model_has_roles'], function (Blueprint $table) use ($roleKey, $modelKey): void {
                $table->unsignedBigInteger($roleKey);
                $table->string('model_type');
                $table->unsignedBigInteger($modelKey);
                $table->index([$modelKey, 'model_type']);
                $table->foreign($roleKey)->references('id')->on('roles')->onDelete('cascade');
                $table->primary([$roleKey, $modelKey, 'model_type']);
            });
        }

        if (! $schema->hasTable($tables['role_has_permissions'])) {
            $schema->create($tables['role_has_permissions'], function (Blueprint $table) use ($roleKey, $permissionKey): void {
                $table->unsignedBigInteger($permissionKey);
                $table->unsignedBigInteger($roleKey);
                $table->foreign($permissionKey)->references('id')->on('permissions')->onDelete('cascade');
                $table->foreign($roleKey)->references('id')->on('roles')->onDelete('cascade');
                $table->primary([$permissionKey, $roleKey]);
            });
        }
    }

    public function down(): void
    {
        // Permission tables are shared authorization data; never remove them automatically.
    }
};
