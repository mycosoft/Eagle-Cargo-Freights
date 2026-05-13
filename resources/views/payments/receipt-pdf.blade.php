<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $payment->receipt_number }}</title>
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
            max-width: 100px;
            height: auto;
            margin-bottom: 10px;
        }
        .company-details {
            font-size: 9px;
            color: #666;
            line-height: 1.4;
        }
        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin: 12px 0;
            text-transform: uppercase;
        }
        .receipt-info {
            background: #f8f9fa;
            padding: 10px;
            margin: 15px 0;
            border-left: 3px solid #28a745;
        }
        .receipt-info table {
            width: 100%;
            font-size: 9px;
        }
        .receipt-info td {
            padding: 4px;
        }
        .receipt-info td:first-child {
            font-weight: bold;
            width: 120px;
        }
        .amount-box {
            background: #28a745;
            color: white;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
        }
        .amount-box .label {
            font-size: 10px;
            margin-bottom: 5px;
        }
        .amount-box .amount {
            font-size: 24px;
            font-weight: bold;
        }
        .invoice-details {
            margin: 20px 0;
            padding: 10px;
            background: #f8f9fa;
            border-left: 3px solid #3498db;
            font-size: 9px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details td {
            padding: 3px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 8px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path($companySettings['logo']) }}" alt="{{ $companySettings['name'] }}" class="logo">
        <div class="company-details">
            {{ $companySettings['address'] }}<br>
            Call: {{ $companySettings['phone'] }} | WhatsApp: {{ $companySettings['whatsapp'] }}<br>
            China: {{ $companySettings['china'] }} | Website: {{ $companySettings['website'] }}<br>
            Email: {{ $companySettings['email'] }}
        </div>
        <div class="document-title">Payment Receipt</div>
    </div>

    <div class="receipt-info">
        <table>
            <tr>
                <td>Receipt Number:</td>
                <td><strong>{{ $payment->receipt_number }}</strong></td>
            </tr>
            <tr>
                <td>Payment Date:</td>
                <td>{{ $payment->payment_date->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td>Payment Method:</td>
                <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
            </tr>
            @if($payment->reference_number)
            <tr>
                <td>Reference Number:</td>
                <td>{{ $payment->reference_number }}</td>
            </tr>
            @endif
        </table>
    </div>

    @php
        $curr = $shipment->currency ?? \App\Models\Setting::get('system_currency', 'UGX');
        $sym = match($curr) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $curr,
        };
    @endphp

    <div class="amount-box">
        <div class="label">Amount Paid</div>
        <div class="amount">{{ $sym }} {{ number_format($payment->amount, 2) }}</div>
    </div>

    <div class="invoice-details">
        <h4 style="margin: 0 0 10px 0; color: #2c3e50;">Invoice Details</h4>
        <table>
            <tr>
                <td style="font-weight: bold; width: 120px;">Invoice Number:</td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Invoice Total:</td>
                <td>{{ $sym }} {{ number_format($invoice->total, 2) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Paid:</td>
                <td>{{ $sym }} {{ number_format($invoice->amount_paid, 2) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Balance Due:</td>
                <td><strong style="color: {{ $invoice->balance > 0 ? '#dc3545' : '#28a745' }};">{{ $sym }} {{ number_format($invoice->balance, 2) }}</strong></td>
            </tr>
            @if($shipment)
            <tr>
                <td style="font-weight: bold;">Tracking Number:</td>
                <td>{{ $shipment->tracking_number }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($shipment && $shipment->client)
    <div style="margin: 20px 0; font-size: 9px;">
        <h4 style="margin: 0 0 10px 0; color: #2c3e50;">Client Information</h4>
        <strong>{{ $shipment->client->name }}</strong><br>
        @if($shipment->client->email)
            Email: {{ $shipment->client->email }}<br>
        @endif
        @if($shipment->client->phone)
            Phone: {{ $shipment->client->phone }}<br>
        @endif
    </div>
    @endif

    @if($payment->notes)
    <div style="margin: 20px 0; font-size: 9px;">
        <h4 style="margin: 0 0 10px 0; color: #2c3e50;">Notes</h4>
        <p style="margin: 0;">{{ $payment->notes }}</p>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Received By
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Authorized Signature
            </div>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} {{ $companySettings['name'] }}. All rights reserved.</p>
        <p>This is a computer-generated receipt. No signature is required.</p>
        <p><strong>Thank you for your business!</strong></p>
    </div>
</body>
</html>
