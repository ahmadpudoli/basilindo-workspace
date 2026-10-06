<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->jsonb('tags')->nullable()->after('metadata');
            $table->date('retention_until')->nullable()->index();
            $table->boolean('legal_hold')->default(false)->index();
            $table->index(['company_id', 'status', 'document_date']);
        });

        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->string('name');
            $table->jsonb('filters');
            $table->timestamps();
            $table->unique(['user_id', 'company_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'status', 'document_date']);
            $table->dropColumn(['tags', 'retention_until', 'legal_hold']);
        });
    }
};
