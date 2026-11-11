<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('crm');

        if (! $schema->hasTable('pipeline_stages')) {
            $schema->create('pipeline_stages', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('code', 80)->unique();
                $table->string('name');
                $table->unsignedInteger('sequence')->default(0);
                $table->boolean('is_won')->default(false);
                $table->boolean('is_delivery')->default(false);
                $table->boolean('is_folded')->default(false);
                $table->boolean('requires_expected_close')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['is_active', 'sequence']);
            });
        }

        if (! $schema->hasTable('closing_statuses')) {
            $schema->create('closing_statuses', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('code', 50)->unique();
                $table->string('name');
                $table->unsignedInteger('sequence')->default(0);
                $table->boolean('is_lost')->default(false);
                $table->boolean('is_ignored')->default(false);
                $table->boolean('is_closed')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! $schema->hasColumn('opportunities', 'stage_id')) {
            $schema->table('opportunities', function (Blueprint $table): void {
                $table->uuid('stage_id')->nullable()->index();
                $table->uuid('closing_status_id')->nullable()->index();
                $table->uuid('contact_id')->nullable()->index();
                $table->string('lost_reason')->nullable();
                $table->timestamp('won_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->string('po_number')->nullable();
                $table->uuid('po_document_id')->nullable();
                $table->string('external_source')->nullable();
                $table->string('external_id')->nullable();
                $table->index(['stage_id', 'closing_status_id']);
            });
        }

        if (! $schema->hasColumn('leads', 'contact_id')) {
            $schema->table('leads', function (Blueprint $table): void {
                $table->uuid('contact_id')->nullable()->index();
                $table->uuid('converted_opportunity_id')->nullable()->index();
                $table->timestamp('converted_at')->nullable();
            });
        }

        DB::connection('crm')->statement('ALTER TABLE crm_contacts ALTER COLUMN account_id DROP NOT NULL');

        $stages = [
            ['code' => 'new', 'name' => 'Baru', 'sequence' => 10],
            ['code' => 'qualified', 'name' => 'Terkualifikasi', 'sequence' => 20],
            ['code' => 'technical_review', 'name' => 'Review Teknis / BOQ', 'sequence' => 30],
            ['code' => 'proposal', 'name' => 'Proposal', 'sequence' => 40],
            ['code' => 'negotiation', 'name' => 'Negosiasi', 'sequence' => 50],
            ['code' => 'won', 'name' => 'Won / PO', 'sequence' => 60, 'is_won' => true],
            ['code' => 'delivery', 'name' => 'Delivery', 'sequence' => 70, 'is_delivery' => true],
        ];
        foreach ($stages as $stage) {
            DB::connection('crm')->table('pipeline_stages')->updateOrInsert(
                ['code' => $stage['code']],
                ['id' => (string) Str::uuid(), 'name' => $stage['name'], 'sequence' => $stage['sequence'], 'is_won' => $stage['is_won'] ?? false, 'is_delivery' => $stage['is_delivery'] ?? false, 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        foreach ([
            ['code' => 'open', 'name' => 'Terbuka', 'sequence' => 10],
            ['code' => 'closed', 'name' => 'Closed', 'sequence' => 20, 'is_closed' => true],
            ['code' => 'ignored', 'name' => 'Diabaikan', 'sequence' => 30, 'is_ignored' => true, 'is_closed' => true],
            ['code' => 'lost', 'name' => 'Kalah', 'sequence' => 40, 'is_lost' => true, 'is_closed' => true],
            ['code' => 'cancelled', 'name' => 'Dibatalkan', 'sequence' => 50, 'is_closed' => true],
        ] as $status) {
            DB::connection('crm')->table('closing_statuses')->updateOrInsert(
                ['code' => $status['code']],
                ['id' => (string) Str::uuid(), 'name' => $status['name'], 'sequence' => $status['sequence'], 'is_lost' => $status['is_lost'] ?? false, 'is_ignored' => $status['is_ignored'] ?? false, 'is_closed' => $status['is_closed'] ?? false, 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        $stageIds = DB::connection('crm')->table('pipeline_stages')->pluck('id', 'code');
        DB::connection('crm')->table('opportunities')->whereNull('stage_id')->orderBy('id')->chunkById(200, function ($items) use ($stageIds): void {
            foreach ($items as $item) {
                $code = match ($item->stage) {
                    'new' => 'new', 'proposal', 'proposition' => 'proposal', 'won' => 'won', 'delivery' => 'delivery', default => 'qualified',
                };
                DB::connection('crm')->table('opportunities')->where('id', $item->id)->update(['stage_id' => $stageIds[$code] ?? null]);
            }
        });

        $openId = DB::connection('crm')->table('closing_statuses')->where('code', 'open')->value('id');
        DB::connection('crm')->table('opportunities')->whereNull('closing_status_id')->update(['closing_status_id' => $openId]);
    }

    public function down(): void
    {
        $schema = Schema::connection('crm');
        if ($schema->hasColumn('leads', 'contact_id')) $schema->table('leads', fn (Blueprint $table) => $table->dropColumn(['contact_id', 'converted_opportunity_id', 'converted_at']));
        if ($schema->hasColumn('opportunities', 'stage_id')) $schema->table('opportunities', fn (Blueprint $table) => $table->dropColumn(['stage_id', 'closing_status_id', 'contact_id', 'lost_reason', 'won_at', 'closed_at', 'po_number', 'po_document_id', 'external_source', 'external_id']));
        $schema->dropIfExists('closing_statuses');
        $schema->dropIfExists('pipeline_stages');
    }
};
