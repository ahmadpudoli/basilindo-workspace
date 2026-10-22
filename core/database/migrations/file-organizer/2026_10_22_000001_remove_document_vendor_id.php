<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('documents');

        if (! $schema->hasColumn('documents', 'vendor_id')) {
            return;
        }

        if (DB::connection('documents')->table('documents')->whereNotNull('vendor_id')->exists()) {
            throw new RuntimeException('Kolom doc_documents.vendor_id masih berisi data dan tidak boleh dihapus otomatis.');
        }

        $schema->table('documents', function (Blueprint $table): void {
            $table->dropColumn('vendor_id');
        });
    }

    public function down(): void
    {
        $schema = Schema::connection('documents');

        if (! $schema->hasColumn('documents', 'vendor_id')) {
            $schema->table('documents', function (Blueprint $table): void {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('project_id');
            });
        }
    }
};
