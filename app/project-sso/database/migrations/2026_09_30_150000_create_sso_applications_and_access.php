<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sso_applications', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('url');
            $table->string('oauth_client_id')->unique();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('application_user_access', function (Blueprint $table) {
            $table->foreignId('application_id')->constrained('sso_applications')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role_code');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->primary(['application_id', 'user_id', 'role_code']);
            $table->index(['user_id', 'status']);
        });

        DB::table('sso_applications')->insert([
            'code' => 'file-organizer',
            'name' => 'File Organizer',
            'url' => 'http://localhost:8000',
            'oauth_client_id' => '01a0f145-1826-7130-9bb3-c451b3ff668e',
            'icon' => 'document',
            'sort_order' => 10,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('application_user_access');
        Schema::dropIfExists('sso_applications');
    }
};
