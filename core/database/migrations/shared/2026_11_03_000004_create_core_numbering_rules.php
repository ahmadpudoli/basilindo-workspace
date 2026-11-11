<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::connection('core')->hasTable('numbering_rules')) return;
        Schema::connection('core')->create('numbering_rules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('form_key', 80);
            $table->string('name', 150);
            $table->string('format', 255);
            $table->string('counter_scope', 20)->default('yearly');
            $table->string('period_key', 20)->nullable();
            $table->unsignedInteger('next_number')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'form_key']);
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void {}
};
