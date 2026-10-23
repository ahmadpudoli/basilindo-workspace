<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $connection = DB::connection('pgsql');
        $legacyTable = DB::connection('documents')->getTablePrefix().'audit_events';

        $connection->statement('CREATE SCHEMA IF NOT EXISTS legacy');
        if ($this->exists('public', $legacyTable) && ! $this->exists('legacy', $legacyTable)) {
            $connection->statement('ALTER TABLE "'.$legacyTable.'" SET SCHEMA legacy');
        }
    }

    public function down(): void
    {
        $connection = DB::connection('pgsql');
        $legacyTable = DB::connection('documents')->getTablePrefix().'audit_events';

        if ($this->exists('legacy', $legacyTable) && ! $this->exists('public', $legacyTable)) {
            $connection->statement('ALTER TABLE legacy."'.$legacyTable.'" SET SCHEMA public');
        }
    }

    private function exists(string $schema, string $table): bool
    {
        return DB::connection('pgsql')->selectOne(
            'select to_regclass(?) as relation_name', [''.$schema.'.'.$table],
        )?->relation_name !== null;
    }
};
