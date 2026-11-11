<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $companies = DB::connection('core')->table('companies')->pluck('id');
        foreach ($companies as $companyId) {
            DB::connection('core')->table('numbering_rules')->insertOrIgnore([
                'company_id' => $companyId,
                'form_key' => 'hris_overtime',
                'name' => 'Pengajuan Lembur',
                'format' => 'OT/[tahun]/[bulan_romawi]/[no_urut]',
                'counter_scope' => 'yearly',
                'next_number' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void {}
};
