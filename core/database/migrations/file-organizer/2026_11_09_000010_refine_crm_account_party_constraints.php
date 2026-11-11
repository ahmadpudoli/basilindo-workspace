<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $crm = DB::connection('crm');

        $crm->statement('DROP INDEX IF EXISTS crm_accounts_party_company_unique');
        $crm->statement("CREATE UNIQUE INDEX IF NOT EXISTS crm_accounts_company_party_unique ON crm_accounts (party_company_id) WHERE party_company_id IS NOT NULL AND account_type = 'company' AND deleted_at IS NULL");
        $crm->statement('ALTER TABLE crm_opportunities ALTER COLUMN status DROP NOT NULL');
        $crm->statement('ALTER TABLE crm_opportunities ALTER COLUMN status DROP DEFAULT');
    }

    public function down(): void
    {
        $crm = DB::connection('crm');
        $crm->statement('DROP INDEX IF EXISTS crm_accounts_company_party_unique');
        // The previous broad uniqueness constraint is intentionally not restored:
        // multiple person accounts may belong to the same client/partner company.
    }
};
