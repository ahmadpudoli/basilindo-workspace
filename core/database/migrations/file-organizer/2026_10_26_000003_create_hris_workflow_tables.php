<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->create('overtime_requests', function (Blueprint $table): void {
            $table->string('id', 26)->primary();
            $table->string('employee_id', 26);
            $table->unsignedBigInteger('company_id');
            $table->date('work_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('duration_minutes');
            $table->string('status')->default('submitted')->index();
            $table->text('reason');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('manager_approved_by')->nullable();
            $table->timestamp('manager_approved_at')->nullable();
            $table->unsignedBigInteger('hr_approved_by')->nullable();
            $table->timestamp('hr_approved_at')->nullable();
            $table->text('decision_reason')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'work_date']);
            $table->index(['company_id', 'status']);
        });

        Schema::connection('hris')->create('claims', function (Blueprint $table): void {
            $table->string('id', 26)->primary();
            $table->string('employee_id', 26);
            $table->unsignedBigInteger('company_id');
            $table->string('type')->index();
            $table->string('status')->default('submitted')->index();
            $table->string('category');
            $table->text('purpose');
            $table->decimal('amount', 20, 2);
            $table->char('currency', 3)->default('IDR');
            $table->string('supporting_document_id')->nullable();
            $table->string('accountability_document_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('manager_approved_by')->nullable();
            $table->timestamp('manager_approved_at')->nullable();
            $table->unsignedBigInteger('finance_approved_by')->nullable();
            $table->timestamp('finance_approved_at')->nullable();
            $table->unsignedBigInteger('final_approved_by')->nullable();
            $table->timestamp('final_approved_at')->nullable();
            $table->text('decision_reason')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'type', 'created_at']);
            $table->index(['company_id', 'status']);
        });

        Schema::connection('hris')->create('approval_events', function (Blueprint $table): void {
            $table->id();
            $table->string('request_type');
            $table->string('request_id', 26);
            $table->string('step');
            $table->string('action');
            $table->unsignedBigInteger('actor_id');
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['request_type', 'request_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('hris')->dropIfExists('approval_events');
        Schema::connection('hris')->dropIfExists('claims');
        Schema::connection('hris')->dropIfExists('overtime_requests');
    }
};
