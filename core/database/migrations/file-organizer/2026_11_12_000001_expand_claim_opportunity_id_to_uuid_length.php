<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->table('claims', function (Blueprint $table): void {
            $table->string('opportunity_id', 36)->nullable()->change();
        });
    }

    public function down(): void
    {
        $hasLongUuid = DB::connection('hris')->table('claims')
            ->whereNotNull('opportunity_id')
            ->whereRaw('char_length(opportunity_id) > 26')
            ->exists();

        if ($hasLongUuid) {
            return;
        }

        Schema::connection('hris')->table('claims', function (Blueprint $table): void {
            $table->string('opportunity_id', 26)->nullable()->change();
        });
    }
};
