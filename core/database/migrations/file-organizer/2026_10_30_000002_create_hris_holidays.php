<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->create('holidays', function (Blueprint $table): void {
            $table->string('id', 26)->primary();
            $table->date('holiday_date');
            $table->string('name');
            $table->string('type')->default('national');
            $table->text('description')->nullable();
            $table->boolean('deduct_annual_leave')->default(false);
            $table->boolean('is_global')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['holiday_date', 'type', 'is_active']);
            $table->index(['is_global', 'is_active']);
        });

        Schema::connection('hris')->create('holiday_companies', function (Blueprint $table): void {
            $table->string('holiday_id', 26);
            $table->unsignedBigInteger('company_id');
            $table->primary(['holiday_id', 'company_id']);
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::connection('hris')->dropIfExists('holiday_companies');
        Schema::connection('hris')->dropIfExists('holidays');
    }
};
