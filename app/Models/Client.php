<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable, Auditable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
    ];

    /**
     * Get the shipments for the client.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Get all invoices for the client through shipments.
     */
    public function invoices()
    {
        return $this->hasManyThrough(
            \App\Models\Invoice::class,
            Shipment::class
        );
    }
}
