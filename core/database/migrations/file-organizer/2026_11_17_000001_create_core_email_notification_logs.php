<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('core')->table('users', function (Blueprint $table): void {
            if (! Schema::connection('core')->hasColumn('users', 'email_notifications_enabled')) {
                $table->boolean('email_notifications_enabled')->default(true)->index();
            }
        });

        if (! Schema::connection('core')->hasTable('email_notification_logs')) {
            Schema::connection('core')->create('email_notification_logs', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('notification_id')->nullable()->unique();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('recipient');
                $table->string('template');
                $table->string('subject');
                $table->longText('encrypted_body');
                $table->string('status', 30)->default('queued')->index();
                $table->unsignedSmallInteger('attempts')->default(0);
                $table->timestamp('queued_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();
                $table->index(['status', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        // Preserve queued email history and user preferences during rollback.
    }
};
