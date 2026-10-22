<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $schema = Schema::connection('documents');

        if (! $schema->hasTable('audit_events')) {
            return;
        }

        $schema->table('audit_events', function (Blueprint $table) use ($schema): void {
            if (! $schema->hasColumn('audit_events', 'module')) {
                $table->string('module', 80)->nullable()->after('action');
            }
            if (! $schema->hasColumn('audit_events', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('request_id');
            }
            if (! $schema->hasColumn('audit_events', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (! $schema->hasColumn('audit_events', 'device')) {
                $table->string('device', 120)->nullable()->after('user_agent');
            }
            if (! $schema->hasColumn('audit_events', 'http_method')) {
                $table->string('http_method', 10)->nullable()->after('device');
            }
            if (! $schema->hasColumn('audit_events', 'route_name')) {
                $table->string('route_name', 180)->nullable()->after('http_method');
            }
            if (! $schema->hasColumn('audit_events', 'old_values')) {
                $table->jsonb('old_values')->nullable()->after('metadata');
            }
            if (! $schema->hasColumn('audit_events', 'new_values')) {
                $table->jsonb('new_values')->nullable()->after('old_values');
            }
        });

        $schema->table('audit_events', function (Blueprint $table): void {
            $table->index(['module', 'created_at']);
            $table->index(['actor_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
        });
    }

    public function down(): void
    {
        $schema = Schema::connection('documents');

        if (! $schema->hasTable('audit_events')) {
            return;
        }

        $schema->table('audit_events', function (Blueprint $table) use ($schema): void {
            foreach (['module', 'ip_address', 'user_agent', 'device', 'http_method', 'route_name', 'old_values', 'new_values'] as $column) {
                if ($schema->hasColumn('audit_events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
