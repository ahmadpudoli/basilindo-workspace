<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('hris')->hasTable('leave_requests')) return;
        Schema::connection('hris')->table('leave_requests', function (Blueprint $table): void {
            $table->unsignedBigInteger('revision_requested_by')->nullable();
            $table->string('revision_requested_role')->nullable();
            $table->string('revision_type')->nullable();
            $table->text('revision_reason')->nullable();
            $table->timestamp('revision_requested_at')->nullable();
            $table->timestamp('revision_submitted_at')->nullable();
            $table->unsignedInteger('revision_number')->default(0);
            $table->index(['status', 'revision_type']);
        });
    }

    public function down(): void
    {
        if (! Schema::connection('hris')->hasTable('leave_requests')) return;
        Schema::connection('hris')->table('leave_requests', function (Blueprint $table): void {
            $table->dropIndex(['status', 'revision_type']);
            $table->dropColumn(['revision_requested_by', 'revision_requested_role', 'revision_type', 'revision_reason', 'revision_requested_at', 'revision_submitted_at', 'revision_number']);
        });
    }
};
