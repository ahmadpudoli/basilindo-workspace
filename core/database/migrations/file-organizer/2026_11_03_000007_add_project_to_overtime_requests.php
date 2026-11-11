<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('hris')->hasColumn('overtime_requests', 'project_id')) {
            Schema::connection('hris')->table('overtime_requests', function (Blueprint $table): void {
                $table->unsignedBigInteger('project_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // Keep the nullable column and existing references for a data-safe code rollback.
    }
};
