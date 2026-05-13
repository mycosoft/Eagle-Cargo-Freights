<?php

namespace App\Notifications;

use App\Models\Shipment;
use App\Models\ShipmentStatusUpdate;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $shipment;

    protected $statusUpdate;

    /**
     * Create a new notification instance.
     */
    public function __construct(Shipment $shipment, ShipmentStatusUpdate $statusUpdate)
    {
        $this->shipment = $shipment;
        $this->statusUpdate = $statusUpdate;
    }

    /**
     * Get the notification's delivery channels based on settings.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        // Get notification settings using Setting model
        // Default to true for email and whatsapp if not set
        $notifyEmail = \App\Models\Setting::get('notify_status_change_email', 1);
        $notifyWhatsapp = \App\Models\Setting::get('notify_status_change_whatsapp', 1);
        $notifySms = \App\Models\Setting::get('notify_status_change_sms', 0);

        if ($notifyEmail && $notifiable->email) {
            $channels[] = 'mail';
        }

        if ($notifyWhatsapp && $notifiable->phone) {
            $channels[] = WhatsAppChannel::class;
        }

        // SMS can be added later
        // if ($notifySms && $notifiable->phone) {
        //     $channels[] = 'sms';
        // }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Shipment Status Update - '.$this->shipment->tracking_number)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your shipment status has been updated.')
            ->line('**Tracking Number:** '.$this->shipment->tracking_number)
            ->line('**New Status:** '.$this->statusUpdate->status)
            ->line('**Location:** '.$this->statusUpdate->location)
            ->line('**Remarks:** '.($this->statusUpdate->remarks ?? 'N/A'))
            ->action('Track Shipment', url('/track?tracking_number='.$this->shipment->tracking_number))
            ->line('Thank you for using Bryanz Logistics!');
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        $message = "*Shipment Status Update*\n\n";
        $message .= "Hello {$notifiable->name},\n\n";
        $message .= "Your shipment has been updated:\n\n";
        $message .= "📦 *Tracking:* {$this->shipment->tracking_number}\n";
        $message .= "📍 *Status:* {$this->statusUpdate->status}\n";
        $message .= "🌍 *Location:* {$this->statusUpdate->location}\n";

        if ($this->statusUpdate->remarks) {
            $message .= "💬 *Remarks:* {$this->statusUpdate->remarks}\n";
        }

        $message .= "\nTrack your shipment: ".url('/track?tracking_number='.$this->shipment->tracking_number);
        $message .= "\n\n_Bryanz Logistics_";

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'shipment_id' => $this->shipment->id,
            'tracking_number' => $this->shipment->tracking_number,
            'status' => $this->statusUpdate->status,
            'location' => $this->statusUpdate->location,
        ];
    }
}
