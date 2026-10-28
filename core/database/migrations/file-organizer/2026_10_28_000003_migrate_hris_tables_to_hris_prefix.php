<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'employees', 'settings', 'leave_requests', 'overtime_requests',
        'claims', 'approval_events', 'leave_request_dates', 'workflow_approvers',
    ];

    public function up(): void
    {
        $connection = DB::connection('hris');
        foreach ($this->tables as $table) {
            $legacy = 'ws_hris_'.$table;
            $target = 'hris_'.$table;
            $legacyExists = $connection->selectOne('SELECT to_regclass(?) AS name', [$legacy])?->name !== null;
            $targetExists = $connection->selectOne('SELECT to_regclass(?) AS name', [$target])?->name !== null;
            if ($legacyExists && ! $targetExists) {
                $connection->statement('ALTER TABLE "'.$legacy.'" RENAME TO "'.$target.'"');
            }
        }

        if (Schema::connection('hris')->hasTable('leave_requests')) {
            Schema::connection('hris')->table('leave_requests', function (Blueprint $table): void {
                $table->date('start_date')->nullable()->change();
                $table->date('end_date')->nullable()->change();
            });
        }

        if (Schema::connection('hris')->hasTable('leave_requests') && Schema::connection('hris')->hasTable('leave_request_dates')) {
            $connection->statement(<<<'SQL'
                INSERT INTO "hris_leave_request_dates" ("leave_request_id", "leave_date", "created_at", "updated_at")
                SELECT request."id", series::date, request."created_at", request."updated_at"
                FROM "hris_leave_requests" request
                CROSS JOIN LATERAL generate_series(request."start_date", request."end_date", interval '1 day') series
                WHERE request."start_date" IS NOT NULL
                  AND request."end_date" IS NOT NULL
                  AND NOT EXISTS (
                      SELECT 1 FROM "hris_leave_request_dates" detail
                      WHERE detail."leave_request_id" = request."id" AND detail."leave_date" = series::date
                  )
            SQL);
        }
    }

    public function down(): void
    {
        $connection = DB::connection('hris');
        foreach (array_reverse($this->tables) as $table) {
            $legacy = 'ws_hris_'.$table;
            $target = 'hris_'.$table;
            $legacyExists = $connection->selectOne('SELECT to_regclass(?) AS name', [$legacy])?->name !== null;
            $targetExists = $connection->selectOne('SELECT to_regclass(?) AS name', [$target])?->name !== null;
            if ($targetExists && ! $legacyExists) {
                $connection->statement('ALTER TABLE "'.$target.'" RENAME TO "'.$legacy.'"');
            }
        }
    }
};
