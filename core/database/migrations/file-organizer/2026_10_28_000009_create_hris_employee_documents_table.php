<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->create('employee_documents', function (Blueprint $table): void {
            $table->string('id', 26)->primary();
            $table->string('employee_id', 26);
            $table->unsignedBigInteger('company_id');
            $table->string('type', 50);
            $table->string('title');
            $table->string('original_filename');
            $table->string('object_key');
            $table->string('disk', 50);
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('file_size');
            $table->string('checksum', 64)->nullable();
            $table->date('effective_from')->nullable();
            $table->date('expires_at')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['employee_id', 'type']);
            $table->index(['company_id', 'created_at']);
            $table->index(['company_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::connection('hris')->dropIfExists('employee_documents');
    }
};
