<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Notifications\Channels\WhatsAppChannel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReceiptSent extends Notification implements ShouldQueue
{
    use Queueable;

    public $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function via($notifiable)
    {
        $channels = [];

        if (\App\Models\Setting::get('notify_status_change_email', 1) && $notifiable->email) {
            $channels[] = 'mail';
        }

        if (\App\Models\Setting::get('notify_status_change_whatsapp', 1) && $notifiable->phone) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $companySettings = [
            'name' => 'Eagle Cargo Freights',
            'address' => 'P.O.Box 75529, Kampala',
            'phone' => '+256 200 991 118',
            'whatsapp' => '0777151635, +256 701 579417',
            'china' => '+86 130 7021 8275',
            'email' => 'eaglecargofreights@gmail.com',
            'website' => 'www.eaglecargofreights.com',
            'logo' => 'images/logo.jpeg',
        ];

        // Generate PDF
        $pdf = Pdf::loadView('payments.receipt-pdf', [
            'payment' => $this->payment,
            'invoice' => $this->payment->invoice,
            'shipment' => $this->payment->invoice->shipment,
            'companySettings' => $companySettings,
        ]);

        // Generate PNG image using Browsershot
        $imagePath = null;
        try {
            // Ensure temp directory exists
            if (! file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $imagePath = storage_path('app/temp/receipt-'.$this->payment->receipt_number.'.png');

            // Generate HTML for image
            $receiptHtml = view('payments.receipt-image', [
                'payment' => $this->payment,
                'invoice' => $this->payment->invoice,
                'shipment' => $this->payment->invoice->shipment,
                'companySettings' => $companySettings,
            ])->render();

            // Use Browsershot to convert HTML to PNG
            \Spatie\Browsershot\Browsershot::html($receiptHtml)
                ->windowSize(800, 1200)
                ->setScreenshotType('png')
                ->save($imagePath);

        } catch (\Exception $e) {
            // If image generation fails, log and continue without image
            \Log::warning('Failed to generate receipt image: '.$e->getMessage());
            $imagePath = null;
        }

        $currency = $this->payment->invoice->shipment->currency ?? \App\Models\Setting::getCurrencySymbol();

        $mail = (new MailMessage)
            ->subject('Payment Receipt - '.$this->payment->receipt_number)
            ->greeting('Dear '.$notifiable->name.',')
            ->line('Thank you for your payment.')
            ->line('Receipt Number: '.$this->payment->receipt_number)
            ->line('Amount Paid: '.$currency.' '.number_format($this->payment->amount, 2))
            ->line('Payment Date: '.$this->payment->payment_date->format('F d, Y'))
            ->line('Payment Method: '.ucwords(str_replace('_', ' ', $this->payment->payment_method)))
            ->attachData($pdf->output(), 'receipt-'.$this->payment->receipt_number.'.pdf', [
                'mime' => 'application/pdf',
            ]);

        // Attach PNG image if generated successfully
        if ($imagePath && file_exists($imagePath)) {
            $mail->attach($imagePath, [
                'as' => 'receipt-'.$this->payment->receipt_number.'.png',
                'mime' => 'image/png',
            ]);
        }

        return $mail
            ->line('Please keep this receipt for your records.')
            ->line('Thank you for your business!');
    }

    public function toWhatsApp($notifiable)
    {
        $currency = $this->payment->invoice->shipment->currency ?? \App\Models\Setting::getCurrencySymbol();

        $message = "🧾 *Payment Receipt*\n\n";
        $message .= "Dear {$notifiable->name},\n\n";
        $message .= "Thank you for your payment.\n\n";
        $message .= "📋 *Receipt Details:*\n";
        $message .= "Receipt No: {$this->payment->receipt_number}\n";
        $message .= "Amount: {$currency} ".number_format($this->payment->amount, 2)."\n";
        $message .= 'Date: '.$this->payment->payment_date->format('F d, Y')."\n";
        $message .= 'Method: '.ucwords(str_replace('_', ' ', $this->payment->payment_method))."\n\n";
        $message .= "Invoice: {$this->payment->invoice->invoice_number}\n";
        $message .= "Balance: {$currency} ".number_format($this->payment->invoice->balance, 2)."\n\n";
        $message .= "Thank you for your business!\n";
        $message .= '📞 Support: UG +256 701 57941';

        return $message;
    }
}
