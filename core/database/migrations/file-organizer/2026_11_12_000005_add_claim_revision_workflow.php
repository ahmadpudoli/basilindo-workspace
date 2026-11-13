<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('hris')->hasTable('claims')) return;

        $columns = [
            'requester_user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('requester_user_id')->nullable()->index(),
            'revision_requested_by' => fn (Blueprint $table) => $table->unsignedBigInteger('revision_requested_by')->nullable(),
            'revision_requested_role' => fn (Blueprint $table) => $table->string('revision_requested_role', 30)->nullable(),
            'revision_type' => fn (Blueprint $table) => $table->string('revision_type', 30)->nullable(),
            'revision_reason' => fn (Blueprint $table) => $table->text('revision_reason')->nullable(),
            'revision_requested_at' => fn (Blueprint $table) => $table->timestamp('revision_requested_at')->nullable(),
            'revision_submitted_at' => fn (Blueprint $table) => $table->timestamp('revision_submitted_at')->nullable(),
            'revision_number' => fn (Blueprint $table) => $table->unsignedInteger('revision_number')->default(0),
        ];

        Schema::connection('hris')->table('claims', function (Blueprint $table) use ($columns): void {
            foreach ($columns as $name => $addColumn) {
                if (! Schema::connection('hris')->hasColumn('claims', $name)) $addColumn($table);
            }
        });

        DB::connection('hris')->table('claims')->whereNull('requester_user_id')->whereNotNull('created_by')->update(['requester_user_id' => DB::raw('created_by')]);
    }

    public function down(): void
    {
        // Keep revision history and requester attribution intact on rollback.
    }
};
