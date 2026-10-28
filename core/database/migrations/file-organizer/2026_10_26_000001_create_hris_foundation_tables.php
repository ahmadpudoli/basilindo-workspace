<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->create('employees', function (Blueprint $table): void {
            $table->string('id', 26)->primary();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('employee_number');
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('job_title')->nullable();
            $table->string('manager_id', 26)->nullable();
            $table->date('hire_date')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'employee_number']);
            $table->index(['company_id', 'status']);
            $table->index(['manager_id', 'status']);
        });

        Schema::connection('hris')->create('settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('key');
            $table->jsonb('value');
            $table->date('effective_from')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['company_id', 'key', 'is_active']);
        });

        Schema::connection('hris')->create('leave_requests', function (Blueprint $table): void {
            $table->string('id', 26)->primary();
            $table->string('employee_id', 26);
            $table->unsignedBigInteger('company_id');
            $table->string('type')->index();
            $table->string('status')->default('draft')->index();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('total_days');
            $table->text('reason')->nullable();
            $table->string('supporting_document_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('manager_approved_by')->nullable();
            $table->timestamp('manager_approved_at')->nullable();
            $table->unsignedBigInteger('hr_approved_by')->nullable();
            $table->timestamp('hr_approved_at')->nullable();
            $table->text('decision_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'start_date', 'end_date']);
            $table->index(['company_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::connection('hris')->dropIfExists('leave_requests');
        Schema::connection('hris')->dropIfExists('settings');
        Schema::connection('hris')->dropIfExists('employees');
    }
};
