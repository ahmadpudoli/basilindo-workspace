<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'work_reference_type' => fn (Blueprint $table) => $table->string('work_reference_type', 20)->default('none')->index(),
            'opportunity_id' => fn (Blueprint $table) => $table->uuid('opportunity_id')->nullable()->index(),
            'reference_name_snapshot' => fn (Blueprint $table) => $table->string('reference_name_snapshot', 255)->nullable(),
            'reference_status_snapshot' => fn (Blueprint $table) => $table->string('reference_status_snapshot', 100)->nullable(),
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::connection('hris')->hasColumn('overtime_requests', $column)) {
                Schema::connection('hris')->table('overtime_requests', fn (Blueprint $table) => $definition($table));
            }
        }

        DB::connection('hris')->table('overtime_requests')
            ->whereNotNull('project_id')
            ->where('work_reference_type', 'none')
            ->update([
                'work_reference_type' => 'project',
                'reference_status_snapshot' => 'Project',
            ]);
    }

    public function down(): void
    {
        // Forward-only: reference metadata is part of the audit history of submitted overtime.
    }
};
