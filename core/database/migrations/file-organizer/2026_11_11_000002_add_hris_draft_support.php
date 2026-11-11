<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['leave_requests', 'overtime_requests', 'claims'] as $tableName) {
            if (! Schema::connection('hris')->hasColumn($tableName, 'deleted_at')) {
                Schema::connection('hris')->table($tableName, function (Blueprint $table): void {
                    $table->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['leave_requests', 'overtime_requests', 'claims'] as $tableName) {
            if (Schema::connection('hris')->hasColumn($tableName, 'deleted_at')) {
                Schema::connection('hris')->table($tableName, function (Blueprint $table): void {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
