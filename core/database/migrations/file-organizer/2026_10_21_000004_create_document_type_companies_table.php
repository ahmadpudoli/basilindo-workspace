<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('documents')->create('document_type_companies', function (Blueprint $table): void {
            $table->foreignId('document_type_id')->constrained('document_types')->cascadeOnDelete();
            $table->unsignedBigInteger('company_id');
            $table->timestamps();
            $table->primary(['document_type_id', 'company_id']);
            $table->index('company_id');
        });

        DB::connection('documents')->table('document_types')
            ->whereNotNull('company_id')
            ->get(['id', 'company_id'])
            ->each(function (object $type): void {
                DB::connection('documents')->table('document_type_companies')->insert([
                    'document_type_id' => $type->id,
                    'company_id' => $type->company_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::connection('documents')->dropIfExists('document_type_companies');
    }
};
