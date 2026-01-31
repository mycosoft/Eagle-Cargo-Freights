<?php

namespace App\Jobs;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBulkNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subject;
    protected $message;
    protected $channel;
    protected $clientIds;

    /**
     * Create a new job instance.
     */
    public function __construct($subject, $message, $channel, $clientIds = null)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->channel = $channel;
        $this->clientIds = $clientIds;
    }

    /**
     * Execute the job with rate limiting to prevent WhatsApp bans.
     */
    public function handle(): void
    {
        $query = Client::query();
        
        // Filter by specific clients if provided
        if ($this->clientIds) {
            $query->whereIn('id', $this->clientIds);
        }
        
        $clients = $query->get();

        // Rate limiting: Add delay between each message
        $delaySeconds = 0;

        foreach ($clients as $client) {
            if ($this->channel === 'email' && $client->email) {
                try {
                    // In a real app, you'd create a Mailable class for this
                    // For now, we'll log it to simulate sending
                    Log::info("Sending Bulk Email to {$client->email}: Subject: {$this->subject}");
                    
                    // Mail::raw($this->message, function ($msg) use ($client) {
                    //     $msg->to($client->email)->subject($this->subject);
                    // });

                } catch (\Exception $e) {
                    Log::error("Failed to send bulk email to {$client->email}: " . $e->getMessage());
                }
            } elseif ($this->channel === 'sms' && $client->phone) {
                // Placeholder for SMS logic
                Log::info("Sending Bulk SMS to {$client->phone}: {$this->message}");
            } elseif ($this->channel === 'whatsapp' && $client->phone) {
                try {
                    // Dispatch WhatsApp notification with delay to prevent ban
                    \App\Jobs\SendDelayedWhatsAppJob::dispatch($client, $this->subject, $this->message)
                        ->delay(now()->addSeconds($delaySeconds));
                    
                    Log::info("Scheduled Bulk WhatsApp to {$client->phone} with {$delaySeconds}s delay");
                    
                    // Increment delay by 3-5 seconds per message (randomized)
                    $delaySeconds += rand(3, 5);
                    
                } catch (\Exception $e) {
                    Log::error("Failed to schedule bulk WhatsApp to {$client->phone}: " . $e->getMessage());
                }
            }
        }
        
        Log::info("Bulk notification completed. Total clients: " . $clients->count() . ", Channel: {$this->channel}");
    }
}
