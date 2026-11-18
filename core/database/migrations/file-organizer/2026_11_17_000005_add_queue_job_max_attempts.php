<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::connection('core')->hasTable('queue_job_logs')
            && ! Schema::connection('core')->hasColumn('queue_job_logs', 'max_attempts')) {
            Schema::connection('core')->table('queue_job_logs', function (Blueprint $table): void {
                $table->unsignedSmallInteger('max_attempts')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Keep retry eligibility history intact during rollback.
    }
};
