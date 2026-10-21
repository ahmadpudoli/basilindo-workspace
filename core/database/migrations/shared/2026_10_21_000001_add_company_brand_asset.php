<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('core');

        if (! $schema->hasColumn('companies', 'logo_disk')) {
            $schema->table('companies', function (Blueprint $table): void {
                $table->string('logo_disk', 50)->nullable()->after('storage_key');
                $table->string('logo_object_key', 500)->nullable()->after('logo_disk');
                $table->string('logo_mime_type', 100)->nullable()->after('logo_object_key');
                $table->unsignedBigInteger('logo_file_size')->nullable()->after('logo_mime_type');
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('core');

        if ($schema->hasColumn('companies', 'logo_disk')) {
            $schema->table('companies', function (Blueprint $table): void {
                $table->dropColumn(['logo_disk', 'logo_object_key', 'logo_mime_type', 'logo_file_size']);
            });
        }
    }
};
