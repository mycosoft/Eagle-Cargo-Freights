<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shipments Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .stats { display: table; width: 100%; margin-bottom: 20px; }
        .stat-box { display: table-cell; width: 25%; text-align: center; padding: 15px; background: #f4f4f4; border: 1px solid #ddd; }
        .stat-box h3 { margin: 0; font-size: 24px; color: #007bff; }
        .stat-box p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #007bff; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .badge { padding: 3px 8px; border-radius: 3px; background: #17a2b8; color: white; font-size: 10px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Eagle Cargo Freights</h1>
        <p>Shipments Report</p>
        <p>Generated on: {{ date('F d, Y H:i') }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>{{ $stats['total'] }}</h3>
            <p>Total Shipments</p>
        </div>
        <div class="stat-box">
            <h3>{{ $stats['pending'] }}</h3>
            <p>Pending</p>
        </div>
        <div class="stat-box">
            <h3>{{ $stats['in_transit'] }}</h3>
            <p>In Transit</p>
        </div>
        <div class="stat-box">
            <h3>{{ $stats['delivered'] }}</h3>
            <p>Delivered</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tracking #</th>
                <th>Client</th>
                <th>Origin</th>
                <th>Destination</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipments as $shipment)
                <tr>
                    <td>{{ $shipment->tracking_number }}</td>
                    <td>{{ $shipment->client->name }}</td>
                    <td>{{ $shipment->origin }}</td>
                    <td>{{ $shipment->destination }}</td>
                    <td><span class="badge">{{ $shipment->current_status }}</span></td>
                    <td>{{ $shipment->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Eagle Cargo Freights. All rights reserved.</p>
    </div>
</body>
</html>
