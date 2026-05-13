<?php

namespace App\Services;

use App\Mail\ShipmentStatusUpdatedMail;
use App\Models\Shipment;
use App\Models\ShipmentStatusUpdate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send notification for shipment status update.
     */
    public function sendStatusUpdateNotification(Shipment $shipment, ShipmentStatusUpdate $statusUpdate)
    {
        // 1. Send Email
        if ($shipment->client && $shipment->client->email) {
            try {
                Mail::to($shipment->client->email)->send(new ShipmentStatusUpdatedMail($shipment, $statusUpdate));
                Log::info("Email sent to {$shipment->client->email} for shipment {$shipment->tracking_number}");
            } catch (\Exception $e) {
                Log::error('Failed to send email: '.$e->getMessage());
            }
        }

        // 2. Send SMS (Placeholder)
        if ($shipment->client && $shipment->client->phone) {
            $this->sendSMS($shipment->client->phone, "Shipment {$shipment->tracking_number} updated to: {$statusUpdate->status}");
        }

        // 3. Send WhatsApp (Placeholder)
        // $this->sendWhatsApp(...);
    }

    /**
     * Placeholder for SMS sending logic
     */
    private function sendSMS($phone, $message)
    {
        // Integration with SMS gateway (e.g., Twilio, Africa's Talking) would go here
        Log::info("SMS would be sent to {$phone}: {$message}");
    }
}
