<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->table('claims', function (Blueprint $table): void {
            $table->string('expense_scope')->default('internal')->after('type');
            $table->string('work_reference_type')->default('none')->after('expense_scope');
            $table->string('opportunity_id', 26)->nullable()->after('project_id');
            $table->string('work_location')->nullable()->after('po_number');
            $table->string('reference_name_snapshot')->nullable()->after('work_location');
            $table->string('reference_status_snapshot')->nullable()->after('reference_name_snapshot');
            $table->index(['company_id', 'expense_scope']);
        });
    }

    public function down(): void
    {
        Schema::connection('hris')->table('claims', function (Blueprint $table): void {
            $table->dropIndex(['company_id', 'expense_scope']);
            $table->dropColumn(['expense_scope', 'work_reference_type', 'opportunity_id', 'work_location', 'reference_name_snapshot', 'reference_status_snapshot']);
        });
    }
};
