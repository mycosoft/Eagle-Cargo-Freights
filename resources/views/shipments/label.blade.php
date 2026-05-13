<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label - {{ $shipment->tracking_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .label-container {
            border: 2px solid #000;
            width: 400px;
            padding: 20px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .barcode {
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
        }
        .details {
            display: flex;
            justify-content: space-between;
        }
        .sender, .receiver {
            width: 48%;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
            text-align: center;
            font-size: 12px;
        }
        @media print {
            body { margin: 0; padding: 0; }
            .label-container { border: none; width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="label-container">
        <div class="header">
            <h1>EAGLE CARGO FREIGHTS</h1>
            <p>Express Delivery</p>
        </div>
        
        <div class="barcode">
            {{ $shipment->tracking_number }}
        </div>
        
        <div class="details">
            <div class="sender">
                <strong>FROM:</strong><br>
                {{ $shipment->sender_name }}<br>
                {{ $shipment->sender_phone }}<br>
                {!! nl2br(e($shipment->sender_address)) !!}
            </div>
            <div class="receiver">
                <strong>TO:</strong><br>
                {{ $shipment->receiver_name }}<br>
                {{ $shipment->receiver_phone }}<br>
                {!! nl2br(e($shipment->receiver_address)) !!}
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <strong>Weight:</strong> {{ $shipment->weight }} kg<br>
            <strong>Service:</strong> {{ ucfirst($shipment->service_type ?? 'Standard') }}<br>
            <strong>Date:</strong> {{ now()->format('d/m/Y') }}
        </div>
        
        <div class="footer">
            Thank you for choosing Eagle Cargo Freights
        </div>
    </div>
</body>
</html>
