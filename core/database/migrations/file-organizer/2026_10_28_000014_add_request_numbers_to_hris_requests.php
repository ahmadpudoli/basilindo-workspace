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
        foreach (['leave_requests', 'overtime_requests', 'claims'] as $tableName) {
            if (! Schema::connection('hris')->hasColumn($tableName, 'request_number')) {
                Schema::connection('hris')->table($tableName, function (Blueprint $table): void {
                    $table->string('request_number', 70)->nullable()->after('id');
                });
            }
        }

        $prefixes = [
            'leave_requests' => 'LEAVE',
            'overtime_requests' => 'OVERTIME',
            'claims' => 'CLAIM',
        ];

        foreach ($prefixes as $tableName => $prefix) {
            DB::connection('hris')->table($tableName)->whereNull('request_number')->orderBy('id')->chunkById(100, function ($requests) use ($tableName, $prefix): void {
                foreach ($requests as $request) {
                    DB::connection('hris')->table($tableName)->where('id', $request->id)->update([
                        'request_number' => sprintf('HRIS-%s-%s-%s', $prefix, now()->format('Ymd'), Str::upper(Str::random(10))),
                    ]);
                }
            }, 'id');
        }

        foreach (array_keys($prefixes) as $tableName) {
            Schema::connection('hris')->table($tableName, function (Blueprint $table): void {
                $table->unique('request_number');
            });
        }
    }

    public function down(): void
    {
        foreach (['leave_requests', 'overtime_requests', 'claims'] as $tableName) {
            Schema::connection('hris')->table($tableName, function (Blueprint $table): void {
                $table->dropUnique($table->getTable().'_request_number_unique');
                $table->dropColumn('request_number');
            });
        }
    }
};
