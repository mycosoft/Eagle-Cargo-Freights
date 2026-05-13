<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocalWhatsAppProvider implements WhatsAppProviderInterface
{
    protected $apiUrl;

    protected $webhookSecret;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.local.api_url', 'http://localhost:3000');
        $this->webhookSecret = config('services.whatsapp.local.webhook_secret');
    }

    /**
     * Send a text message via local go-whatsapp-web-multidevice API
     */
    public function sendMessage(string $to, string $message): array
    {
        try {
            // Remove any non-numeric characters except +
            $to = preg_replace('/[^0-9+]/', '', $to);

            // Remove leading + if present
            $to = ltrim($to, '+');

            $response = Http::timeout(60)
                ->retry(3, 1000)
                ->post("{$this->apiUrl}/send/message", [
                    'phone' => $to,
                    'message' => $message,
                ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['code']) && $responseData['code'] === 'SUCCESS') {
                Log::info("[Local WhatsApp] Message sent successfully to {$to}", [
                    'response' => $responseData,
                ]);

                return [
                    'success' => true,
                    'message_id' => $responseData['message_id'] ?? $responseData['id'] ?? null,
                    'provider' => 'local',
                    'data' => $responseData,
                ];
            } else {
                Log::error("[Local WhatsApp] Failed to send message to {$to}", [
                    'status' => $response->status(),
                    'response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'error' => $responseData['message'] ?? $responseData['error'] ?? 'Unknown error',
                    'provider' => 'local',
                    'data' => $responseData,
                ];
            }
        } catch (\Exception $e) {
            Log::error("[Local WhatsApp] Exception while sending message to {$to}: ".$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'local',
            ];
        }
    }

    /**
     * Send a template message
     * Note: Local API doesn't support templates, so we send as regular message
     */
    public function sendTemplate(string $to, string $templateName, array $parameters = []): array
    {
        // Local API doesn't support templates, so we'll send as a regular message
        // You can customize this to format the template as a text message
        $message = "Template: {$templateName}";

        if (! empty($parameters)) {
            $message .= "\n".json_encode($parameters, JSON_PRETTY_PRINT);
        }

        Log::warning('[Local WhatsApp] Templates not supported, sending as text message', [
            'template' => $templateName,
            'to' => $to,
        ]);

        return $this->sendMessage($to, $message);
    }

    /**
     * Check if local WhatsApp API is available
     */
    public function isAvailable(): bool
    {
        if (empty($this->apiUrl)) {
            return false;
        }

        try {
            // Try to ping the API to check if it's running
            $response = Http::timeout(5)->get("{$this->apiUrl}/devices");

            return $response->successful();
        } catch (\Exception $e) {
            Log::debug('[Local WhatsApp] API not available: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Get provider name
     */
    public function getName(): string
    {
        return 'local';
    }

    /**
     * Get connected devices
     */
    public function getDevices(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/devices");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get devices',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send an image
     */
    public function sendImage(string $to, string $imageUrl, string $caption = ''): array
    {
        try {
            $to = preg_replace('/[^0-9+]/', '', $to);
            $to = ltrim($to, '+');

            $response = Http::timeout(60)
                ->retry(3, 1000)
                ->post("{$this->apiUrl}/send/image", [
                    'phone' => $to,
                    'image' => $imageUrl,
                    'caption' => $caption,
                ]);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info("[Local WhatsApp] Image sent successfully to {$to}");

                return [
                    'success' => true,
                    'message_id' => $responseData['message_id'] ?? $responseData['id'] ?? null,
                    'provider' => 'local',
                    'data' => $responseData,
                ];
            }

            return [
                'success' => false,
                'error' => $responseData['message'] ?? 'Unknown error',
                'provider' => 'local',
                'data' => $responseData,
            ];
        } catch (\Exception $e) {
            Log::error("[Local WhatsApp] Exception while sending image to {$to}: ".$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'local',
            ];
        }
    }
}
