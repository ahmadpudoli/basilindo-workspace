<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $crm = Schema::connection('crm');
        $core = Schema::connection('core');

        $crm->table('accounts', function (Blueprint $table): void {
            foreach (['street', 'street2', 'city', 'state', 'zip', 'country'] as $column) {
                $table->string($column)->nullable();
            }
        });

        $core->table('companies', function (Blueprint $table): void {
            foreach (['street', 'street2', 'city', 'state', 'zip', 'country'] as $column) {
                $table->string($column)->nullable();
            }
        });
    }

    public function down(): void
    {
        $crm = Schema::connection('crm');
        $core = Schema::connection('core');

        $crm->table('accounts', function (Blueprint $table): void {
            $table->dropColumn(['street', 'street2', 'city', 'state', 'zip', 'country']);
        });

        $core->table('companies', function (Blueprint $table): void {
            $table->dropColumn(['street', 'street2', 'city', 'state', 'zip', 'country']);
        });
    }
};
