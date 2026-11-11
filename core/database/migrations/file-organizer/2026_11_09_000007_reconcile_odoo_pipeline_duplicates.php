<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::connection('crm')->statement(<<<'SQL'
            UPDATE crm_opportunities AS items
            SET deleted_at = COALESCE(deleted_at, NOW()), updated_at = NOW()
            WHERE items.external_source = 'odoo'
              AND items.deleted_at IS NULL
              AND NOT EXISTS (
                  SELECT 1
                  FROM core_external_references AS refs
                  WHERE refs.entity_type = 'crm_lead'
                    AND refs.local_entity_type = 'crm_pipeline_item'
                    AND refs.local_id = items.id::text
              )
        SQL);

        DB::connection('crm')->statement('CREATE UNIQUE INDEX IF NOT EXISTS crm_opportunities_odoo_external_unique ON crm_opportunities (external_source, external_id) WHERE external_source = \'odoo\' AND external_id IS NOT NULL AND deleted_at IS NULL');
    }

    public function down(): void
    {
        DB::connection('crm')->statement('DROP INDEX IF EXISTS crm_opportunities_odoo_external_unique');
    }
};
