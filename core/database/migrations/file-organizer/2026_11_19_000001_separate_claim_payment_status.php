<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('hris')->hasTable('claims')) return;

        if (! Schema::connection('hris')->hasColumn('claims', 'payment_status')) {
            Schema::connection('hris')->table('claims', function (Blueprint $table): void {
                $table->string('payment_status', 30)->nullable()->index();
            });
        }

        DB::connection('hris')->table('claims')
            ->where('type', 'reimbursement')->where('status', 'pending_payment')
            ->update(['status' => 'approved', 'payment_status' => 'pending_payment']);
        DB::connection('hris')->table('claims')
            ->where('type', 'reimbursement')->whereIn('status', ['paid', 'completed'])
            ->update(['status' => 'approved', 'payment_status' => 'paid']);
        DB::connection('hris')->table('claims')
            ->where('type', 'reimbursement')->where('status', 'approved')->whereNull('payment_status')
            ->update(['payment_status' => 'pending_payment']);
    }

    public function down(): void
    {
        // Keep separated payment state to avoid losing payment history on rollback.
    }
};
