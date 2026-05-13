<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$batch = \App\Models\ShipmentBatch::with('shipments.invoices.items')->find(29);
if ($batch) {
    echo "Batch: {$batch->batch_number} - {$batch->name}\n";
    echo "Status: {$batch->current_status}\n";
    echo "Shipments: {$batch->shipments->count()}\n\n";

    foreach ($batch->shipments as $shipment) {
        echo "--- Shipment: {$shipment->tracking_number} ---\n";
        $invoice = $shipment->invoices->first() ?? $shipment->invoice;
        if ($invoice) {
            echo "Invoice: {$invoice->invoice_number} | Total: UGX " . number_format($invoice->total, 0) . "\n";
            echo "Items:\n";
            foreach ($invoice->items as $item) {
                $isStorage = strtolower(trim($item->description)) === 'storage fee' ? ' [STORAGE FEE]' : '';
                echo "  - {$item->description}: UGX " . number_format($item->amount, 0) . "{$isStorage}\n";
            }
        } else {
            echo "No invoice\n";
        }
        echo "\n";
    }
} else {
    echo "Batch not found";
}