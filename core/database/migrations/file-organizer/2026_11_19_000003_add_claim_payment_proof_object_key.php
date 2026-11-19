<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->table('claims', function (Blueprint $table): void {
            $table->string('payment_proof_object_key')->nullable();
        });
    }

    public function down(): void
    {
        // Keep uploaded proof references intact during rollback.
    }
};
