<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::connection('crm')->statement('ALTER TABLE crm_opportunities ALTER COLUMN status DROP NOT NULL');
        DB::connection('crm')->statement('ALTER TABLE crm_opportunities ALTER COLUMN status DROP DEFAULT');
    }

    public function down(): void
    {
        // Do not restore NOT NULL because existing Odoo records may legitimately
        // have no closing status yet.
    }
};
