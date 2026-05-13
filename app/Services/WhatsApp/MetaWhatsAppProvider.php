<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaWhatsAppProvider implements WhatsAppProviderInterface
{
    protected $accessToken;

    protected $phoneNumberId;

    protected $apiVersion;

    protected $baseUrl;

    public function __construct()
    {
        $this->accessToken = config('services.whatsapp.meta.access_token');
        $this->phoneNumberId = config('services.whatsapp.meta.phone_number_id');
        $this->apiVersion = config('services.whatsapp.meta.api_version', 'v21.0');
        $this->baseUrl = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}";
    }

    /**
     * Send a text message via WhatsApp Cloud API
     */
    public function sendMessage(string $to, string $message): array
    {
        try {
            // Remove any non-numeric characters except +
            $to = preg_replace('/[^0-9+]/', '', $to);

            // Remove leading + if present
            $to = ltrim($to, '+');

            $response = Http::withToken($this->accessToken)
                ->timeout(60) // Increase timeout to 60 seconds
                ->retry(3, 1000) // Retry 3 times with 1s delay
                ->post("{$this->baseUrl}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ]);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info("[Meta WhatsApp] Message sent successfully to {$to}", [
                    'message_id' => $responseData['messages'][0]['id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'message_id' => $responseData['messages'][0]['id'] ?? null,
                    'provider' => 'meta',
                    'data' => $responseData,
                ];
            } else {
                Log::error("[Meta WhatsApp] Failed to send message to {$to}", [
                    'status' => $response->status(),
                    'response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'error' => $responseData['error']['message'] ?? 'Unknown error',
                    'provider' => 'meta',
                    'data' => $responseData,
                ];
            }
        } catch (\Exception $e) {
            Log::error("[Meta WhatsApp] Exception while sending message to {$to}: ".$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'meta',
            ];
        }
    }

    /**
     * Send a template message (for marketing/notifications)
     */
    public function sendTemplate(string $to, string $templateName, array $parameters = []): array
    {
        try {
            $to = preg_replace('/[^0-9+]/', '', $to);
            $to = ltrim($to, '+');

            $response = Http::withToken($this->accessToken)
                ->timeout(60)
                ->retry(3, 1000)
                ->post("{$this->baseUrl}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => 'en_US',
                        ],
                        'components' => $parameters,
                    ],
                ]);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info("[Meta WhatsApp] Template sent successfully to {$to}");

                return [
                    'success' => true,
                    'message_id' => $responseData['messages'][0]['id'] ?? null,
                    'provider' => 'meta',
                    'data' => $responseData,
                ];
            } else {
                Log::error("[Meta WhatsApp] Failed to send template to {$to}", [
                    'response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'error' => $responseData['error']['message'] ?? 'Unknown error',
                    'provider' => 'meta',
                    'data' => $responseData,
                ];
            }
        } catch (\Exception $e) {
            Log::error("[Meta WhatsApp] Exception while sending template to {$to}: ".$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'meta',
            ];
        }
    }

    /**
     * Check if Meta WhatsApp Cloud API is available
     */
    public function isAvailable(): bool
    {
        return ! empty($this->accessToken) && ! empty($this->phoneNumberId);
    }

    /**
     * Get provider name
     */
    public function getName(): string
    {
        return 'meta';
    }
}
