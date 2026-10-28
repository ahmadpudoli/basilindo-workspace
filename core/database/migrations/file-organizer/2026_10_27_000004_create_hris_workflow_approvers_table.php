<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->create('workflow_approvers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('workflow_key', 80);
            $table->string('step', 30);
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'workflow_key', 'step', 'user_id']);
            $table->index(['company_id', 'workflow_key', 'step', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::connection('hris')->dropIfExists('workflow_approvers');
    }
};
