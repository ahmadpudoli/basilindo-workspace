<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable();
        });

        Schema::create('ticket_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            // Prevent duplicate assignments
            $table->unique(['ticket_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_users');
        
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
};
