<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WasenderWhatsAppProvider implements WhatsAppProviderInterface
{
    protected $apiUrl;
    protected $apiKey;
    protected $sender;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.wasender.api_url', 'https://wasenderapi.com/api');
        $this->apiKey = config('services.whatsapp.wasender.api_key');
        $this->sender = config('services.whatsapp.wasender.sender');
    }

    /**
     * Send a text message via WasenderAPI
     */
    public function sendMessage(string $to, string $message): array
    {
        try {
            $to = $this->formatPhoneNumber($to);

            $response = Http::timeout(60)
                ->retry(3, 1000)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/send-message", [
                    'phone' => $to,
                    'message' => $message,
                ]);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info("[WasenderAPI] Message sent successfully to {$to}", [
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message_id' => $responseData['id'] ?? $responseData['message_id'] ?? $responseData['data']['id'] ?? null,
                    'provider' => 'wasender',
                    'data' => $responseData
                ];
            } else {
                Log::error("[WasenderAPI] Failed to send message to {$to}", [
                    'status' => $response->status(),
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'error' => $responseData['message'] ?? $responseData['error'] ?? 'Unknown error',
                    'provider' => 'wasender',
                    'data' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error("[WasenderAPI] Exception while sending message to {$to}: " . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'wasender'
            ];
        }
    }

    /**
     * Send a template message
     */
    public function sendTemplate(string $to, string $templateName, array $parameters = []): array
    {
        try {
            $to = $this->formatPhoneNumber($to);

            $response = Http::timeout(60)
                ->retry(3, 1000)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/send-template", [
                    'phone' => $to,
                    'template_name' => $templateName,
                    'parameters' => $parameters,
                ]);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info("[WasenderAPI] Template message sent to {$to}", [
                    'template' => $templateName,
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message_id' => $responseData['id'] ?? $responseData['message_id'] ?? null,
                    'provider' => 'wasender',
                    'data' => $responseData
                ];
            }

            Log::error("[WasenderAPI] Failed to send template to {$to}", [
                'template' => $templateName,
                'response' => $responseData
            ]);

            return [
                'success' => false,
                'error' => $responseData['message'] ?? $responseData['error'] ?? 'Unknown error',
                'provider' => 'wasender',
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error("[WasenderAPI] Exception while sending template to {$to}: " . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'wasender'
            ];
        }
    }

    /**
     * Send an image with caption
     */
    public function sendImage(string $to, string $imageUrl, string $caption = ''): array
    {
        try {
            $to = $this->formatPhoneNumber($to);

            $response = Http::timeout(60)
                ->retry(3, 1000)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/send-media", [
                    'phone' => $to,
                    'url' => $imageUrl,
                    'caption' => $caption,
                ]);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info("[WasenderAPI] Image sent successfully to {$to}");
                return [
                    'success' => true,
                    'message_id' => $responseData['id'] ?? $responseData['message_id'] ?? null,
                    'provider' => 'wasender',
                    'data' => $responseData
                ];
            }

            return [
                'success' => false,
                'error' => $responseData['message'] ?? $responseData['error'] ?? 'Unknown error',
                'provider' => 'wasender',
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error("[WasenderAPI] Exception while sending image to {$to}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'wasender'
            ];
        }
    }

    /**
     * Check if WasenderAPI is available
     */
    public function isAvailable(): bool
    {
        if (empty($this->apiUrl) || empty($this->apiKey)) {
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->get("{$this->apiUrl}/device-status");

            if ($response->successful()) {
                $data = $response->json();
                // Check if device is connected/active
                return isset($data['status']) && $data['status'] === 'connected';
            }

            return false;
        } catch (\Exception $e) {
            Log::debug("[WasenderAPI] API not available: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get provider name
     */
    public function getName(): string
    {
        return 'wasender';
    }

    /**
     * Get connection/device status
     */
    public function getConnectionStatus(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->get("{$this->apiUrl}/device-status");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'state' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get device status',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get QR code for connection (if supported)
     */
    public function getQrCode(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->get("{$this->apiUrl}/get-qr");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'qrcode' => $data['qrcode'] ?? $data['base64'] ?? null,
                    'code' => $data['code'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get QR code',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Logout/disconnect device
     */
    public function logout(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->post("{$this->apiUrl}/logout");

            return [
                'success' => $response->successful(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number for WasenderAPI (Uganda: 256)
     */
    protected function formatPhoneNumber(string $to): string
    {
        $to = preg_replace('/[^0-9+]/', '', $to);
        $to = ltrim($to, '+');
        
        // Already has Uganda country code (256)
        if (str_starts_with($to, '256')) {
            return $to;
        }
        
        // Starts with 0, remove 0 and add Uganda country code
        if (str_starts_with($to, '0')) {
            return '256' . substr($to, 1);
        }
        
        // No country code, add Uganda code
        return '256' . $to;
    }
}
