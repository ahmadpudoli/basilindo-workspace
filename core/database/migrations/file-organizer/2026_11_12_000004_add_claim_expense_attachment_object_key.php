<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('hris')->hasColumn('claim_expense_items', 'attachment_object_key')) {
            Schema::connection('hris')->table('claim_expense_items', function (Blueprint $table): void {
                $table->string('attachment_object_key')->nullable();
            });
        }

        $connection = DB::connection('hris');
        $connection->table('claim_expense_items')
            ->join('claims', 'claims.id', '=', 'claim_expense_items.claim_id')
            ->whereNull('claim_expense_items.attachment_object_key')
            ->whereNotNull('claims.supporting_document_id')
            ->whereColumn('claim_expense_items.attachment_reference', 'claims.supporting_document_id')
            ->select(['claim_expense_items.id', 'claims.supporting_document_id'])
            ->orderBy('claim_expense_items.id')
            ->chunkById(100, function ($items) use ($connection): void {
                foreach ($items as $item) {
                    $connection->table('claim_expense_items')->where('id', $item->id)
                        ->whereNull('attachment_object_key')
                        ->update(['attachment_object_key' => $item->supporting_document_id]);
                }
            }, 'claim_expense_items.id', 'id');
    }

    public function down(): void
    {
        // Retain object references on rollback so an application rollback cannot orphan uploaded evidence.
    }
};
