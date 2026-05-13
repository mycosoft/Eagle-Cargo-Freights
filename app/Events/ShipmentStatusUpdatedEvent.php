<?php

namespace App\Events;

use App\Models\Shipment;
use App\Models\ShipmentStatusUpdate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentStatusUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $shipment;

    public $statusUpdate;

    /**
     * Create a new event instance.
     */
    public function __construct(Shipment $shipment, ShipmentStatusUpdate $statusUpdate)
    {
        $this->shipment = $shipment;
        $this->statusUpdate = $statusUpdate;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
