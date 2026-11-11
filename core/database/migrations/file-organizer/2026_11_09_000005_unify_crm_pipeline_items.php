<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('crm');

        $schema->table('opportunities', function (Blueprint $table) use ($schema): void {
            if (! $schema->hasColumn('opportunities', 'record_type')) $table->string('record_type', 30)->default('opportunity');
            if (! $schema->hasColumn('opportunities', 'company_id')) $table->unsignedBigInteger('company_id')->nullable();
            if (! $schema->hasColumn('opportunities', 'email')) $table->string('email')->nullable();
            if (! $schema->hasColumn('opportunities', 'phone')) $table->string('phone')->nullable();
            if (! $schema->hasColumn('opportunities', 'source')) $table->string('source')->nullable();
            if (! $schema->hasColumn('opportunities', 'status')) $table->string('status', 50)->default('open');
        });

        DB::connection('crm')->statement('ALTER TABLE crm_opportunities DROP CONSTRAINT IF EXISTS "crm_opportunities_account_id_foreign"');
        DB::connection('crm')->statement('ALTER TABLE crm_opportunities DROP CONSTRAINT IF EXISTS "ws_crm_opportunities_account_id_foreign"');

        DB::connection('crm')->statement('ALTER TABLE crm_opportunities ALTER COLUMN account_id DROP NOT NULL');
        DB::connection('crm')->statement('ALTER TABLE crm_opportunities ADD CONSTRAINT crm_opportunities_account_id_unified_foreign FOREIGN KEY (account_id) REFERENCES crm_accounts (id) ON DELETE SET NULL');
        DB::connection('crm')->statement('CREATE INDEX IF NOT EXISTS crm_opportunities_company_type_stage_idx ON crm_opportunities (company_id, record_type, stage_id)');
        DB::connection('crm')->statement('CREATE INDEX IF NOT EXISTS crm_opportunities_record_type_idx ON crm_opportunities (record_type)');
        DB::connection('crm')->statement('CREATE INDEX IF NOT EXISTS crm_opportunities_status_idx ON crm_opportunities (status)');
    }

    public function down(): void
    {
        // Keep the columns during rollback when records may already have been
        // normalized from Odoo. Removing them could destroy integration data.
    }
};
