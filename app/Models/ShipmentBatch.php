<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShipmentBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_number',
        'cargo_type',
        'name',
        'description',
        'current_status',
        'created_by',
    ];

    /**
     * Boot the model and generate batch number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batch) {
            $cargoType = $batch->cargo_type ?? 'air';
            
            // Auto-generate batch number if not provided
            if (empty($batch->batch_number)) {
                $batch->batch_number = self::generateBatchNumber($cargoType);
            }
            
            // Auto-generate batch name if not provided
            if (empty($batch->name)) {
                $batch->name = self::generateBatchName($cargoType);
            }
        });
    }

    /**
     * Generate a unique batch number based on cargo type
     * Format: AIR-BATCH-YYYYMMDD-XXXXXX or SEA-BATCH-YYYYMMDD-XXXXXX
     */
    public static function generateBatchNumber($cargoType = 'air')
    {
        $date = date('Ymd');
        $typePrefix = strtoupper($cargoType) . '-BATCH-';
        $prefix = $typePrefix . $date . '-';
        
        // Get the last batch of this type created today
        $lastBatch = self::where('batch_number', 'like', $prefix . '%')
            ->where('cargo_type', $cargoType)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBatch) {
            // Extract the unique ID from the last batch number
            $lastNumber = (int) substr($lastBatch->batch_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Pad with zeros to make it 6 digits
        $uniqueId = str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        return $prefix . $uniqueId;
    }

    /**
     * Generate a descriptive batch name based on cargo type and date
     * Format: "Air Cargo Batch - December 5, 2025" or "Sea Cargo Batch - December 5, 2025"
     */
    public static function generateBatchName($cargoType = 'air')
    {
        $cargoTypeLabel = $cargoType === 'sea' ? 'Sea Cargo' : 'Air Cargo';
        $date = date('F j, Y'); // e.g., "December 5, 2025"
        
        return "{$cargoTypeLabel} Batch - {$date}";
    }

    /**
     * Get the shipments in this batch
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'batch_id');
    }

    /**
     * Get the user who created this batch
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Update batch status and cascade to all shipments
     * WITH RATE LIMITING to prevent WhatsApp bans
     */
    public function updateBatchStatus($status, $location, $notes = null)
    {
        // Update batch status
        $this->update(['current_status' => $status]);

        // Update all shipments in this batch
        $this->shipments()->update(['current_status' => $status]);

        // Create status update records for each shipment with RATE-LIMITED notifications
        $delaySeconds = 0;
        
        foreach ($this->shipments as $shipment) {
            $statusUpdate = $shipment->statusUpdates()->create([
                'status' => $status,
                'location' => $location,
                'remarks' => $notes,
            ]);

            // Load the client relationship
            $shipment->load('client');

            // Dispatch notification job with incremental delay
            // This prevents rapid-fire messages that trigger WhatsApp spam detection
            \App\Jobs\SendBatchShipmentNotificationJob::dispatch($shipment, $statusUpdate)
                ->delay(now()->addSeconds($delaySeconds));
            
            // Increment delay by 3-5 seconds per message (randomized to appear natural)
            // WhatsApp allows ~20 messages per minute safely
            $delaySeconds += rand(3, 5);
        }

        return true;
    }
}
