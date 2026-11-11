<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('crm');

        if (! $schema->hasColumn('opportunities', 'source_record_status')) {
            $schema->table('opportunities', function (Blueprint $table): void {
                $table->string('source_record_status', 30)->nullable()->after('is_integrated');
                $table->index(['source_system', 'source_database', 'source_record_status'], 'crm_opportunities_source_lifecycle_idx');
            });
        }

        DB::connection('crm')->table('opportunities')
            ->whereNotNull('source_system')
            ->whereNull('source_record_status')
            ->update(['source_record_status' => 'active']);
    }

    public function down(): void
    {
        if (Schema::connection('crm')->hasColumn('opportunities', 'source_record_status')) {
            Schema::connection('crm')->table('opportunities', function (Blueprint $table): void {
                $table->dropIndex('crm_opportunities_source_lifecycle_idx');
                $table->dropColumn('source_record_status');
            });
        }
    }
};
