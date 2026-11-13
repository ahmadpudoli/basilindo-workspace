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

        if (! Schema::connection('hris')->hasColumn('claims', 'submitted_at')) {
            Schema::connection('hris')->table('claims', function (Blueprint $table): void {
                $table->timestamp('submitted_at')->nullable();
            });
        }

        DB::connection('hris')->table('claims')
            ->whereNull('submitted_at')
            ->where('status', '!=', 'draft')
            ->update(['submitted_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        // Retain submission timestamps if this migration is rolled back.
    }
};
