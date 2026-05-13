<?php

namespace App\Console\Commands;

use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class TestWhatsAppProviders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:test 
                            {phone? : Phone number to send test message (optional)}
                            {--provider= : Force specific provider (meta/local)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp providers (Meta Cloud API and Local API)';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsappService)
    {
        $this->info('🔍 Testing WhatsApp Providers...');
        $this->newLine();

        // Display provider status
        $this->info('📊 Provider Status:');
        $status = $whatsappService->getStatus();

        $this->table(
            ['Provider Type', 'Name', 'Available'],
            [
                [$status['provider'], $status['name'], $status['available'] ? '✅ Yes' : '❌ No'],
            ]
        );

        $this->newLine();

        // Check if we should send a test message
        $phone = $this->argument('phone');

        if (! $phone) {
            $this->info('💡 To send a test message, run:');
            $this->line('   php artisan whatsapp:test 256774222619');
            $this->newLine();

            // Ask if user wants to send a test message
            if ($this->confirm('Would you like to send a test message now?', false)) {
                $phone = $this->ask('Enter phone number (with country code, e.g., 256774222619)');
            } else {
                $this->info('✅ Provider status check complete!');

                return Command::SUCCESS;
            }
        }

        if ($phone) {
            $this->info("📱 Sending test message to: {$phone}");
            $this->newLine();

            $message = "🧪 Test message from Bryanz Logistics\n\n";
            $message .= 'Provider: '.$whatsappService->getProviderName()."\n";
            $message .= 'Time: '.now()->format('Y-m-d H:i:s')."\n";
            $message .= "\nThis is a test message to verify WhatsApp integration.";

            $this->line('Sending...');

            $result = $whatsappService->sendMessage($phone, $message);

            if ($result['success']) {
                $this->info('✅ Message sent successfully!');
                $this->line('   Provider: '.($result['provider'] ?? 'unknown'));
                $this->line('   Message ID: '.($result['message_id'] ?? 'N/A'));
            } else {
                $this->error('❌ Failed to send message');
                $this->line('   Provider: '.($result['provider'] ?? 'unknown'));
                $this->line('   Error: '.($result['error'] ?? 'Unknown error'));
            }

            $this->newLine();
        }

        $this->info('✅ Test complete!');

        return Command::SUCCESS;
    }
}
