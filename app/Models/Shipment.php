<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'client_id',
        'receiver_id',
        'tracking_number',
        'origin',
        'destination',
        'weight',
        'shipment_type',
        'current_status',
        'description',
        'expected_delivery_date',
        'china_warehouse_date',
        // Delivery Range
        'delivery_time_min',
        'delivery_time_max',
        'delivery_time_unit',
        // Batch Information
        'batch_id',
        // Package Details
        'num_packages',
        'length',
        'width',
        'height',
        'cbm',
        'package_type',
        'fragile',
        'special_instructions',
        // Pricing & Billing
        'shipping_cost',
        'cost_price',
        'items',
        'insurance_value',
        'tax',
        'discount',
        'total_amount',
        'currency',
        'payment_method',
        'payment_status',
        // Sender Information
        'sender_name',
        'sender_phone',
        'sender_address',
        // Receiver Information
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        // Additional Details
        'service_type',
        'delivery_instructions',
        'reference_number',
        'special_notes',
        // Customs Information
        'is_international',
        'customs_value',
        'customs_description',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'fragile' => 'boolean',
        'is_international' => 'boolean',
        'shipping_cost' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'items' => 'array',
        'insurance_value' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'customs_value' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'cbm' => 'decimal:3',
    ];

    /**
     * Default attribute values
     */
    protected $attributes = [
        'currency' => 'USD',
    ];

    /**
     * Auto-generates tracking number if not provided.
     */

    /**
     * Get the client that owns the shipment
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the receiver client (if applicable)
     */
    public function receiver()
    {
        return $this->belongsTo(Client::class, 'receiver_id');
    }

    /**
     * Get the primary invoice for the shipment
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class)->latest();
    }

    /**
     * Get the invoices for the shipment
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the status updates for the shipment
     */
    public function statusUpdates()
    {
        return $this->hasMany(ShipmentStatusUpdate::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the batch that owns the shipment
     */
    public function batch()
    {
        return $this->belongsTo(ShipmentBatch::class, 'batch_id');
    }

    /**
     * Get the packages for the shipment
     */
    public function packages()
    {
        return $this->hasMany(ShipmentPackage::class)->orderBy('sort_order');
    }

    /**
     * Generate a unique tracking number
     */
    public static function generateTrackingNumber($prefix = 'EGL-')
    {
        do {
            $number = $prefix.mt_rand(10000000, 99999999);
        } while (self::where('tracking_number', $number)->exists());

        return $number;
    }

    /**
     * Get formatted delivery range (e.g., "5-7 days" or "2-3 months")
     */
    public function getDeliveryRangeAttribute()
    {
        if ($this->delivery_time_min && $this->delivery_time_max && $this->delivery_time_unit) {
            return $this->delivery_time_min.'-'.$this->delivery_time_max.' '.$this->delivery_time_unit;
        }

        return null;
    }

    /**
     * Get total costs for this shipment (cost_price from line items)
     */
    public function getTotalCostsAttribute()
    {
        return $this->cost_price ?? 0;
    }

    /**
     * Get revenue for this shipment (sum of payments)
     */
    public function getRevenueAttribute()
    {
        return Payment::whereHas('invoice', function ($q) {
            $q->where('shipment_id', $this->id);
        })->sum('amount');
    }

    /**
     * Get profit for this shipment
     */
    public function getProfitAttribute()
    {
        return $this->revenue - $this->total_costs;
    }

    /**
     * Get profit margin percentage
     */
    public function getProfitMarginAttribute()
    {
        if ($this->revenue <= 0) return 0;
        return round(($this->profit / $this->revenue) * 100, 1);
    }
}
