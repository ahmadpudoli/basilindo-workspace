<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('core');

        $schema->create('integration_connections', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code', 80)->unique();
            $table->string('name');
            $table->string('driver', 40);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_tested_at')->nullable();
            $table->string('last_test_status', 30)->nullable();
            $table->text('last_test_message')->nullable();
            $table->jsonb('settings')->nullable();
            $table->timestamps();
        });

        $schema->create('external_references', function (Blueprint $table): void {
            $table->id();
            $table->string('source_system', 80);
            $table->string('source_database', 120)->nullable();
            $table->string('entity_type', 80);
            $table->string('external_id', 120);
            $table->string('local_entity_type', 120)->nullable();
            $table->string('local_id', 120)->nullable();
            $table->string('external_code')->nullable();
            $table->timestamp('source_updated_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->string('sync_status', 30)->default('synced')->index();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->unique(['source_system', 'source_database', 'entity_type', 'external_id'], 'core_external_refs_unique');
            $table->index(['local_entity_type', 'local_id']);
        });

        $schema->create('integration_reference_records', function (Blueprint $table): void {
            $table->id();
            $table->string('source_system', 80);
            $table->string('source_database', 120)->nullable();
            $table->string('entity_type', 80);
            $table->string('external_id', 120);
            $table->string('display_name')->nullable();
            $table->string('source_updated_at_value')->nullable();
            $table->jsonb('payload');
            $table->timestamp('last_synced_at');
            $table->timestamps();
            $table->unique(['source_system', 'source_database', 'entity_type', 'external_id'], 'core_integration_records_unique');
            $table->index(['entity_type', 'display_name']);
        });

        $schema->create('integration_sync_runs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('source_system', 80);
            $table->string('sync_type', 40);
            $table->string('status', 30)->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('processed_count')->default(0);
            $table->unsignedInteger('created_count')->default(0);
            $table->unsignedInteger('updated_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->text('message')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->index(['source_system', 'created_at']);
        });

        $schema->create('integration_sync_errors', function (Blueprint $table): void {
            $table->id();
            $table->uuid('sync_run_id');
            $table->string('entity_type', 80)->nullable();
            $table->string('external_id', 120)->nullable();
            $table->text('message');
            $table->jsonb('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['sync_run_id', 'created_at']);
        });
    }

    public function down(): void
    {
        $schema = Schema::connection('core');
        $schema->dropIfExists('integration_sync_errors');
        $schema->dropIfExists('integration_sync_runs');
        $schema->dropIfExists('integration_reference_records');
        $schema->dropIfExists('external_references');
        $schema->dropIfExists('integration_connections');
    }
};
