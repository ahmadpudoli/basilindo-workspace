<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('core')->create('external_identities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('issuer');
            $table->string('subject');
            $table->string('provider')->default('project-sso');
            $table->jsonb('claims')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->unique(['issuer', 'subject']);
            $table->index(['user_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::connection('core')->dropIfExists('external_identities');
    }
};
