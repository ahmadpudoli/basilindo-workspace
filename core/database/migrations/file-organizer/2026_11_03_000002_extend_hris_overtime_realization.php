<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('hris')->hasColumn('employees', 'organization_unit_id')) {
            Schema::connection('hris')->table('employees', function (Blueprint $table): void { $table->string('organization_unit_id', 26)->nullable()->index(); });
        }
        $columns = [
            'overtime_type' => fn (Blueprint $table) => $table->string('overtime_type', 20)->default('internal'),
            'work_location' => fn (Blueprint $table) => $table->string('work_location', 150)->nullable(),
            'organization_unit_name' => fn (Blueprint $table) => $table->string('organization_unit_name', 150)->nullable(),
            'job_title_snapshot' => fn (Blueprint $table) => $table->string('job_title_snapshot', 150)->nullable(),
            'requester_user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('requester_user_id')->nullable()->index(),
            'revision_requested_by' => fn (Blueprint $table) => $table->unsignedBigInteger('revision_requested_by')->nullable(),
            'revision_requested_role' => fn (Blueprint $table) => $table->string('revision_requested_role', 30)->nullable(),
            'revision_type' => fn (Blueprint $table) => $table->string('revision_type', 30)->nullable(),
            'revision_reason' => fn (Blueprint $table) => $table->text('revision_reason')->nullable(),
            'revision_requested_at' => fn (Blueprint $table) => $table->timestamp('revision_requested_at')->nullable(),
        ];
        foreach ($columns as $column => $definition) {
            if (! Schema::connection('hris')->hasColumn('overtime_requests', $column)) {
                Schema::connection('hris')->table('overtime_requests', fn (Blueprint $table) => $definition($table));
            }
        }
        if (! Schema::connection('hris')->hasTable('overtime_request_entries')) {
            Schema::connection('hris')->create('overtime_request_entries', function (Blueprint $table): void {
                $table->string('id', 26)->primary();
                $table->string('overtime_request_id', 26);
                $table->date('work_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->unsignedInteger('duration_minutes');
                $table->text('work_description');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['overtime_request_id', 'sort_order']);
                $table->index('work_date');
            });
        }
        DB::connection('hris')->table('overtime_requests')->orderBy('id')->chunk(200, function ($requests): void {
            foreach ($requests as $request) {
                if (DB::connection('hris')->table('overtime_request_entries')->where('overtime_request_id', $request->id)->exists()) continue;
                DB::connection('hris')->table('overtime_request_entries')->insert([
                    'id' => (string) Str::ulid(), 'overtime_request_id' => $request->id,
                    'work_date' => $request->work_date, 'start_time' => $request->start_time,
                    'end_time' => $request->end_time, 'duration_minutes' => $request->duration_minutes,
                    'work_description' => $request->reason, 'sort_order' => 0,
                    'created_at' => $request->created_at, 'updated_at' => $request->updated_at,
                ]);
            }
        });
        DB::connection('hris')->table('overtime_requests')->where('status', 'submitted')->update(['status' => 'pending_manager']);
        DB::connection('hris')->table('overtime_requests')->whereNull('requester_user_id')->whereNotNull('created_by')->update(['requester_user_id' => DB::raw('created_by')]);
    }

    public function down(): void
    {
        // Forward-only: detail lembur dan referensi organisasi tidak dihapus saat rollback.
    }
};
