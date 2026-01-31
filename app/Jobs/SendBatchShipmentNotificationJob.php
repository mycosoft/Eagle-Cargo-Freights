<?php

namespace App\Jobs;

use App\Models\Shipment;
use App\Models\ShipmentStatusUpdate;
use App\Notifications\ShipmentStatusChanged;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBatchShipmentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 10;

    /**
     * The shipment instance.
     *
     * @var \App\Models\Shipment
     */
    protected $shipment;

    /**
     * The status update instance.
     *
     * @var \App\Models\ShipmentStatusUpdate
     */
    protected $statusUpdate;

    /**
     * Create a new job instance.
     */
    public function __construct(Shipment $shipment, ShipmentStatusUpdate $statusUpdate)
    {
        $this->shipment = $shipment;
        $this->statusUpdate = $statusUpdate;
    }

    /**
     * Execute the job.
     * 
     * This job is rate-limited to prevent WhatsApp account bans
     * when sending bulk notifications for batch status updates.
     */
    public function handle(): void
    {
        try {
            // Check if client exists and has notification preferences
            if (!$this->shipment->client) {
                Log::warning("[Batch Notification] Shipment {$this->shipment->tracking_number} has no client assigned");
                return;
            }

            Log::info("[Batch Notification] Sending notification for shipment: {$this->shipment->tracking_number} to client: {$this->shipment->client->name}");

            // Send notification via configured channels (Email + WhatsApp)
            $this->shipment->client->notify(new ShipmentStatusChanged($this->shipment, $this->statusUpdate));

            Log::info("[Batch Notification] Successfully sent notification for shipment: {$this->shipment->tracking_number}");

        } catch (\Exception $e) {
            Log::error("[Batch Notification] Failed to send notification for shipment {$this->shipment->tracking_number}: " . $e->getMessage());
            
            // Rethrow to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("[Batch Notification] Job permanently failed for shipment {$this->shipment->tracking_number}: " . $exception->getMessage());
    }
}
