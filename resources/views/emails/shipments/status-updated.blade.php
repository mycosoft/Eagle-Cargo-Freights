<!DOCTYPE html>
<html>
<head>
    <title>Shipment Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #007bff; color: #fff; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; }
        .status-badge { display: inline-block; padding: 5px 10px; background-color: #17a2b8; color: #fff; border-radius: 3px; font-weight: bold; }
        .button { display: inline-block; padding: 10px 20px; background-color: #28a745; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Shipment Update</h2>
        </div>
        <div class="content">
            <p>Dear {{ $shipment->client->name }},</p>
            <p>The status of your shipment (Tracking #: <strong>{{ $shipment->tracking_number }}</strong>) has been updated.</p>
            
            <p><strong>New Status:</strong> <span class="status-badge">{{ $statusUpdate->status }}</span></p>
            <p><strong>Location:</strong> {{ $statusUpdate->location }}</p>
            @if($statusUpdate->remarks)
                <p><strong>Remarks:</strong> {{ $statusUpdate->remarks }}</p>
            @endif

            <p>You can track the full history of your shipment by clicking the button below:</p>
            <p style="text-align: center;">
                <a href="{{ route('tracking.result', ['tracking_number' => $shipment->tracking_number]) }}" class="button">Track Shipment</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Eagle Cargo Freights. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
