<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::connection('core')->hasTable('company_operational_visibility')) {
            return;
        }

        Schema::connection('core')->create('company_operational_visibility', function (Blueprint $table): void {
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('operational_company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['company_id', 'operational_company_id']);
            $table->index('operational_company_id');
        });
    }

    public function down(): void
    {
        Schema::connection('core')->dropIfExists('company_operational_visibility');
    }
};
