<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('core')->create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('group')->nullable();
            $table->string('key');
            $table->text('value')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'key'], 'settings_user_id_key_unique');
        });
    }

    public function down(): void
    {
        Schema::connection('core')->dropIfExists('settings');
    }
};
