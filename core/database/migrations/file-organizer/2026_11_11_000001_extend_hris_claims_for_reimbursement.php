<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->table('claims', function (Blueprint $table): void {
            $table->string('department')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('po_number')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('paid_to')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('bank_account')->nullable();
            $table->decimal('paid_amount', 20, 2)->nullable();
            $table->date('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('payment_proof_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->index(['company_id', 'payment_method']);
            $table->index(['company_id', 'paid_at']);
        });

        Schema::connection('hris')->create('claim_expense_items', function (Blueprint $table): void {
            $table->string('id', 26)->primary();
            $table->string('claim_id', 26);
            $table->unsignedInteger('line_number');
            $table->date('receipt_date')->nullable();
            $table->date('activity_date')->nullable();
            $table->string('category', 100);
            $table->text('description');
            $table->char('currency', 3)->default('IDR');
            $table->decimal('amount', 20, 2);
            $table->string('attachment_reference')->nullable();
            $table->timestamps();
            $table->index(['claim_id', 'line_number']);
            $table->index(['category', 'activity_date']);
        });

        DB::connection('hris')->table('claims')->whereNotExists(function ($query): void {
            $query->select(DB::raw(1))->from('claim_expense_items')->whereColumn('claim_expense_items.claim_id', 'claims.id');
        })->orderBy('id')->chunkById(100, function ($claims): void {
            foreach ($claims as $claim) {
                DB::connection('hris')->table('claim_expense_items')->insert([
                    'id' => (string) Str::ulid(),
                    'claim_id' => $claim->id,
                    'line_number' => 1,
                    'receipt_date' => null,
                    'activity_date' => null,
                    'category' => $claim->category,
                    'description' => $claim->purpose,
                    'currency' => $claim->currency ?: 'IDR',
                    'amount' => $claim->amount,
                    'attachment_reference' => $claim->supporting_document_id,
                    'created_at' => $claim->created_at,
                    'updated_at' => $claim->updated_at,
                ]);
            }
        }, 'id');
    }

    public function down(): void
    {
        Schema::connection('hris')->dropIfExists('claim_expense_items');
        Schema::connection('hris')->table('claims', function (Blueprint $table): void {
            $table->dropIndex(['company_id', 'payment_method']);
            $table->dropIndex(['company_id', 'paid_at']);
            $table->dropColumn([
                'department', 'project_id', 'client_id', 'po_number', 'payment_method',
                'paid_to', 'bank_name', 'bank_account', 'paid_amount', 'paid_at',
                'payment_reference', 'payment_proof_id', 'completed_at', 'paid_by',
            ]);
        });
    }
};
