<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::connection('hris')->hasTable('claim_expense_items') && ! Schema::connection('hris')->hasColumn('claim_expense_items', 'is_active')) {
            Schema::connection('hris')->table('claim_expense_items', function (Blueprint $table): void {
                $table->boolean('is_active')->default(true)->index();
            });
        }

        if (! Schema::connection('hris')->hasTable('claim_revision_snapshots')) {
            Schema::connection('hris')->create('claim_revision_snapshots', function (Blueprint $table): void {
                $table->id();
                $table->string('claim_id', 26)->index();
                $table->unsignedInteger('revision_number');
                $table->unsignedBigInteger('actor_id');
                $table->string('requested_role', 30);
                $table->string('revision_type', 30);
                $table->text('revision_reason');
                $table->jsonb('claim_snapshot');
                $table->jsonb('expense_items_snapshot');
                $table->timestamp('created_at')->useCurrent();
                $table->unique(['claim_id', 'revision_number']);
            });
        }
    }

    public function down(): void
    {
        // Preserve revision snapshots and item activity state across rollback.
    }
};
