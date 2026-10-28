<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->create('leave_request_date_link_repairs', function (Blueprint $table): void {
            $table->bigInteger('detail_id')->primary();
            $table->string('old_leave_request_id', 26);
            $table->string('new_leave_request_id', 26);
            $table->timestamps();
        });

        DB::connection('hris')->statement(<<<'SQL'
            WITH orphan_groups AS (
                SELECT detail.leave_request_id AS orphan_id,
                       detail.created_at,
                       COUNT(*) AS detail_count
                FROM hris_leave_request_dates detail
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM hris_leave_requests request_exists
                    WHERE request_exists.id = detail.leave_request_id
                )
                GROUP BY detail.leave_request_id, detail.created_at
            ),
            matches AS (
                SELECT orphan_groups.orphan_id,
                       MIN(request.id) AS request_id
                FROM orphan_groups
                JOIN hris_leave_requests request
                  ON request.created_at = orphan_groups.created_at
                 AND request.total_days = orphan_groups.detail_count
                GROUP BY orphan_groups.orphan_id
                HAVING COUNT(request.id) = 1
            )
            INSERT INTO hris_leave_request_date_link_repairs
                (detail_id, old_leave_request_id, new_leave_request_id, created_at, updated_at)
            SELECT detail.id, detail.leave_request_id, matches.request_id, NOW(), NOW()
            FROM hris_leave_request_dates detail
            JOIN matches ON matches.orphan_id = detail.leave_request_id
        SQL);

        DB::connection('hris')->statement(<<<'SQL'
            UPDATE hris_leave_request_dates detail
            SET leave_request_id = repairs.new_leave_request_id
            FROM hris_leave_request_date_link_repairs repairs
            WHERE detail.id = repairs.detail_id
        SQL);
    }

    public function down(): void
    {
        if (! Schema::connection('hris')->hasTable('leave_request_date_link_repairs')) {
            return;
        }

        DB::connection('hris')->statement(<<<'SQL'
            UPDATE hris_leave_request_dates detail
            SET leave_request_id = repairs.old_leave_request_id
            FROM hris_leave_request_date_link_repairs repairs
            WHERE detail.id = repairs.detail_id
        SQL);

        Schema::connection('hris')->dropIfExists('leave_request_date_link_repairs');
    }
};
