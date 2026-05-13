<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            ['key' => 'site_name', 'value' => 'Eagle Cargo Freights', 'type' => 'text'],
            ['key' => 'site_email', 'value' => 'eaglecargofreights@gmail.com', 'type' => 'email'],
            ['key' => 'site_phone', 'value' => 'UG +256 701 57941 / China +86 130 7021 8275', 'type' => 'text'],
            ['key' => 'site_address', 'value' => 'Nabugabo BTC Building, Kampala', 'type' => 'text'],
            ['key' => 'smtp_host', 'value' => 'smtp.gmail.com', 'type' => 'text'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'text'],
            ['key' => 'smtp_username', 'value' => '', 'type' => 'text'],
            ['key' => 'smtp_password', 'value' => '', 'type' => 'text'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'text'],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }

        $this->command->info('Default settings created successfully!');
    }
}
