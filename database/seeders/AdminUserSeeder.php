<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@eaglecargofreights.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Create default staff user
        $staff = User::updateOrCreate(
            ['email' => 'staff@eaglecargofreights.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
            ]
        );
        $staff->assignRole('staff');

        $this->command->info('Admin and Staff users created successfully!');
        $this->command->info('Admin: admin@eaglecargofreights.com / password');
        $this->command->info('Staff: staff@eaglecargofreights.com / password');
    }
}
