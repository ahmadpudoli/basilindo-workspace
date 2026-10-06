<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $coreSchema = Schema::connection('core');

        if (! $coreSchema->hasColumn('users', 'is_system_account')) {
            $coreSchema->table('users', function (Blueprint $table): void {
                $table->boolean('is_system_account')->default(false)->index();
            });
        }

        DB::connection('core')
            ->table('users')
            ->whereRaw('LOWER(email) = ?', ['admin@example.com'])
            ->update(['is_system_account' => true]);

        DB::connection('core')->unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION prevent_system_account_delete()
            RETURNS trigger AS $$
            BEGIN
                IF OLD.is_system_account OR LOWER(OLD.email) = 'admin@example.com' THEN
                    RAISE EXCEPTION 'System account cannot be deleted';
                END IF;
                RETURN OLD;
            END;
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS users_prevent_system_account_delete ON core_users;
            CREATE TRIGGER users_prevent_system_account_delete
            BEFORE DELETE ON core_users
            FOR EACH ROW EXECUTE FUNCTION prevent_system_account_delete();
        SQL);

        Schema::dropIfExists('saved_searches');
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
        DB::connection('core')->unprepared(<<<'SQL'
            DROP TRIGGER IF EXISTS users_prevent_system_account_delete ON core_users;
            DROP FUNCTION IF EXISTS prevent_system_account_delete();
        SQL);
        if (Schema::connection('core')->hasColumn('users', 'is_system_account')) {
            Schema::connection('core')->table('users', function (Blueprint $table): void {
                $table->dropColumn('is_system_account');
            });
        }
    }
};
