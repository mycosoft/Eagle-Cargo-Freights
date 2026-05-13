<?php

namespace App\Mail;

use App\Models\Shipment;
use App\Models\ShipmentStatusUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShipmentStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $shipment;

    public $statusUpdate;

    /**
     * Create a new message instance.
     */
    public function __construct(Shipment $shipment, ShipmentStatusUpdate $statusUpdate)
    {
        $this->shipment = $shipment;
        $this->statusUpdate = $statusUpdate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Shipment Status Update - '.$this->shipment->tracking_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.shipments.status-updated',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
