<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $localAdminEmail = (string) env('LOCAL_ADMIN_EMAIL', 'admin@example.com');
        $localPassword = (string) env('LOCAL_ADMIN_PASSWORD', '12345678');

        // Create/update the local administrator account used to access the SSO console.
        $this->call(RoleSeeder::class);

        $admin = User::updateOrCreate(
            ['email' => $localAdminEmail],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => $localPassword,
            ],
        );

        $admin->assignRole('super_admin');

        // Development recovery action: keep every existing local SSO account usable.
        // Hash explicitly because this bulk update bypasses the model cast.
        User::query()->update([
            'password' => Hash::make($localPassword),
            'updated_at' => now(),
        ]);
    }
}
