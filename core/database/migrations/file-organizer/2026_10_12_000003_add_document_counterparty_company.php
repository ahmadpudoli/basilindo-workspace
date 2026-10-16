<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('documents');

        if (! $schema->hasColumn('documents', 'counterparty_company_id')) {
            $schema->table('documents', function (Blueprint $table): void {
                $table->unsignedBigInteger('counterparty_company_id')->nullable()->after('company_id');
                $table->index('counterparty_company_id', 'doc_documents_counterparty_company_index');
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('documents');

        if ($schema->hasColumn('documents', 'counterparty_company_id')) {
            $schema->table('documents', function (Blueprint $table): void {
                $table->dropIndex('doc_documents_counterparty_company_index');
                $table->dropColumn('counterparty_company_id');
            });
        }
    }
};
