<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('core')->table('companies', function (Blueprint $table): void {
            $table->string('entity_type')->default('company')->after('code');
            $table->string('classification')->default('external')->after('entity_type');
            $table->jsonb('roles')->nullable()->after('classification');
            $table->foreignId('parent_company_id')->nullable()->after('roles');
            $table->index(['classification', 'status']);
            $table->index(['parent_company_id', 'status']);
        });

        Schema::connection('core')->table('companies', function (Blueprint $table): void {
            $table->foreign('parent_company_id')
                ->references('id')
                ->on('companies')
                ->nullOnDelete();
        });

        Schema::connection('core')->create('project_parties', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['project_id', 'company_id', 'role'], 'core_project_parties_unique');
            $table->index(['project_id', 'role']);
            $table->index(['company_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::connection('core')->dropIfExists('project_parties');

        Schema::connection('core')->table('companies', function (Blueprint $table): void {
            $table->dropForeign(['parent_company_id']);
            $table->dropIndex(['parent_company_id', 'status']);
            $table->dropIndex(['classification', 'status']);
            $table->dropColumn(['parent_company_id', 'roles', 'classification', 'entity_type']);
        });
    }
};
