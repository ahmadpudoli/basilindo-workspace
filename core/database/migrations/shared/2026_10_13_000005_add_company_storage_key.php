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
        $schema = Schema::connection('core');

        if (! $schema->hasColumn('companies', 'storage_key')) {
            $schema->table('companies', function (Blueprint $table): void {
                $table->string('storage_key', 26)->nullable()->unique()->after('code');
            });
        }

        DB::connection('core')
            ->table('companies')
            ->whereNull('storage_key')
            ->orderBy('id')
            ->get(['id'])
            ->each(function (object $company): void {
                DB::connection('core')
                    ->table('companies')
                    ->where('id', $company->id)
                    ->update(['storage_key' => (string) Str::ulid()]);
            });
    }

    public function down(): void
    {
        $schema = Schema::connection('core');

        if ($schema->hasColumn('companies', 'storage_key')) {
            $schema->table('companies', function (Blueprint $table): void {
                $table->dropUnique(['storage_key']);
                $table->dropColumn('storage_key');
            });
        }
    }
};
