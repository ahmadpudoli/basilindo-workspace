<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE application_user_access ADD COLUMN id BIGSERIAL');
        DB::statement('ALTER TABLE application_user_access DROP CONSTRAINT application_user_access_pkey');
        DB::statement('ALTER TABLE application_user_access ADD PRIMARY KEY (id)');
        DB::statement('ALTER TABLE application_user_access ADD CONSTRAINT application_user_access_unique_access UNIQUE (application_id, user_id, role_code)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE application_user_access DROP CONSTRAINT application_user_access_unique_access');
        DB::statement('ALTER TABLE application_user_access DROP CONSTRAINT application_user_access_pkey');
        DB::statement('ALTER TABLE application_user_access ADD PRIMARY KEY (application_id, user_id, role_code)');
        DB::statement('ALTER TABLE application_user_access DROP COLUMN id');
    }
};
