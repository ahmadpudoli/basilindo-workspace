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
        $schema = Schema::connection('hris');

        if (! $schema->hasTable('expense_categories')) {
            $schema->create('expense_categories', function (Blueprint $table): void {
                $table->string('id', 26)->primary();
                $table->string('name', 100)->unique();
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true)->index();
                $table->timestamp('archived_at')->nullable();
                $table->unsignedBigInteger('archived_by')->nullable();
                $table->timestamps();
            });
        }

        $defaults = [
            'Transportasi',
            'Akomodasi',
            'Konsumsi',
            'Perlengkapan dan kebutuhan kantor',
            'Komunikasi dan internet',
            'Pengiriman dan kurir',
            'Biaya proyek atau client',
            'Lain-lain',
        ];
        $legacyNames = DB::connection('hris')->table('claim_expense_items')->distinct()->pluck('category')->all();
        $names = collect([...$defaults, ...$legacyNames])->map(fn ($name): string => trim((string) $name))->filter()->unique()->values();

        foreach ($names as $index => $name) {
            DB::connection('hris')->table('expense_categories')->insertOrIgnore([
                'id' => (string) Str::ulid(),
                'name' => $name,
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (! $schema->hasColumn('claim_expense_items', 'expense_category_id')) {
            $schema->table('claim_expense_items', function (Blueprint $table): void {
                $table->string('expense_category_id', 26)->nullable()->index();
                $table->foreign('expense_category_id')->references('id')->on('expense_categories')->restrictOnDelete();
            });
        }

        $categoryIds = DB::connection('hris')->table('expense_categories')->pluck('id', 'name');
        foreach ($categoryIds as $name => $id) {
            DB::connection('hris')->table('claim_expense_items')->where('category', $name)->update(['expense_category_id' => $id]);
        }
    }

    public function down(): void
    {
        // Keep the category master and nullable reference during rollback so configured categories and historical links survive.
    }
};
