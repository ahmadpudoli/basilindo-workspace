<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addColumns('crm', 'accounts');
        $this->addColumns('crm', 'contacts');
        $this->addColumns('crm', 'opportunities');
        $this->addColumns('core', 'projects');
        $this->addColumns('core', 'companies');

        DB::connection('crm')->statement("UPDATE crm_accounts SET source_system = 'odoo', external_id = metadata->>'external_id', is_integrated = true, last_synced_at = COALESCE(updated_at, NOW()) WHERE metadata->>'source' = 'odoo'");
        DB::connection('crm')->statement("UPDATE crm_opportunities SET source_system = external_source, is_integrated = (external_source IS NOT NULL), last_synced_at = COALESCE(updated_at, NOW()) WHERE external_source IS NOT NULL");

        $this->backfillReferences('crm', 'contacts', 'crm_contact');
        $this->backfillReferences('core', 'projects', 'project');
        $this->backfillReferences('core', 'companies', 'company');
    }

    private function addColumns(string $connection, string $table): void
    {
        $schema = Schema::connection($connection);
        $schema->table($table, function (Blueprint $blueprint) use ($schema): void {
            if (! $schema->hasColumn($blueprint->getTable(), 'source_system')) {
                $blueprint->string('source_system')->nullable();
            }
            if (! $schema->hasColumn($blueprint->getTable(), 'source_database')) {
                $blueprint->string('source_database')->nullable();
            }
            if (! $schema->hasColumn($blueprint->getTable(), 'external_id')) {
                $blueprint->string('external_id')->nullable();
            }
            if (! $schema->hasColumn($blueprint->getTable(), 'is_integrated')) {
                $blueprint->boolean('is_integrated')->default(false);
            }
            if (! $schema->hasColumn($blueprint->getTable(), 'last_synced_at')) {
                $blueprint->timestamp('last_synced_at')->nullable();
            }
        });

        $prefixedTable = config("database.connections.{$connection}.prefix").$table;
        DB::connection($connection)->statement("CREATE INDEX IF NOT EXISTS {$prefixedTable}_integration_source_idx ON {$prefixedTable} (source_system, source_database, external_id)");
    }

    private function backfillReferences(string $connection, string $table, string $localEntityType): void
    {
        $references = DB::connection('core')->table('external_references')
            ->where('source_system', 'odoo')
            ->where('local_entity_type', $localEntityType)
            ->whereNotNull('local_id')
            ->get(['source_database', 'external_id', 'local_id']);

        foreach ($references as $reference) {
            DB::connection($connection)->table($table)->where('id', $reference->local_id)->update([
                'source_system' => 'odoo',
                'source_database' => $reference->source_database,
                'external_id' => $reference->external_id,
                'is_integrated' => true,
                'last_synced_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        foreach ([['crm', 'accounts'], ['crm', 'contacts'], ['crm', 'opportunities'], ['core', 'projects'], ['core', 'companies']] as [$connection, $table]) {
            $prefixedTable = config("database.connections.{$connection}.prefix").$table;
            DB::connection($connection)->statement("DROP INDEX IF EXISTS {$prefixedTable}_integration_source_idx");
            Schema::connection($connection)->table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn(['source_system', 'source_database', 'external_id', 'is_integrated', 'last_synced_at']);
            });
        }
    }
};
