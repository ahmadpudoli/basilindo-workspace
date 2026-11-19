<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'hris';

    public function up(): void
    {
        Schema::connection('hris')->create('employee_payment_accounts', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->char('employee_id', 26)->index();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('account_type', 24)->index();
            $table->string('bank_name', 120);
            $table->text('account_holder');
            $table->text('account_number');
            $table->boolean('is_current')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('replaced_at')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'account_type', 'is_current'], 'employee_payment_accounts_current_idx');
        });
    }

    public function down(): void
    {
        Schema::connection('hris')->dropIfExists('employee_payment_accounts');
    }
};
