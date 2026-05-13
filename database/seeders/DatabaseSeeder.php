<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run all seeders in proper order
        $this->call([
            RoleSeeder::class,
            SettingSeeder::class,
            AdminUserSeeder::class,
            ExpenseCategorySeeder::class,
        ]);

        // Create hidden API user
        $apiUser = User::updateOrCreate(
            ['email' => 'apiuser@eaglecargofreights.com'],
            [
                'name' => 'API User',
                'password' => Hash::make('password'),
                'is_hidden' => true,
            ]
        );
        $apiUser->assignRole('admin');

        $this->command->info('All seeders completed successfully!');
        $this->command->info('API User: apiuser@eaglecargofreights.com / password (hidden from users list)');
    }
}
