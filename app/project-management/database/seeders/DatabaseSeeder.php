<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Shared users are seeded by File Organizer/core only.
        $this->call(RoleSeeder::class);
    }
}
