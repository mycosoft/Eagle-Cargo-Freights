<?php

namespace App\Services;

use App\Services\WhatsApp\WhatsAppProviderInterface;
use App\Services\WhatsApp\MetaWhatsAppProvider;
use App\Services\WhatsApp\LocalWhatsAppProvider;
use App\Services\WhatsApp\EvolutionWhatsAppProvider;
use App\Services\WhatsApp\WasenderWhatsAppProvider;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $provider;
    protected $fallbackProvider;
    protected $providerType;

    public function __construct()
    {
        $this->providerType = config('services.whatsapp.provider', 'evolution');
        
        // Initialize primary provider
        $this->provider = $this->createProvider($this->providerType);
        
        // Initialize fallback if using 'auto' mode
        if ($this->providerType === 'auto') {
            // Auto mode: try WasenderAPI first (most reliable), fallback to Evolution, then Meta
            $this->provider = $this->createProvider('wasender');
            $this->fallbackProvider = $this->createProvider('evolution');
            
            Log::info('[WhatsApp] Auto mode enabled: WasenderAPI primary, Evolution fallback');
        }
    }

    /**
     * Create a provider instance based on type
     *
     * @param string $type Provider type: 'meta', 'local', 'evolution', 'wasender'
     * @return WhatsAppProviderInterface
     */
    protected function createProvider(string $type): WhatsAppProviderInterface
    {
        switch ($type) {
            case 'local':
                return new LocalWhatsAppProvider();
            case 'evolution':
                return new EvolutionWhatsAppProvider();
            case 'wasender':
                return new WasenderWhatsAppProvider();
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
        // Try primary provider
        $result = $this->provider->sendMessage($to, $message);
        
        // Fallback if needed and available
        if (!$result['success'] && $this->fallbackProvider && $this->fallbackProvider->isAvailable()) {
            Log::warning("[WhatsApp] Primary provider ({$this->provider->getName()}) failed, trying fallback ({$this->fallbackProvider->getName()})");
            $result = $this->fallbackProvider->sendMessage($to, $message);
        }
        
        return $result;
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
        // Try primary provider
        $result = $this->provider->sendTemplate($to, $templateName, $parameters);
        
        // Fallback if needed and available
        if (!$result['success'] && $this->fallbackProvider && $this->fallbackProvider->isAvailable()) {
            Log::warning("[WhatsApp] Primary provider ({$this->provider->getName()}) failed, trying fallback ({$this->fallbackProvider->getName()})");
            $result = $this->fallbackProvider->sendTemplate($to, $templateName, $parameters);
        }
        
        return $result;
    }

    /**
     * Check if WhatsApp service is available
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        $available = $this->provider->isAvailable();
        
        if (!$available && $this->fallbackProvider) {
            $available = $this->fallbackProvider->isAvailable();
        }
        
        return $available;
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
        $status = [
            'provider' => $this->providerType,
            'primary' => [
                'name' => $this->provider->getName(),
                'available' => $this->provider->isAvailable(),
            ],
        ];

        if ($this->fallbackProvider) {
            $status['fallback'] = [
                'name' => $this->fallbackProvider->getName(),
                'available' => $this->fallbackProvider->isAvailable(),
            ];
        }

        return $status;
    }
}
