<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->table('workflow_approvers', function (Blueprint $table): void { $table->softDeletes(); });
        $tableName = 'hris_workflow_approvers';
        DB::statement('ALTER TABLE "'.$tableName.'" DROP CONSTRAINT IF EXISTS "hris_workflow_approvers_company_id_workflow_key_step_user_id_un"');
        Schema::connection('hris')->table('workflow_approvers', function (Blueprint $table): void { $table->index(['company_id', 'workflow_key', 'step', 'user_id'], 'hris_workflow_approvers_lookup_idx'); });
        DB::statement('WITH ranked AS (SELECT id, ROW_NUMBER() OVER (PARTITION BY company_id, workflow_key, step ORDER BY created_at DESC, id DESC) AS position FROM "'.$tableName.'" WHERE deleted_at IS NULL) UPDATE "'.$tableName.'" SET deleted_at = NOW() FROM ranked WHERE "'.$tableName.'".id = ranked.id AND ranked.position > 1');
        DB::statement('CREATE UNIQUE INDEX hris_workflow_approvers_active_unique ON "'.$tableName.'" (company_id, workflow_key, step) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS hris_workflow_approvers_active_unique');
        Schema::connection('hris')->table('workflow_approvers', function (Blueprint $table): void {
            $table->dropIndex('hris_workflow_approvers_lookup_idx');
            $table->unique(['company_id', 'workflow_key', 'step', 'user_id']);
            $table->dropSoftDeletes();
        });
    }
};
