<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $core = Schema::connection('core');
        if (! $core->hasTable('audit_logs')) {
            $core->create('audit_logs', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->string('action');
                $table->string('module', 80)->nullable();
                $table->string('subject_type')->nullable();
                $table->string('subject_id')->nullable();
                $table->string('request_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device', 120)->nullable();
                $table->string('http_method', 10)->nullable();
                $table->string('route_name', 180)->nullable();
                $table->jsonb('metadata')->nullable();
                $table->jsonb('old_values')->nullable();
                $table->jsonb('new_values')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index(['company_id', 'action', 'created_at']);
                $table->index(['module', 'created_at']);
                $table->index(['actor_id', 'created_at']);
                $table->index(['ip_address', 'created_at']);
            });
        }

        $legacy = Schema::connection('documents');
        if (! $legacy->hasTable('audit_events')) {
            return;
        }

        $coreTable = DB::connection('core')->getTablePrefix().'audit_logs';
        $legacyTable = DB::connection('documents')->getTablePrefix().'audit_events';
        DB::connection('pgsql')->statement("INSERT INTO \"{$coreTable}\" (id, company_id, actor_id, action, module, subject_type, subject_id, request_id, ip_address, user_agent, device, http_method, route_name, metadata, old_values, new_values, created_at) SELECT id, company_id, actor_id, action, module, subject_type, subject_id, request_id, ip_address, user_agent, device, http_method, route_name, metadata, old_values, new_values, created_at FROM \"{$legacyTable}\" source WHERE NOT EXISTS (SELECT 1 FROM \"{$coreTable}\" target WHERE target.id = source.id)");
        DB::connection('pgsql')->statement("SELECT setval(pg_get_serial_sequence('{$coreTable}', 'id'), COALESCE((SELECT MAX(id) FROM \"{$coreTable}\"), 1), (SELECT COUNT(*) > 0 FROM \"{$coreTable}\"))");
    }

    public function down(): void
    {
        // Audit history is retained on rollback; the legacy source remains intact.
    }
};
