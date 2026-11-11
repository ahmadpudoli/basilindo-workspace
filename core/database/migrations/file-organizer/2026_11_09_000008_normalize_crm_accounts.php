<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('crm');

        if (! $schema->hasColumn('accounts', 'account_type')) {
            $schema->table('accounts', fn ($table) => $table->string('account_type', 20)->nullable());
        }

        if (! $schema->hasColumn('accounts', 'party_company_id')) {
            $schema->table('accounts', fn ($table) => $table->unsignedBigInteger('party_company_id')->nullable());
        }

        DB::connection('crm')->statement('ALTER TABLE crm_accounts ALTER COLUMN company_id DROP NOT NULL');
        DB::connection('crm')->statement('ALTER TABLE crm_accounts ALTER COLUMN status DROP NOT NULL');
        DB::connection('crm')->statement('ALTER TABLE crm_accounts ALTER COLUMN status DROP DEFAULT');
        DB::connection('crm')->statement('CREATE INDEX IF NOT EXISTS crm_accounts_party_company_idx ON crm_accounts (party_company_id)');
        DB::connection('crm')->statement("CREATE UNIQUE INDEX IF NOT EXISTS crm_accounts_party_company_unique ON crm_accounts (party_company_id) WHERE party_company_id IS NOT NULL AND deleted_at IS NULL");

        DB::connection('crm')->table('accounts')
            ->where('metadata->source', 'odoo')
            ->where('status', 'prospect')
            ->update(['status' => null]);
    }

    public function down(): void
    {
        // Keep additive integration columns and normalized data on rollback.
    }
};
