<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('hris')->create('leave_request_dates', function (Blueprint $table): void {
            $table->id();
            $table->string('leave_request_id', 26);
            $table->date('leave_date');
            $table->timestamps();
            $table->unique(['leave_request_id', 'leave_date']);
            $table->index(['leave_date', 'leave_request_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('hris')->dropIfExists('leave_request_dates');
    }
};
