<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CompanySeeder::class);

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => env('LOCAL_TEST_PASSWORD', '12345678'),
            ],
        );

        // Create/update the local administrator account used to access Filament.
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
