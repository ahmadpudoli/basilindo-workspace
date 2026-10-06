<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verification_items', function (Blueprint $table) {
            $table->text('reviewer_comment')->nullable()->after('discrepancy');
        });
    }

    public function down(): void
    {
        Schema::table('verification_items', function (Blueprint $table) {
            $table->dropColumn('reviewer_comment');
        });
    }
};
