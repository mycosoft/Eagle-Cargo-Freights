<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            width: 800px;
        }
        .receipt {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .receipt-title {
            font-size: 36px;
            color: #27ae60;
            font-weight: bold;
            margin: 20px 0;
        }
        .receipt-number {
            font-size: 18px;
            color: #7f8c8d;
        }
        .info-section {
            margin: 30px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .info-label {
            font-weight: bold;
            color: #34495e;
            font-size: 16px;
        }
        .info-value {
            color: #2c3e50;
            font-size: 16px;
        }
        .amount-section {
            background: #ecf0f1;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: center;
        }
        .amount-label {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        .amount-value {
            font-size: 42px;
            font-weight: bold;
            color: #27ae60;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 14px;
        }
        .thank-you {
            font-size: 24px;
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="company-name">{{ $companySettings['name'] }}</div>
            <div style="color: #7f8c8d; font-size: 14px; margin-top: 5px;">
                {{ $companySettings['address'] }}<br>
                Call: {{ $companySettings['phone'] }} | WhatsApp: {{ $companySettings['whatsapp'] }}<br>
                China: {{ $companySettings['china'] }} | Website: {{ $companySettings['website'] }}<br>
                Email: {{ $companySettings['email'] }}
            </div>
        </div>

        <div style="text-align: center;">
            <div class="receipt-title">PAYMENT RECEIPT</div>
            <div class="receipt-number">{{ $payment->receipt_number }}</div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ $payment->payment_date->format('F d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Received From:</span>
                <span class="info-value">{{ $payment->invoice->shipment->client->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Method:</span>
                <span class="info-value">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</span>
            </div>
            @if($payment->reference_number)
            <div class="info-row">
                <span class="info-label">Reference:</span>
                <span class="info-value">{{ $payment->reference_number }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Invoice Number:</span>
                <span class="info-value">{{ $payment->invoice->invoice_number }}</span>
            </div>
        </div>

        @php
            $curr = $payment->invoice->shipment->currency ?? \App\Models\Setting::get('system_currency', 'UGX');
            $sym = match($curr) {
                'USD' => '$',
                'EUR' => '€',
                'GBP' => '£',
                default => $curr,
            };
        @endphp

        <div class="amount-section">
            <div class="amount-label">Amount Paid</div>
            <div class="amount-value">{{ $sym }} {{ number_format($payment->amount, 0) }}</div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Invoice Total:</span>
                <span class="info-value">{{ $sym }} {{ number_format($payment->invoice->total, 0) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Paid:</span>
                <span class="info-value">{{ $sym }} {{ number_format($payment->invoice->amount_paid, 0) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Balance Due:</span>
                <span class="info-value" style="color: {{ $payment->invoice->balance > 0 ? '#e74c3c' : '#27ae60' }}; font-weight: bold;">
                    {{ $sym }} {{ number_format($payment->invoice->balance, 0) }}
                </span>
            </div>
        </div>

        <div class="footer">
            <div class="thank-you">Thank You!</div>
            <p>This is an official receipt from {{ $companySettings['name'] }}</p>
            <p style="margin-top: 10px;">For any queries, please contact us at {{ $companySettings['phone'] }} or {{ $companySettings['email'] }}</p>
        </div>
    </div>
</body>
</html>
