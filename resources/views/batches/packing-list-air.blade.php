<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Packing List - {{ $batch->batch_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 15px;
            color: #333;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }
        .logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        .company-details {
            font-size: 9px;
            color: #666;
            line-height: 1.4;
            margin-top: 5px;
        }
        .document-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin: 12px 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .batch-info {
            background: #f8f9fa;
            padding: 10px;
            margin: 15px 0;
            border-left: 3px solid #3498db;
            font-size: 9px;
        }
        .batch-info table {
            width: 100%;
        }
        .batch-info td {
            padding: 3px;
        }
        .batch-info td:first-child {
            font-weight: bold;
            width: 120px;
        }
        .shipments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 9px;
        }
        .shipments-table th {
            background: #2c3e50;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
        }
        .shipments-table td {
            padding: 5px 4px;
            border-bottom: 1px solid #ddd;
        }
        .shipments-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .totals {
            margin-top: 15px;
            background: #e8f4f8;
            padding: 10px;
            border-left: 3px solid #3498db;
            font-size: 9px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 3px;
            font-weight: bold;
        }
        .totals td:first-child {
            width: 150px;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #666;
            font-size: 8px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Professional Header -->
    <div class="header">
        <img src="{{ public_path('images/logo.jpeg') }}" alt="Eagle Cargo Freights" class="logo">
        <div class="company-details">
            Nabugabo BTC Building, Kampala<br>
            Tel: UG +256 701 57941 / China +86 130 7021 8275 | Email: eaglecargofreights@gmail.com
        </div>
        <div class="document-title">Packing List - Air Cargo</div>
    </div>

    <!-- Batch Information -->
    <div class="batch-info">
        <table>
            <tr>
                <td>Batch Number:</td>
                <td><strong>{{ $batch->batch_number }}</strong></td>
            </tr>
            <tr>
                <td>Batch Name:</td>
                <td>{{ $batch->name }}</td>
            </tr>
            <tr>
                <td>Date:</td>
                <td>{{ $batch->created_at->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>{{ $batch->current_status }}</td>
            </tr>
            <tr>
                <td>Total Shipments:</td>
                <td>{{ $batch->shipments->count() }}</td>
            </tr>
        </table>
    </div>

    <table class="shipments-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tracking #</th>
                <th style="width: 20%;">Client</th>
                <th style="width: 25%;">Route</th>
                <th style="width: 10%;">Weight (kg)</th>
                <th style="width: 10%;">Packages</th>
                <th style="width: 20%;">Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($batch->shipments as $shipment)
            <tr>
                <td>{{ $shipment->tracking_number }}</td>
                <td>{{ $shipment->client->name }}</td>
                <td>{{ $shipment->origin }} → {{ $shipment->destination }}</td>
                <td>{{ number_format($shipment->weight ?? 0, 2) }}</td>
                <td>{{ $shipment->num_packages ?? '-' }}</td>
                <td>{{ Str::limit($shipment->description ?? 'N/A', 50) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Total Shipments:</td>
                <td>{{ $batch->shipments->count() }}</td>
            </tr>
            <tr>
                <td>Total Weight:</td>
                <td>{{ number_format($totalWeight, 2) }} kg</td>
            </tr>
            <tr>
                <td>Total Packages:</td>
                <td>{{ $totalPackages }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Eagle Cargo Freights. All rights reserved.</p>
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html>
