<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->create('leave_status_normalizations', function (Blueprint $table): void {
            $table->string('leave_request_id', 26)->primary();
            $table->string('old_status', 40);
            $table->string('new_status', 40);
            $table->timestamps();
        });

        DB::connection('hris')->table('leave_requests')
            ->whereIn('status', ['submitted', 'cancelled', 'expired'])
            ->get(['id', 'status'])
            ->each(function (object $request): void {
                $newStatus = $request->status === 'submitted' ? 'pending_manager' : 'rejected';
                DB::connection('hris')->table('leave_status_normalizations')->insert([
                    'leave_request_id' => $request->id,
                    'old_status' => $request->status,
                    'new_status' => $newStatus,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::connection('hris')->table('leave_requests')->where('id', $request->id)->update(['status' => $newStatus]);
            });
    }

    public function down(): void
    {
        if (! Schema::connection('hris')->hasTable('leave_status_normalizations')) {
            return;
        }

        DB::connection('hris')->table('leave_status_normalizations')->get()->each(function (object $repair): void {
            DB::connection('hris')->table('leave_requests')->where('id', $repair->leave_request_id)->update(['status' => $repair->old_status]);
        });
        Schema::connection('hris')->dropIfExists('leave_status_normalizations');
    }
};
