<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - {{ $companySettings['name'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 40px;
            background: #f5f5f5;
        }
        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 50px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 35px;
            padding-bottom: 25px;
            border-bottom: 3px solid #2c3e50;
        }
        .company-logo {
            max-width: 150px;
            height: auto;
        }
        .company-details {
            flex: 1;
            margin-left: 30px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .company-info {
            font-size: 13px;
            color: #666;
            line-height: 1.8;
        }
        .invoice-title-section {
            text-align: right;
        }
        .invoice-title {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .invoice-meta {
            font-size: 13px;
            color: #666;
        }
        .invoice-meta strong {
            color: #2c3e50;
        }
        .parties-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 30px;
        }
        .party-box {
            flex: 1;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .party-title {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .party-details {
            font-size: 14px;
            line-height: 1.8;
        }
        .section-box {
            margin-bottom: 25px;
            padding: 18px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 12px;
            letter-spacing: 1px;
            padding-bottom: 8px;
            border-bottom: 2px solid #dee2e6;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px 20px;
        }
        .detail-item {
            font-size: 13px;
        }
        .detail-label {
            color: #666;
        }
        .detail-value {
            font-weight: 600;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .invoice-table thead {
            background: #2c3e50;
            color: white;
        }
        .invoice-table th {
            padding: 14px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .invoice-table td {
            padding: 14px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }
        .invoice-table tbody tr:hover {
            background: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .totals-section {
            margin-left: auto;
            width: 350px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
        }
        .total-row.subtotal {
            border-top: 1px solid #e9ecef;
        }
        .total-row.grand-total {
            border-top: 2px solid #2c3e50;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        .total-row.amount-paid {
            color: #155724;
            font-weight: bold;
        }
        .total-row.balance-due {
            color: #721c24;
            font-weight: bold;
            font-size: 16px;
        }
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .payments-table thead {
            background: #155724;
            color: white;
        }
        .payments-table th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .payments-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
        }
        .packages-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .packages-table thead {
            background: #6c757d;
            color: white;
        }
        .packages-table th {
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        .packages-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
        }
        .invoice-footer {
            margin-top: 40px;
            padding-top: 25px;
            border-top: 2px solid #e9ecef;
            text-align: center;
        }
        .payment-status {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .status-paid { background: #d4edda; color: #155724; }
        .status-sent { background: #d1ecf1; color: #0c5460; }
        .status-partial { background: #fff3cd; color: #856404; }
        .status-overdue { background: #f8d7da; color: #721c24; }
        .status-draft { background: #e2e3e5; color: #383d41; }
        .thank-you {
            font-size: 16px;
            color: #666;
            margin-top: 20px;
        }
        .notes-section {
            margin-top: 25px;
            padding: 18px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
            text-align: left;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #856404;
        }
        @media print {
            body {
                padding: 0;
                background: white;
            }
            .invoice-container {
                box-shadow: none;
                padding: 20px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        {{-- Header --}}
        <div class="invoice-header">
            <div style="display: flex; align-items: flex-start;">
                @if(file_exists(public_path($companySettings['logo'])))
                <img src="{{ asset($companySettings['logo']) }}" alt="{{ $companySettings['name'] }}" class="company-logo">
                @endif
                <div class="company-details">
                    <div class="company-name">{{ $companySettings['name'] }}</div>
                    <div class="company-info">
                        {{ $companySettings['address'] }}<br>
                        Call: {{ $companySettings['phone'] }}<br>
                        WhatsApp: {{ $companySettings['whatsapp'] }}<br>
                        China: {{ $companySettings['china'] }}<br>
                        Website: {{ $companySettings['website'] }}<br>
                        Email: {{ $companySettings['email'] }}
                    </div>
                </div>
            </div>
            <div class="invoice-title-section">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Date:</strong> {{ $invoice->issue_date->format('M d, Y') }}<br>
                    @if($invoice->due_date)
                    <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                    @endif
                    <strong>Tracking # (HBL):</strong> {{ $shipment->tracking_number }}
                </div>
            </div>
        </div>

        {{-- Bill To / Ship To --}}
        <div class="parties-section">
            <div class="party-box">
                <div class="party-title">Bill To</div>
                <div class="party-details">
                    <strong>{{ $shipment->client->name }}</strong><br>
                    @if($shipment->client->company)
                    {{ $shipment->client->company }}<br>
                    @endif
                    @if($shipment->client->address)
                    {{ $shipment->client->address }}<br>
                    @endif
                    {{ $shipment->client->email }}<br>
                    {{ $shipment->client->phone }}
                </div>
            </div>
            <div class="party-box">
                <div class="party-title">Ship To</div>
                <div class="party-details">
                    @if($shipment->receiver)
                        <strong>{{ $shipment->receiver->name }}</strong><br>
                        @if($shipment->receiver->company)
                        {{ $shipment->receiver->company }}<br>
                        @endif
                        @if($shipment->receiver->address)
                        {{ $shipment->receiver->address }}<br>
                        @endif
                        {{ $shipment->receiver->email }}<br>
                        {{ $shipment->receiver->phone }}
                    @else
                        <strong>{{ $shipment->receiver_name ?? 'N/A' }}</strong><br>
                        @if($shipment->receiver_address)
                        {{ $shipment->receiver_address }}<br>
                        @endif
                        {{ $shipment->receiver_phone ?? '' }}
                    @endif
                </div>
            </div>
        </div>

        {{-- Shipment & Cargo Details --}}
        <div class="section-box">
            <div class="section-title">Shipment &amp; Cargo Details</div>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Tracking (HBL):</span><br>
                    <span class="detail-value">{{ $shipment->tracking_number }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Route:</span><br>
                    <span class="detail-value">{{ $shipment->origin }} → {{ $shipment->destination }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Service:</span><br>
                    <span class="detail-value">{{ ucfirst($shipment->service_type ?? 'Standard') }} / {{ ucfirst($shipment->shipment_type) }}</span>
                </div>
                @if($shipment->weight)
                <div class="detail-item">
                    <span class="detail-label">Total Weight:</span><br>
                    <span class="detail-value">{{ $shipment->weight }} kg</span>
                </div>
                @endif
                @if($shipment->cbm)
                <div class="detail-item">
                    <span class="detail-label">Total CBM:</span><br>
                    <span class="detail-value">{{ number_format($shipment->cbm, 3) }} m³</span>
                </div>
                @endif
                @if($shipment->num_packages)
                <div class="detail-item">
                    <span class="detail-label">Packages:</span><br>
                    <span class="detail-value">{{ $shipment->num_packages }}</span>
                </div>
                @endif
                @if($shipment->reference_number)
                <div class="detail-item">
                    <span class="detail-label">Reference #:</span><br>
                    <span class="detail-value">{{ $shipment->reference_number }}</span>
                </div>
                @endif
                @if($shipment->batch)
                <div class="detail-item">
                    <span class="detail-label">Batch:</span><br>
                    <span class="detail-value">{{ $shipment->batch->batch_number }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Package / Contents Details --}}
        @php
            $packages = $shipment->packages;
            $currency = $shipment->currency ?? 'USD';
        @endphp
        @if($packages && $packages->count() > 0)
        <div style="margin-bottom: 25px;">
            <div class="section-title" style="background: #6c757d; color: white; padding: 10px 15px; border-radius: 8px 8px 0 0; margin-bottom: 0;">Package / Contents Details</div>
            <table class="packages-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">L (cm)</th>
                        <th class="text-right">W (cm)</th>
                        <th class="text-right">H (cm)</th>
                        <th class="text-right">Weight (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($packages as $pkg)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pkg->description ?? 'Package ' . $loop->iteration }}</td>
                        <td class="text-right">{{ $pkg->quantity ?? 1 }}</td>
                        <td class="text-right">{{ $pkg->length ? number_format($pkg->length, 1) : '—' }}</td>
                        <td class="text-right">{{ $pkg->width ? number_format($pkg->width, 1) : '—' }}</td>
                        <td class="text-right">{{ $pkg->height ? number_format($pkg->height, 1) : '—' }}</td>
                        <td class="text-right">{{ $pkg->weight ? number_format($pkg->weight, 2) : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Charges Breakdown --}}
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th>Description</th>
                    <th class="text-right" style="width:10%">Qty</th>
                    <th class="text-right" style="width:15%">Rate</th>
                    <th class="text-right" style="width:18%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($invoice->items && $invoice->items->count() > 0)
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->description }}</strong></td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">{{ $currency }} {{ number_format($item->rate, 2) }}</td>
                        <td class="text-right">{{ $currency }} {{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td>1</td>
                        <td>
                            <strong>Shipping Charges</strong><br>
                            <small style="color: #666;">{{ ucfirst($shipment->service_type ?? 'Standard') }} Service</small>
                        </td>
                        <td class="text-right">1</td>
                        <td class="text-right">{{ $currency }} {{ number_format($shipment->shipping_cost, 2) }}</td>
                        <td class="text-right">{{ $currency }} {{ number_format($shipment->shipping_cost, 2) }}</td>
                    </tr>
                    @if($shipment->insurance_value > 0)
                    <tr>
                        <td>2</td>
                        <td>
                            <strong>Insurance Coverage</strong><br>
                            <small style="color: #666;">Package Protection</small>
                        </td>
                        <td class="text-right">1</td>
                        <td class="text-right">{{ $currency }} {{ number_format($shipment->insurance_value, 2) }}</td>
                        <td class="text-right">{{ $currency }} {{ number_format($shipment->insurance_value, 2) }}</td>
                    </tr>
                    @endif
                @endif
            </tbody>
        </table>

        {{-- Totals --}}
        @php
            $totalPaid = $invoice->payments->sum('amount');
            $balanceDue = $invoice->total - $totalPaid;
        @endphp
        <div class="totals-section">
            <div class="total-row subtotal">
                <span>Subtotal:</span>
                <span>{{ $currency }} {{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            @if($invoice->tax > 0)
            <div class="total-row">
                <span>Tax:</span>
                <span>{{ $currency }} {{ number_format($invoice->tax, 2) }}</span>
            </div>
            @endif
            @if($invoice->discount > 0)
            <div class="total-row">
                <span>Discount:</span>
                <span>-{{ $currency }} {{ number_format($invoice->discount, 2) }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>{{ $currency }} {{ number_format($invoice->total, 2) }}</span>
            </div>
            @if($totalPaid > 0)
            <div class="total-row amount-paid">
                <span>Amount Paid:</span>
                <span>{{ $currency }} {{ number_format($totalPaid, 2) }}</span>
            </div>
            <div class="total-row balance-due">
                <span>Balance Due:</span>
                <span>{{ $currency }} {{ number_format($balanceDue, 2) }}</span>
            </div>
            @endif
        </div>

        {{-- Payment Breakdown --}}
        @if($invoice->payments && $invoice->payments->count() > 0)
        <div style="margin-bottom: 25px;">
            <div class="section-title" style="background: #155724; color: white; padding: 10px 15px; border-radius: 8px 8px 0 0; margin-bottom: 0;">Payment Breakdown</div>
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Recorded By</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{ $payment->receipt_number }}</td>
                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>{{ $payment->reference_number ?? '—' }}</td>
                        <td>{{ $payment->recorder ? $payment->recorder->name : 'System' }}</td>
                        <td class="text-right">{{ $currency }} {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr style="font-weight: bold; background: #e8f5e9;">
                        <td colspan="5" class="text-right" style="font-size: 14px;">Total Payments:</td>
                        <td class="text-right" style="font-size: 14px;">{{ $currency }} {{ number_format($totalPaid, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        @if($invoice->notes)
        <div class="notes-section">
            <div class="notes-title">Notes:</div>
            <div>{{ $invoice->notes }}</div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="invoice-footer">
            <div class="payment-status status-{{ $invoice->status }}">
                Status: {{ ucfirst($invoice->status) }}
            </div>
            <div class="thank-you">
                Thank you for choosing {{ $companySettings['name'] }}!<br>
                <small style="color: #999;">For inquiries, contact us at {{ $companySettings['email'] }} or {{ $companySettings['phone'] }} | Website: {{ $companySettings['website'] }}</small>
            </div>
        </div>

        {{-- Print / Close Buttons --}}
        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()" style="padding: 12px 30px; background: #2c3e50; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: bold;">
                Print Invoice
            </button>
            <button onclick="window.close()" style="padding: 12px 30px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: bold; margin-left: 10px;">
                Close
            </button>
        </div>
    </div>
</body>
</html>
