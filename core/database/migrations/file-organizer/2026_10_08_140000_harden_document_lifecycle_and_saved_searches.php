<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table): void {
            $table->text('rejection_reason')->nullable();
            $table->timestampTz('quarantined_at')->nullable()->index();
            $table->timestampTz('released_at')->nullable();
            $table->timestampTz('rejected_at')->nullable();
        });

        Schema::create('saved_searches', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->string('name', 160);
            $table->jsonb('filters');
            $table->timestampsTz();
            $table->unique(['user_id', 'company_id', 'name']);
            $table->index(['company_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_searches');

        Schema::table('documents', function (Blueprint $table): void {
            $table->dropIndex(['quarantined_at']);
            $table->dropColumn(['rejection_reason', 'quarantined_at', 'released_at', 'rejected_at']);
        });
    }
};
