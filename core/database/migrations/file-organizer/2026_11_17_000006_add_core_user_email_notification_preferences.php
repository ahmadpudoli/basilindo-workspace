<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('core')->hasColumn('users', 'email_notification_preferences')) {
            Schema::connection('core')->table('users', function (Blueprint $table): void {
                $table->json('email_notification_preferences')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Retain saved notification preferences during rollback.
    }
};
