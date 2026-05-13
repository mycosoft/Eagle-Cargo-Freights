<?php

namespace App\Notifications\Channels;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Get the phone number from the notifiable entity
        $to = $notifiable->phone ?? $notifiable->routeNotificationFor('whatsapp');

        if (! $to) {
            return;
        }

        // Get the message from the notification
        $message = $notification->toWhatsApp($notifiable);

        // Send the message
        $this->whatsappService->sendMessage($to, $message);
    }
}
