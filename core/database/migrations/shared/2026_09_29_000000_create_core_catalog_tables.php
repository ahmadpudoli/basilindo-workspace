<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('core')->create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection('core')->table('projects', function (Blueprint $table): void {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->index(['company_id', 'name']);
        });

        Schema::connection('core')->create('company_user', function (Blueprint $table): void {
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('scope_role')->default('member');
            $table->timestamps();
            $table->primary(['company_id', 'user_id']);
        });

        Schema::connection('core')->create('vendors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('tax_identifier')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::connection('core')->dropIfExists('vendors');
        Schema::connection('core')->dropIfExists('company_user');
        Schema::connection('core')->table('projects', function (Blueprint $table): void {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id', 'name']);
            $table->dropColumn('company_id');
        });
        Schema::connection('core')->dropIfExists('companies');
    }
};
