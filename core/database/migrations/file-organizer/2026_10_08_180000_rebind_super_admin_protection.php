<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $connection = DB::connection('core');

        $connection->unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION prevent_invalid_super_admin_role()
            RETURNS trigger AS $$
            DECLARE
                role_name text;
                is_protected boolean;
            BEGIN
                IF TG_OP = 'DELETE' THEN
                    SELECT name INTO role_name FROM core_roles WHERE id = OLD.role_id;
                    IF role_name = 'super_admin' THEN
                        SELECT (is_system_account OR LOWER(email) = 'admin@example.com')
                        INTO is_protected FROM core_users WHERE id = OLD.model_id;
                        IF OLD.model_type = 'Core\Models\User' AND COALESCE(is_protected, false) THEN
                            RAISE EXCEPTION 'The protected super_admin account cannot lose its role';
                        END IF;
                    END IF;
                    RETURN OLD;
                END IF;

                SELECT name INTO role_name FROM core_roles WHERE id = NEW.role_id;
                IF role_name = 'super_admin' AND NEW.model_type = 'Core\Models\User' THEN
                    SELECT (is_system_account OR LOWER(email) = 'admin@example.com')
                    INTO is_protected FROM core_users WHERE id = NEW.model_id;
                    IF NOT COALESCE(is_protected, false) THEN
                        RAISE EXCEPTION 'Only the protected system account may have the super_admin role';
                    END IF;
                    IF EXISTS (
                        SELECT 1 FROM core_model_has_roles
                        WHERE role_id = NEW.role_id
                          AND model_type = NEW.model_type
                          AND model_id <> NEW.model_id
                    ) THEN
                        RAISE EXCEPTION 'Only one super_admin account is allowed';
                    END IF;
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS model_has_roles_protect_super_admin ON core_model_has_roles;
            CREATE TRIGGER model_has_roles_protect_super_admin
            BEFORE INSERT OR UPDATE OR DELETE ON core_model_has_roles
            FOR EACH ROW EXECUTE FUNCTION prevent_invalid_super_admin_role();
        SQL);
    }

    public function down(): void
    {
        DB::connection('core')->unprepared(
            'DROP TRIGGER IF EXISTS model_has_roles_protect_super_admin ON core_model_has_roles; DROP FUNCTION IF EXISTS prevent_invalid_super_admin_role();',
        );
    }
};
