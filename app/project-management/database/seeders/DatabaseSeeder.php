<?php

namespace Database\Seeders;

use Core\Models\User;
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

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => env('LOCAL_ADMIN_PASSWORD', '12345678'),
                'is_system_account' => true,
            ],
        );

        $admin->forceFill(['is_system_account' => true])->save();
        $admin->assignRole('super_admin');
    }
}
