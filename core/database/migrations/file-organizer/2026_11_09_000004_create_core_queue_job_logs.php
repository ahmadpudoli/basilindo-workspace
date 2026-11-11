<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('core')->create('queue_job_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('job_id')->nullable()->index();
            $table->string('connection_name')->nullable();
            $table->string('queue_name')->nullable()->index();
            $table->string('job_name');
            $table->string('status', 30)->default('queued')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::connection('core')->dropIfExists('queue_job_logs');
    }
};
