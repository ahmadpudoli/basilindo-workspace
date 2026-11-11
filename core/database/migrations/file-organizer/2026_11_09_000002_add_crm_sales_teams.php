<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('crm');
        if (! $schema->hasTable('teams')) {
            $schema->create('teams', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->string('name');
                $table->unsignedBigInteger('leader_user_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['company_id', 'is_active']);
            });
        }
        if (! $schema->hasColumn('opportunities', 'team_id')) {
            $schema->table('opportunities', fn (Blueprint $table) => $table->uuid('team_id')->nullable()->index());
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('crm');
        if ($schema->hasColumn('opportunities', 'team_id')) $schema->table('opportunities', fn (Blueprint $table) => $table->dropColumn('team_id'));
        $schema->dropIfExists('teams');
    }
};
