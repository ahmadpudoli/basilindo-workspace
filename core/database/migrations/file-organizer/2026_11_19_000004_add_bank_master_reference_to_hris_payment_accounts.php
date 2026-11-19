<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $hrisSchema = Schema::connection('hris');
        if (! $hrisSchema->hasTable('employee_payment_accounts')) return;

        $hrisSchema->table('employee_payment_accounts', function (Blueprint $table) use ($hrisSchema): void {
            if (! $hrisSchema->hasColumn('employee_payment_accounts', 'bank_id')) {
                $table->string('bank_id', 48)->nullable()->index();
            }
            if (! $hrisSchema->hasColumn('employee_payment_accounts', 'bank_code')) {
                $table->string('bank_code', 3)->nullable();
            }
        });

        if (! Schema::connection('core')->hasTable('banks')) return;
        $bankLookup = [];
        foreach (DB::connection('core')->table('banks')->get(['id', 'bank_code', 'name', 'short_name']) as $bank) {
            foreach ([$bank->name, $bank->short_name] as $candidate) {
                $normalized = $this->normalize($candidate);
                if ($normalized !== '' && ! isset($bankLookup[$normalized])) {
                    $bankLookup[$normalized] = ['id' => $bank->id, 'code' => $bank->bank_code];
                }
            }
        }

        DB::connection('hris')->table('employee_payment_accounts')->whereNull('bank_id')->orderBy('id')->chunkById(200, function ($accounts) use ($bankLookup): void {
            foreach ($accounts as $account) {
                $match = $bankLookup[$this->normalize($account->bank_name ?? '')] ?? null;
                if ($match) {
                    DB::connection('hris')->table('employee_payment_accounts')->where('id', $account->id)->update([
                        'bank_id' => $match['id'],
                        'bank_code' => $match['code'],
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        // Keep bank references and snapshots so rollback never loses account history.
    }

    private function normalize(?string $value): string
    {
        return strtolower(preg_replace('/[^a-z0-9]/i', '', trim((string) $value)));
    }
};
