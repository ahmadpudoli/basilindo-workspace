<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_statuses', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('name');
            $table->string('color')->default('#64748B');
            $table->boolean('is_completed')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['project_id', 'sort_order']);
        });

        Schema::create('ticket_priorities', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->default('#64748B');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('ticket_status_id')->nullable();
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['project_id', 'ticket_status_id']);
            $table->index(['priority_id', 'due_date']);
        });

        Schema::create('ticket_users', function (Blueprint $table): void {
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->primary(['ticket_id', 'user_id']);
            $table->index('user_id');
        });

        DB::table('ticket_priorities')->insert([
            ['name' => 'Low', 'color' => '#22C55E', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medium', 'color' => '#F59E0B', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'High', 'color' => '#EF4444', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('ticket_statuses')->insert([
            ['name' => 'Backlog', 'color' => '#64748B', 'is_completed' => false, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'In Progress', 'color' => '#3B82F6', 'is_completed' => false, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Done', 'color' => '#22C55E', 'is_completed' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_users');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_priorities');
        Schema::dropIfExists('ticket_statuses');
    }
};
