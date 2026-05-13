<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentPackage extends Model
{
    protected $fillable = [
        'shipment_id',
        'description',
        'quantity',
        'length',
        'width',
        'height',
        'weight',
        'package_type',
        'fragile',
        'special_instructions',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'fragile' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
