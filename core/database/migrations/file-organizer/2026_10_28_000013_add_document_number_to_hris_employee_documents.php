<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('hris');
        if (! $schema->hasColumn('employee_documents', 'document_number')) {
            $schema->table('employee_documents', function (Blueprint $table): void {
                $table->string('document_number', 50)->nullable()->after('id');
            });
        }

        $db = DB::connection('hris');
        $db->table('employee_documents')->whereNull('document_number')->orderBy('id')->chunkById(100, function ($documents): void {
            foreach ($documents as $document) {
                DB::connection('hris')->table('employee_documents')->where('id', $document->id)->update([
                    'document_number' => 'HRIS-LEGACY-'.$document->id,
                ]);
            }
        }, 'id');

        $schema->table('employee_documents', function (Blueprint $table): void {
            $table->unique('document_number', 'hris_employee_documents_document_number_unique');
        });
    }

    public function down(): void
    {
        Schema::connection('hris')->table('employee_documents', function (Blueprint $table): void {
            $table->dropUnique('hris_employee_documents_document_number_unique');
            $table->dropColumn('document_number');
        });
    }
};
