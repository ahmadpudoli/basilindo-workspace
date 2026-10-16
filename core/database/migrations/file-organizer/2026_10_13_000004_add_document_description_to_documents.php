<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('documents');

        if (! $schema->hasColumn('documents', 'description')) {
            $schema->table('documents', function (Blueprint $table): void {
                $table->text('description')->nullable()->after('title');
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('documents');

        if ($schema->hasColumn('documents', 'description')) {
            $schema->table('documents', function (Blueprint $table): void {
                $table->dropColumn('description');
            });
        }
    }
};
