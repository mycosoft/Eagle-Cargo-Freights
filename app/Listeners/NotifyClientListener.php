<?php

namespace App\Listeners;

use App\Events\ShipmentStatusUpdatedEvent;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class NotifyClientListener
{
    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(ShipmentStatusUpdatedEvent $event): void
    {
        Log::info("Processing notification for Tracking #{$event->shipment->tracking_number}");

        if ($event->shipment->client) {
            $event->shipment->client->notify(new \App\Notifications\ShipmentStatusChanged($event->shipment, $event->statusUpdate));
        }
    }
}
