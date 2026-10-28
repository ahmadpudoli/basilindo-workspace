<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $table = 'hris_workflow_approvers';
        $quotedTable = '"'.str_replace('"', '""', $table).'"';

        // The original unique constraint was created with the connection table
        // prefix. Remove both historical names so archived rows no longer block
        // reactivation of the same workflow configuration.
        foreach ([
            'hris_workflow_approvers_company_id_workflow_key_step_user_id_un',
            'hris_workflow_approvers_company_id_workflow_key_step_user_id_unique',
            'ws_hris_workflow_approvers_company_id_workflow_key_step_user_id',
            'ws_hris_workflow_approvers_company_id_workflow_key_step_user_id_unique',
        ] as $constraint) {
            DB::statement('ALTER TABLE '.$quotedTable.' DROP CONSTRAINT IF EXISTS "'.$constraint.'"');
        }

        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS hris_workflow_approvers_active_unique ON '.$quotedTable.' (company_id, workflow_key, step) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        $table = 'hris_workflow_approvers';
        $quotedTable = '"'.str_replace('"', '""', $table).'"';
        DB::statement('DROP INDEX IF EXISTS hris_workflow_approvers_active_unique');
        DB::statement('ALTER TABLE '.$quotedTable.' ADD CONSTRAINT "'.$table.'_company_id_workflow_key_step_user_id" UNIQUE (company_id, workflow_key, step, user_id)');
    }
};
