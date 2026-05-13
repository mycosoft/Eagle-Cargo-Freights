<?php

namespace App\Http\Controllers;

use App\Events\ShipmentStatusUpdatedEvent;
use App\Models\Shipment;
use App\Models\ShipmentStatusUpdate;
use Illuminate\Http\Request;

class ShipmentStatusUpdateController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_id' => 'required|exists:shipments,id',
            'status' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'storage_fee' => 'nullable|numeric|min:0',
        ]);

        // Prevent Picked Up status if invoice is not fully paid or lacks a Storage Fee
        if (strtolower(trim($validated['status'])) === 'picked up') {
            $shipmentCheck = Shipment::with(['invoices.items', 'invoices.payments'])->findOrFail($validated['shipment_id']);
            $invoice = $shipmentCheck->invoices->first();

            if ($invoice) {
                // If the frontend passed a storage fee, inject it now
                if ($request->filled('storage_fee')) {
                    $fee = floatval($request->storage_fee);

                    $hasStorageFee = false;
                    foreach ($invoice->items as $item) {
                        if (strtolower(trim($item->description)) === 'storage fee') {
                            $hasStorageFee = true;
                            break;
                        }
                    }

                    if (! $hasStorageFee) {
                        $invoice->items()->create([
                            'description' => 'Storage Fee',
                            'quantity' => 1,
                            'rate' => $fee,
                            'amount' => $fee,
                            'order' => $invoice->items->count(),
                        ]);

                        $invoice->subtotal = $invoice->items()->sum('amount');
                        $invoice->total = $invoice->subtotal + $invoice->tax - $invoice->discount;
                        $invoice->save();

                        // Automatically mark as paid (collected on pickup)
                        if ($fee > 0) {
                            $invoice->payments()->create([
                                'amount' => $fee,
                                'payment_date' => now(),
                                'payment_method' => 'Cash',
                                'reference_number' => 'Storage Fee Collection',
                                'notes' => 'Collected on Pickup',
                                'created_by' => auth()->id() ?? 1,
                            ]);
                        }

                        $invoice->updateStatus();
                        // Refresh to get new balance
                        $invoice = $invoice->fresh(['items', 'payments']);
                    }
                }

                // Final validation check to ensure everything is perfect
                $hasStorageFee = false;
                if ($invoice->items) {
                    foreach ($invoice->items as $item) {
                        if (strtolower(trim($item->description)) === 'storage fee') {
                            $hasStorageFee = true;
                            break;
                        }
                    }
                }

                if (! $hasStorageFee) {
                    return redirect()->back()
                        ->with('error', 'Cannot change status to "Picked Up". A "Storage Fee" must be added to the invoice. If there is no fee, please add it with an amount of 0.');
                }

                if ($invoice->balance > 0) {
                    return redirect()->back()
                        ->with('error', 'Cannot change status to "Picked Up". The invoice has an outstanding balance. Please ensure the invoice is fully paid first.');
                }
            }
        }

        // Create the status update
        $statusUpdate = ShipmentStatusUpdate::create($validated);

        // Update the shipment's current status
        $shipment = Shipment::findOrFail($validated['shipment_id']);
        $shipment->update(['current_status' => $validated['status']]);

        // Load the client relationship
        $shipment->load('client');

        // Dispatch the event for notifications
        event(new ShipmentStatusUpdatedEvent($shipment, $statusUpdate));

        return redirect()->route('admin.shipments.show', $shipment)
            ->with('success', 'Status update added successfully.');
    }
}
