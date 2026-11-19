<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->table('claims', function (Blueprint $table): void {
            $table->string('petty_cash_source', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection('hris')->table('claims', function (Blueprint $table): void {
            $table->dropColumn('petty_cash_source');
        });
    }
};
