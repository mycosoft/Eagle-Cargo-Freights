<?php

namespace App\Jobs;

use App\Models\Client;
use App\Notifications\WhatsAppMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDelayedWhatsAppJob implements ShouldQueue
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
     * The client instance.
     *
     * @var \App\Models\Client
     */
    protected $client;

    /**
     * The message subject.
     *
     * @var string
     */
    protected $subject;

    /**
     * The message content.
     *
     * @var string
     */
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct(Client $client, string $subject, string $message)
    {
        $this->client = $client;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * This job sends WhatsApp messages with proper delays to prevent account bans.
     */
    public function handle(): void
    {
        try {
            if (! $this->client->phone) {
                Log::warning("[Delayed WhatsApp] Client {$this->client->name} has no phone number");

                return;
            }

            Log::info("[Delayed WhatsApp] Sending message to {$this->client->phone} ({$this->client->name})");

            // Send WhatsApp notification
            $this->client->notify(new WhatsAppMessage($this->subject, $this->message));

            Log::info("[Delayed WhatsApp] Successfully sent to {$this->client->phone}");

        } catch (\Exception $e) {
            Log::error("[Delayed WhatsApp] Failed to send to {$this->client->phone}: ".$e->getMessage());

            // Rethrow to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("[Delayed WhatsApp] Job permanently failed for client {$this->client->name}: ".$exception->getMessage());
    }
}
