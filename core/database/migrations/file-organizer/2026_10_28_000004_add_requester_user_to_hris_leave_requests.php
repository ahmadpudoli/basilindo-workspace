<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('hris')->hasColumn('leave_requests', 'requester_user_id')) {
            Schema::connection('hris')->table('leave_requests', function (Blueprint $table): void {
                $table->unsignedBigInteger('requester_user_id')->nullable()->index();
            });
        }

        DB::connection('hris')->statement('UPDATE "hris_leave_requests" SET "requester_user_id" = "created_by" WHERE "requester_user_id" IS NULL AND "created_by" IS NOT NULL');
    }

    public function down(): void
    {
        if (Schema::connection('hris')->hasColumn('leave_requests', 'requester_user_id')) {
            Schema::connection('hris')->table('leave_requests', function (Blueprint $table): void {
                $table->dropIndex(['requester_user_id']);
                $table->dropColumn('requester_user_id');
            });
        }
    }
};
