<?php

namespace App\Services;

use App\Services\WhatsApp\WhatsAppProviderInterface;
use App\Services\WhatsApp\MetaWhatsAppProvider;
use App\Services\WhatsApp\LocalWhatsAppProvider;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $provider;
    protected $providerType;

    public function __construct()
    {
        $this->providerType = config('services.whatsapp.provider', 'meta');
        
        // Initialize primary provider
        $this->provider = $this->createProvider($this->providerType);
    }

    /**
     * Create a provider instance based on type
     *
     * @param string $type Provider type: 'meta', 'local'
     * @return WhatsAppProviderInterface
     */
    protected function createProvider(string $type): WhatsAppProviderInterface
    {
        switch ($type) {
            case 'local':
                return new LocalWhatsAppProvider();
            case 'meta':
            default:
                return new MetaWhatsAppProvider();
        }
    }

    /**
     * Send a text message via WhatsApp
     *
     * @param string $to Phone number in international format (e.g., 256774222619)
     * @param string $message Message content
     * @return array Response from API
     */
    public function sendMessage(string $to, string $message): array
    {
        return $this->provider->sendMessage($to, $message);
    }

    /**
     * Send a template message (for marketing/notifications)
     * Note: Templates must be pre-approved by Meta
     *
     * @param string $to Phone number
     * @param string $templateName Template name
     * @param array $parameters Template parameters
     * @return array Response from API
     */
    public function sendTemplate(string $to, string $templateName, array $parameters = []): array
    {
        return $this->provider->sendTemplate($to, $templateName, $parameters);
    }

    /**
     * Check if WhatsApp service is available
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->provider->isAvailable();
    }

    /**
     * Get the current provider name
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->provider->getName();
    }

    /**
     * Get the current provider instance
     *
     * @return WhatsAppProviderInterface
     */
    public function getProvider(): WhatsAppProviderInterface
    {
        return $this->provider;
    }

    /**
     * Check provider status and get info
     *
     * @return array
     */
    public function getStatus(): array
    {
        return [
            'provider' => $this->providerType,
            'name' => $this->provider->getName(),
            'available' => $this->provider->isAvailable(),
        ];
    }
}
