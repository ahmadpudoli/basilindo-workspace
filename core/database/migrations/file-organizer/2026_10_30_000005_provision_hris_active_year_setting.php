<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaultYear = now()->year;
        $settings = DB::connection('hris')->table('settings');

        $exists = $settings
            ->whereNull('company_id')
            ->where('key', 'hris.active_year')
            ->whereNull('effective_from')
            ->where('is_active', true)
            ->whereNull('archived_at')
            ->exists();

        if (! $exists) {
            $settings->insert([
                'company_id' => null,
                'key' => 'hris.active_year',
                'value' => json_encode($defaultYear, JSON_THROW_ON_ERROR),
                'effective_from' => null,
                'is_active' => true,
                'archived_at' => null,
                'archived_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $defaultYear = now()->year;

        DB::connection('hris')->table('settings')
            ->whereNull('company_id')
            ->where('key', 'hris.active_year')
            ->whereNull('effective_from')
            ->where('is_active', true)
            ->whereNull('archived_at')
            ->whereRaw('value = ?::jsonb', [json_encode($defaultYear, JSON_THROW_ON_ERROR)])
            ->delete();
    }
};
