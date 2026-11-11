<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('hris')->hasColumn('overtime_request_entries', 'day_type')) {
            Schema::connection('hris')->table('overtime_request_entries', function (Blueprint $table): void {
                $table->string('day_type', 20)->default('workday')->after('work_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::connection('hris')->hasColumn('overtime_request_entries', 'day_type')) {
            Schema::connection('hris')->table('overtime_request_entries', function (Blueprint $table): void {
                $table->dropColumn('day_type');
            });
        }
    }
};
