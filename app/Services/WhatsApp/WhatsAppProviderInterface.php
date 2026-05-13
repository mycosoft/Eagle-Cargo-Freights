<?php

namespace App\Services\WhatsApp;

interface WhatsAppProviderInterface
{
    /**
     * Send a text message via WhatsApp
     *
     * @param  string  $to  Phone number in international format (e.g., 256774222619)
     * @param  string  $message  Message content
     * @return array Response with 'success', 'message_id', 'error', and 'data' keys
     */
    public function sendMessage(string $to, string $message): array;

    /**
     * Send a template message (for marketing/notifications)
     * Note: Templates must be pre-approved by Meta for Meta provider
     *
     * @param  string  $to  Phone number
     * @param  string  $templateName  Template name
     * @param  array  $parameters  Template parameters
     * @return array Response with 'success', 'message_id', 'error', and 'data' keys
     */
    public function sendTemplate(string $to, string $templateName, array $parameters = []): array;

    /**
     * Check if the provider is available and configured
     *
     * @return bool True if provider is available
     */
    public function isAvailable(): bool;

    /**
     * Get the provider name
     *
     * @return string Provider name (e.g., 'meta', 'local')
     */
    public function getName(): string;
}
