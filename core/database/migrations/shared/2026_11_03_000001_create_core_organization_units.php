<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::connection('core')->hasTable('organization_units')) return;
        Schema::connection('core')->create('organization_units', function (Blueprint $table): void {
            $table->string('id', 26)->primary();
            $table->unsignedBigInteger('company_id');
            $table->string('parent_id', 26)->nullable();
            $table->string('code', 50);
            $table->string('name', 150);
            $table->string('type', 30)->default('division');
            $table->string('status', 20)->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'status', 'sort_order']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        // Forward-only: data organisasi dipertahankan saat rollback aplikasi.
    }
};
