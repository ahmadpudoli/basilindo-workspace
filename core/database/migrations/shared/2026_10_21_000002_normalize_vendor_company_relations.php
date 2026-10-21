<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('core');

        if (! $schema->hasColumn('vendors', 'vendor_company_id')) {
            $schema->table('vendors', function (Blueprint $table): void {
                $table->foreignId('vendor_company_id')->nullable()->after('company_id')->constrained('companies')->nullOnDelete();
                $table->index(['vendor_company_id', 'status']);
            });
        }

        if (! $schema->hasTable('vendor_operational_companies')) {
            $schema->create('vendor_operational_companies', function (Blueprint $table): void {
                $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->timestamps();
                $table->primary(['vendor_id', 'company_id']);
                $table->index('company_id');
            });
        }

        DB::connection('core')->table('vendors')->whereNotNull('company_id')->orderBy('id')->get(['id', 'company_id'])->each(function (object $vendor): void {
            DB::connection('core')->table('vendor_operational_companies')->insertOrIgnore([
                'vendor_id' => $vendor->id,
                'company_id' => $vendor->company_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        $schema = Schema::connection('core');
        if ($schema->hasTable('vendor_operational_companies')) {
            $schema->drop('vendor_operational_companies');
        }
        if ($schema->hasColumn('vendors', 'vendor_company_id')) {
            $schema->table('vendors', function (Blueprint $table): void {
                $table->dropForeign(['vendor_company_id']);
                $table->dropIndex(['vendor_company_id', 'status']);
                $table->dropColumn('vendor_company_id');
            });
        }
    }
};
