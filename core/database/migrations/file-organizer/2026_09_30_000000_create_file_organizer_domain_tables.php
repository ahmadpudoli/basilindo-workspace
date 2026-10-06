<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('name');
            $table->string('code');
            $table->jsonb('required_fields')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'code']);
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreignId('document_type_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->string('title');
            $table->string('original_filename');
            $table->string('reference_number')->nullable();
            $table->string('normalized_reference_number')->nullable();
            $table->date('document_date')->nullable();
            $table->decimal('amount', 20, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->string('status')->default('pending')->index();
            $table->jsonb('metadata')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'document_date']);
            $table->index(['company_id', 'normalized_reference_number']);
            $table->index(['project_id', 'status']);
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('document_id');
            $table->unsignedInteger('version');
            $table->string('disk')->default('s3');
            $table->string('object_key')->unique();
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->string('checksum', 64);
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->boolean('is_current')->default(true);
            $table->timestamps();
            $table->foreign('document_id')->references('id')->on('documents')->cascadeOnDelete();
            $table->unique(['document_id', 'version']);
            $table->index(['document_id', 'is_current']);
        });

        Schema::create('document_relations', function (Blueprint $table) {
            $table->id();
            $table->uuid('source_document_id');
            $table->uuid('target_document_id');
            $table->string('relation_type');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('source_document_id')->references('id')->on('documents')->cascadeOnDelete();
            $table->foreign('target_document_id')->references('id')->on('documents')->cascadeOnDelete();
            $table->unique(['source_document_id', 'target_document_id', 'relation_type'], 'document_relation_unique');
        });

        Schema::create('verification_cases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('company_id');
            $table->string('status')->default('open')->index();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('summary')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        Schema::create('verification_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('verification_case_id');
            $table->uuid('document_id');
            $table->string('rule_code');
            $table->string('result')->default('pending');
            $table->jsonb('evidence')->nullable();
            $table->text('discrepancy')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->foreign('verification_case_id')->references('id')->on('verification_cases')->cascadeOnDelete();
            $table->foreign('document_id')->references('id')->on('documents')->cascadeOnDelete();
            $table->index(['verification_case_id', 'result']);
        });

        Schema::create('bundles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->string('status')->default('queued')->index();
            $table->jsonb('criteria')->nullable();
            $table->string('disk')->default('s3');
            $table->string('object_key')->nullable();
            $table->unsignedInteger('document_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        Schema::create('audit_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('action');
            $table->nullableMorphs('subject');
            $table->string('request_id')->nullable()->index();
            $table->jsonb('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['company_id', 'action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_events');
        Schema::dropIfExists('bundles');
        Schema::dropIfExists('verification_items');
        Schema::dropIfExists('verification_cases');
        Schema::dropIfExists('document_relations');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_types');
    }
};
