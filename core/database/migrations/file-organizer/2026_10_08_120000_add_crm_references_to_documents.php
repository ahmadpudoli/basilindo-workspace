<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table): void {
            $table->uuid('crm_account_id')->nullable()->after('project_id');
            $table->uuid('crm_opportunity_id')->nullable()->after('crm_account_id');
            $table->foreign('crm_account_id')->references('id')->on('crm_accounts')->nullOnDelete();
            $table->foreign('crm_opportunity_id')->references('id')->on('crm_opportunities')->nullOnDelete();
            $table->index(['crm_account_id', 'crm_opportunity_id']);
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table): void {
            $table->dropForeign(['crm_account_id']);
            $table->dropForeign(['crm_opportunity_id']);
            $table->dropIndex(['crm_account_id', 'crm_opportunity_id']);
            $table->dropColumn(['crm_account_id', 'crm_opportunity_id']);
        });
    }
};
