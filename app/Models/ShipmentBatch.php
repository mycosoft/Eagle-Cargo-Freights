<?php

namespace App\Models;

use App\Models\BatchExpense;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ShipmentBatch extends Model
{
    use HasFactory, Auditable;

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
        $typePrefix = strtoupper($cargoType).'-BATCH-';
        $prefix = $typePrefix.$date.'-';

        // Get the last batch of this type created today
        $lastBatch = self::where('batch_number', 'like', $prefix.'%')
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

        return $prefix.$uniqueId;
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
     * Get the expenses for this batch
     */
    public function expenses()
    {
        return $this->hasMany(BatchExpense::class, 'batch_id');
    }

    /**
     * Get the user who created this batch
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the total revenue for this batch (sum of all payments from invoices)
     */
    public function getRevenueAttribute()
    {
        return Payment::query()
            ->whereHas('invoice', function ($query) {
                $query->whereHas('shipment', function ($shipmentQuery) {
                    $shipmentQuery->where('batch_id', $this->id);
                });
            })
            ->sum('amount');
    }

    /**
     * Get total costs for this batch (sum of all batch expenses in UGX)
     */
    public function getTotalCostUgxAttribute()
    {
        $ugx = $this->expenses()->where('currency', 'UGX')->sum('amount');
        return $ugx;
    }

    /**
     * Get total costs for this batch (sum of all batch expenses in USD)
     */
    public function getTotalCostUsdAttribute()
    {
        $usd = $this->expenses()->where('currency', 'USD')->sum('amount');
        return $usd;
    }

    /**
     * Get net profit (Revenue in UGX - Costs in UGX)
     * Revenue is stored in shipment currency (mixed UGX/USD).
     * For simplicity, this shows revenue. If there are USD payments,
     * convert at a rate or show separately.
     */
    public function getTotalRevenueUgxAttribute()
    {
        return Payment::query()
            ->whereHas('invoice', function ($query) {
                $query->whereHas('shipment', function ($shipmentQuery) {
                    $shipmentQuery->where('batch_id', $this->id);
                });
            })
            ->whereHas('invoice.shipment', function ($q) {
                $q->where('currency', 'UGX');
            })
            ->sum('amount');
    }

    public function getTotalRevenueUsdAttribute()
    {
        return Payment::query()
            ->whereHas('invoice', function ($query) {
                $query->whereHas('shipment', function ($shipmentQuery) {
                    $shipmentQuery->where('batch_id', $this->id);
                });
            })
            ->whereHas('invoice.shipment', function ($q) {
                $q->where('currency', 'USD');
            })
            ->sum('amount');
    }

    /**
     * Get the total invoiced amount for this batch (sum of all invoice totals)
     */
    public function getInvoicedAmountAttribute()
    {
        return Invoice::query()
            ->whereHas('shipment', function ($query) {
                $query->where('batch_id', $this->id);
            })
            ->sum('total');
    }

    /**
     * Get the outstanding balance for this batch
     */
    public function getOutstandingAmountAttribute()
    {
        return $this->invoiced_amount - $this->revenue;
    }

    /**
     * Alias for Revenue (UGX + USD combined in UGX equivalent for display)
     */
    public function getTotalRevenueAttribute()
    {
        return $this->total_revenue_ugx + $this->total_revenue_usd;
    }

    /**
     * Alias for total costs (UGX + USD combined in UGX equivalent)
     */
    public function getTotalCostsAttribute()
    {
        return $this->total_cost_ugx + $this->total_cost_usd;
    }

    /**
     * Net profit in UGX (Revenue UGX - Costs UGX)
     */
    public function getProfitUgxAttribute()
    {
        return $this->total_revenue_ugx - $this->total_cost_ugx;
    }

/**
 * Get net profit in USD (Revenue USD - Costs USD)
 */
public function getProfitUsdAttribute()
{
    return $this->total_revenue_usd - $this->total_cost_usd;
}

/**
 * Get profit as a single figure (UGX + USD combined)
 */
public function getProfitAttribute()
{
    return $this->total_revenue - $this->total_costs;
}

/**
 * Get profit margin percentage
 */
public function getProfitMarginAttribute()
{
    if ($this->total_revenue <= 0) return 0;
    return round(($this->profit / $this->total_revenue) * 100, 1);
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
