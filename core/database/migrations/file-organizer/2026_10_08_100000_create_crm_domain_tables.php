<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_accounts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('company_id');
            $table->string('name');
            $table->string('status')->default('prospect')->index();
            $table->string('industry')->nullable();
            $table->string('segment')->nullable();
            $table->unsignedBigInteger('owner_user_id')->nullable();
            $table->decimal('estimated_value', 20, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'name']);
            $table->index(['company_id', 'status']);
        });

        Schema::create('crm_contacts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('account_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('job_title')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('account_id')->references('id')->on('crm_accounts')->cascadeOnDelete();
            $table->index(['account_id', 'is_primary']);
        });

        Schema::create('crm_leads', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->uuid('account_id')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('new')->index();
            $table->unsignedBigInteger('owner_user_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('account_id')->references('id')->on('crm_accounts')->nullOnDelete();
            $table->index(['company_id', 'status']);
        });

        Schema::create('crm_opportunities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('account_id');
            $table->string('name');
            $table->string('stage')->default('qualification')->index();
            $table->decimal('amount', 20, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->date('expected_close_date')->nullable();
            $table->unsignedBigInteger('owner_user_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('account_id')->references('id')->on('crm_accounts')->cascadeOnDelete();
            $table->index(['account_id', 'stage']);
            $table->index(['project_id', 'stage']);
        });

        Schema::create('crm_activities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->uuid('account_id')->nullable();
            $table->uuid('opportunity_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('type')->default('note');
            $table->string('subject');
            $table->text('description')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('crm_accounts')->nullOnDelete();
            $table->foreign('opportunity_id')->references('id')->on('crm_opportunities')->nullOnDelete();
            $table->index(['company_id', 'due_at']);
            $table->index(['assigned_to', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('crm_opportunities');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('crm_contacts');
        Schema::dropIfExists('crm_accounts');
    }
};
