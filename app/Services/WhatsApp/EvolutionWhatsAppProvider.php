<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionWhatsAppProvider implements WhatsAppProviderInterface
{
    protected $apiUrl;
    protected $apiKey;
    protected $instanceName;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.evolution.api_url', 'http://147.93.85.45:8080');
        $this->apiKey = config('services.whatsapp.evolution.api_key', '429683C4C977415CAAFCCE10F7D57E11');
        $this->instanceName = config('services.whatsapp.evolution.instance_name', 'Mycosoft Technologies');
    }

    /**
     * Get the URL-encoded instance name for API calls
     * Uses rawurlencode to properly encode spaces as %20 instead of +
     */
    protected function getEncodedInstanceName(): string
    {
        return str_replace('+', '%20', urlencode($this->instanceName));
    }

    /**
     * Send a text message via Evolution API
     */
    public function sendMessage(string $to, string $message): array
    {
        try {
            $to = $this->formatPhoneNumber($to);

            $response = Http::timeout(60)
                ->retry(3, 1000)
                ->withHeaders([
                    'apikey' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/message/sendText/{$this->getEncodedInstanceName()}", [
                    'number' => $to,
                    'text' => $message,
                ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['key'])) {
                Log::info("[Evolution WhatsApp] Message sent successfully to {$to}", [
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message_id' => $responseData['key']['id'] ?? null,
                    'provider' => 'evolution',
                    'data' => $responseData
                ];
            } else {
                Log::error("[Evolution WhatsApp] Failed to send message to {$to}", [
                    'status' => $response->status(),
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'error' => $responseData['message'] ?? $responseData['error'] ?? 'Unknown error',
                    'provider' => 'evolution',
                    'data' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error("[Evolution WhatsApp] Exception while sending message to {$to}: " . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'evolution'
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
                    'apikey' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/message/sendTemplate/{$this->getEncodedInstanceName()}", [
                    'number' => $to,
                    'templateName' => $templateName,
                    'parameters' => $parameters,
                ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['key'])) {
                Log::info("[Evolution WhatsApp] Template message sent to {$to}", [
                    'template' => $templateName,
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message_id' => $responseData['key']['id'] ?? null,
                    'provider' => 'evolution',
                    'data' => $responseData
                ];
            }

            Log::error("[Evolution WhatsApp] Failed to send template to {$to}", [
                'template' => $templateName,
                'response' => $responseData
            ]);

            return [
                'success' => false,
                'error' => $responseData['message'] ?? $responseData['error'] ?? 'Unknown error',
                'provider' => 'evolution',
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error("[Evolution WhatsApp] Exception while sending template to {$to}: " . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'evolution'
            ];
        }
    }

    /**
     * Send an image
     */
    public function sendImage(string $to, string $imageUrl, string $caption = ''): array
    {
        try {
            $to = $this->formatPhoneNumber($to);

            $response = Http::timeout(60)
                ->retry(3, 1000)
                ->withHeaders([
                    'apikey' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/message/sendMedia/{$this->getEncodedInstanceName()}", [
                    'number' => $to,
                    'media' => $imageUrl,
                    'fileName' => basename($imageUrl),
                    'caption' => $caption,
                ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['key'])) {
                Log::info("[Evolution WhatsApp] Image sent successfully to {$to}");
                return [
                    'success' => true,
                    'message_id' => $responseData['key']['id'] ?? null,
                    'provider' => 'evolution',
                    'data' => $responseData
                ];
            }

            return [
                'success' => false,
                'error' => $responseData['message'] ?? 'Unknown error',
                'provider' => 'evolution',
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error("[Evolution WhatsApp] Exception while sending image to {$to}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'evolution'
            ];
        }
    }

    /**
     * Check if Evolution API is available
     */
    public function isAvailable(): bool
    {
        if (empty($this->apiUrl) || empty($this->apiKey)) {
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'apikey' => $this->apiKey,
                ])
                ->get("{$this->apiUrl}/instance/connectionState/{$this->getEncodedInstanceName()}");

            if ($response->successful()) {
                $data = $response->json();
                $state = $data['instance']['state'] ?? '';
                // Accept 'open' or 'connected' as valid connected states
                return in_array($state, ['open', 'connected']);
            }

            return false;
        } catch (\Exception $e) {
            Log::debug("[Evolution WhatsApp] API not available: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get provider name
     */
    public function getName(): string
    {
        return 'evolution';
    }

    /**
     * Get connection status
     */
    public function getConnectionStatus(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'apikey' => $this->apiKey,
                ])
                ->get("{$this->apiUrl}/instance/connectionState/{$this->getEncodedInstanceName()}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'state' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get connection state',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get QR code for connection
     */
    public function getQrCode(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'apikey' => $this->apiKey,
                ])
                ->get("{$this->apiUrl}/instance/connect/{$this->getEncodedInstanceName()}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'qrcode' => $data['base64'] ?? null,
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
     * Logout instance
     */
    public function logout(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'apikey' => $this->apiKey,
                ])
                ->delete("{$this->apiUrl}/instance/logout/{$this->getEncodedInstanceName()}");

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
     * Format phone number for Evolution API (Uganda: 256)
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
