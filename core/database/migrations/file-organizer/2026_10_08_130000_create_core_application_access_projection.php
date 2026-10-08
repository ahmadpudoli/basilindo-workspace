<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('core');

        if ($schema->hasTable('application_access')) {
            return;
        }

        $schema->create('application_access', function (Blueprint $table): void {
            $table->id();
            $table->string('application_code', 100);
            $table->string('subject', 191);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 30)->default('active')->index();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->unique(['application_code', 'subject']);
            $table->index(['application_code', 'status']);
        });
    }

    public function down(): void
    {
        Schema::connection('core')->dropIfExists('application_access');
    }
};
