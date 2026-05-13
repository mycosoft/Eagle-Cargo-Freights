<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');

        // Add SMTP settings from .env
        $settings['smtp_host'] = env('MAIL_HOST');
        $settings['smtp_port'] = env('MAIL_PORT');
        $settings['smtp_username'] = env('MAIL_USERNAME');
        $settings['smtp_password'] = env('MAIL_PASSWORD');
        $settings['smtp_encryption'] = env('MAIL_ENCRYPTION');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_email' => 'nullable|email',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string|max:255',
            'system_currency' => 'nullable|string|max:10',
            'site_logo' => 'nullable|image|max:2048',
            'smtp_host' => 'nullable|string',
            'smtp_port' => 'nullable|integer',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'notify_status_change_email' => 'nullable|boolean',
            'notify_status_change_sms' => 'nullable|boolean',
            'notify_status_change_whatsapp' => 'nullable|boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('logos', 'public');
            Setting::set('site_logo', $path, 'file');
        }

        // Handle SMTP settings - update .env file
        $smtpSettings = [
            'MAIL_HOST' => $validated['smtp_host'] ?? env('MAIL_HOST'),
            'MAIL_PORT' => $validated['smtp_port'] ?? env('MAIL_PORT'),
            'MAIL_USERNAME' => $validated['smtp_username'] ?? env('MAIL_USERNAME'),
            'MAIL_ENCRYPTION' => $validated['smtp_encryption'] ?? env('MAIL_ENCRYPTION'),
        ];

        // Only update password if provided
        if (! empty($validated['smtp_password'])) {
            $smtpSettings['MAIL_PASSWORD'] = $validated['smtp_password'];
        }

        $this->updateEnvFile($smtpSettings);

        // Handle notification settings
        $notificationSettings = [
            'notify_status_change_email',
            'notify_status_change_sms',
            'notify_status_change_whatsapp',
        ];

        foreach ($notificationSettings as $setting) {
            $value = $request->has($setting) ? '1' : '0';
            Setting::set($setting, $value, 'boolean');
        }

        // Save other settings (excluding SMTP and notification settings)
        $excludedKeys = array_merge(['site_logo', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption'], $notificationSettings);

        foreach ($validated as $key => $value) {
            if (! in_array($key, $excludedKeys) && $value !== null) {
                $type = in_array($key, ['site_email']) ? 'email' : 'text';
                Setting::set($key, $value, $type);
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Update .env file with new values
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            // Quote value if it contains spaces or special characters
            if (preg_match('/[\s\'"\\\\]/', $value)) {
                $value = '"'.str_replace('"', '\\"', $value).'"';
            }

            // Check if key exists
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                // Update existing key
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                // Add new key
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $envContent);
    }
}
