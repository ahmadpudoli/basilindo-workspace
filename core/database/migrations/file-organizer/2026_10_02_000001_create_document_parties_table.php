<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('database.connections.pgsql.prefix', '');

        Schema::create('document_parties', function (Blueprint $table) use ($prefix): void {
            $table->id();
            $table->uuid('document_id');
            $table->unsignedBigInteger('company_id');
            $table->string('role');
            $table->timestamps();
            $table->foreign('document_id')->references('id')->on('documents')->cascadeOnDelete();
            $table->unique(['document_id', 'company_id', 'role'], $prefix.'document_parties_unique');
            $table->index(['company_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_parties');
    }
};
