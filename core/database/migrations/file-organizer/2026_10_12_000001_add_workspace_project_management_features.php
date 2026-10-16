<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $pm = Schema::connection('pm');

        if (! $pm->hasTable('epics')) {
            $pm->create('epics', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('project_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['project_id', 'sort_order']);
            });
        }

        if ($pm->hasTable('tickets') && ! $pm->hasColumn('tickets', 'epic_id')) {
            $pm->table('tickets', function (Blueprint $table): void {
                $table->unsignedBigInteger('epic_id')->nullable()->after('ticket_status_id');
                $table->index('epic_id');
            });
        }

        if (! $pm->hasTable('project_notes')) {
            $pm->create('project_notes', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('project_id');
                $table->unsignedBigInteger('created_by');
                $table->string('title');
                $table->longText('content');
                $table->date('note_date');
                $table->timestamps();
                $table->index(['project_id', 'note_date']);
            });
        }

        if (! $pm->hasTable('ticket_comments')) {
            $pm->create('ticket_comments', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('ticket_id');
                $table->unsignedBigInteger('user_id');
                $table->longText('comment');
                $table->timestamps();
                $table->index('ticket_id');
            });
        }

        if (! $pm->hasTable('ticket_histories')) {
            $pm->create('ticket_histories', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('ticket_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('ticket_status_id')->nullable();
                $table->timestamps();
                $table->index(['ticket_id', 'created_at']);
            });
        }

        $core = Schema::connection('core');
        if (! $core->hasTable('notifications')) {
            $core->create('notifications', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('type');
                $table->string('title');
                $table->text('message');
                $table->json('data')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'read_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::connection('pm')->dropIfExists('ticket_comments');
        Schema::connection('pm')->dropIfExists('ticket_histories');
        Schema::connection('pm')->dropIfExists('project_notes');
        Schema::connection('pm')->dropIfExists('epics');
        Schema::connection('core')->dropIfExists('notifications');
    }
};
