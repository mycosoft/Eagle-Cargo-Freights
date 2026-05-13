<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2c3e50;
        }
        .company-logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        .company-details {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .company-info {
            font-size: 11px;
            color: #666;
            line-height: 1.6;
        }
        .invoice-title-section {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .invoice-meta {
            font-size: 11px;
            color: #666;
        }
        .parties-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .party-box {
            display: table-cell;
            width: 48%;
            background: #f8f9fa;
            padding: 15px;
            vertical-align: top;
        }
        .party-box:first-child {
            margin-right: 4%;
        }
        .party-title {
            font-size: 10px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .party-details {
            font-size: 11px;
            line-height: 1.6;
        }
        .section-box {
            margin-bottom: 20px;
            padding: 12px;
            background: #f8f9fa;
            font-size: 11px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            padding-bottom: 5px;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-grid {
            display: table;
            width: 100%;
        }
        .detail-row {
            display: table-row;
        }
        .detail-label {
            display: table-cell;
            width: 35%;
            padding: 3px 0;
            color: #666;
        }
        .detail-value {
            display: table-cell;
            padding: 3px 0;
            font-weight: 500;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .invoice-table thead {
            background: #2c3e50;
            color: white;
        }
        .invoice-table th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
        }
        .invoice-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
        }
        .text-right { text-align: right; }
        .totals-section {
            margin-left: auto;
            width: 300px;
            margin-bottom: 15px;
        }
        .total-row {
            display: table;
            width: 100%;
            padding: 5px 0;
            font-size: 11px;
        }
        .total-row > span {
            display: table-cell;
        }
        .total-row > span:last-child {
            text-align: right;
        }
        .total-row.subtotal {
            border-top: 1px solid #e9ecef;
        }
        .total-row.grand-total {
            border-top: 2px solid #2c3e50;
            margin-top: 8px;
            padding-top: 10px;
            font-size: 14px;
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
            font-size: 13px;
        }
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .payments-table thead {
            background: #155724;
            color: white;
        }
        .payments-table th {
            padding: 8px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
        }
        .payments-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e9ecef;
            font-size: 10px;
        }
        .packages-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .packages-table thead {
            background: #6c757d;
            color: white;
        }
        .packages-table th {
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
        }
        .packages-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e9ecef;
            font-size: 10px;
        }
        .invoice-footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            font-size: 11px;
        }
        .payment-status {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 15px;
        }
        .status-paid { background: #d4edda; color: #155724; }
        .status-sent { background: #d1ecf1; color: #0c5460; }
        .status-partial { background: #fff3cd; color: #856404; }
        .status-overdue { background: #f8d7da; color: #721c24; }
        .status-draft { background: #e2e3e5; color: #383d41; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="invoice-container">
        {{-- Header --}}
        <div class="invoice-header">
            <div class="company-details">
                @if(file_exists(public_path($companySettings['logo'])))
                <img src="{{ public_path($companySettings['logo']) }}" alt="{{ $companySettings['name'] }}" class="company-logo">
                @endif
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
            <div class="invoice-title-section">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Date:</strong> {{ $invoice->issue_date->format('M d, Y') }}<br>
                    @if($invoice->due_date)
                    <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                    @endif
                    <strong>Tracking #:</strong> {{ $shipment->tracking_number }}
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
                <div class="detail-row">
                    <div class="detail-label">Tracking Number (HBL):</div>
                    <div class="detail-value">{{ $shipment->tracking_number }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Route:</div>
                    <div class="detail-value">{{ $shipment->origin }} → {{ $shipment->destination }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Service Type:</div>
                    <div class="detail-value">{{ ucfirst($shipment->service_type ?? 'Standard') }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Shipment Type:</div>
                    <div class="detail-value">{{ ucfirst($shipment->shipment_type) }}</div>
                </div>
                @if($shipment->description)
                <div class="detail-row">
                    <div class="detail-label">Description:</div>
                    <div class="detail-value">{{ $shipment->description }}</div>
                </div>
                @endif
                @if($shipment->reference_number)
                <div class="detail-row">
                    <div class="detail-label">Reference Number:</div>
                    <div class="detail-value">{{ $shipment->reference_number }}</div>
                </div>
                @endif
                <div class="detail-row">
                    <div class="detail-label">Total Weight:</div>
                    <div class="detail-value">{{ $shipment->weight ?? 'N/A' }} kg</div>
                </div>
                @if($shipment->cbm)
                <div class="detail-row">
                    <div class="detail-label">Total CBM:</div>
                    <div class="detail-value">{{ number_format($shipment->cbm, 3) }} m³</div>
                </div>
                @endif
                @if($shipment->num_packages)
                <div class="detail-row">
                    <div class="detail-label">Number of Packages:</div>
                    <div class="detail-value">{{ $shipment->num_packages }}</div>
                </div>
                @endif
                @if($shipment->batch)
                <div class="detail-row">
                    <div class="detail-label">Batch:</div>
                    <div class="detail-value">{{ $shipment->batch->batch_number }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Package Details --}}
        @php
            $packages = $shipment->packages;
        @endphp
        @if($packages && $packages->count() > 0)
        <div class="section-box" style="padding: 0; background: white;">
            <table class="packages-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th class="text-right">Length (cm)</th>
                        <th class="text-right">Width (cm)</th>
                        <th class="text-right">Height (cm)</th>
                        <th class="text-right">Weight (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($packages as $pkg)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pkg->description ?? 'Package ' . $loop->iteration }}</td>
                        <td>{{ $pkg->quantity ?? 1 }}</td>
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
                @php
                    $currency = $shipment->currency ?? 'USD';
                @endphp
                @if($invoice->items && $invoice->items->count() > 0)
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $item->description }}</strong>
                        </td>
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
            @php
                $totalPaid = $invoice->payments->sum('amount');
                $balanceDue = $invoice->total - $totalPaid;
            @endphp
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
        <div style="margin-bottom: 20px;">
            <div class="section-title" style="background: #155724; color: white; padding: 8px 12px;">Payment History</div>
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Reference</th>
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
                        <td class="text-right">{{ $currency }} {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr style="font-weight: bold; background: #f8f9fa;">
                        <td colspan="4" class="text-right">Total Payments:</td>
                        <td class="text-right">{{ $currency }} {{ number_format($totalPaid, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        {{-- Footer --}}
        <div class="invoice-footer">
            <div class="payment-status status-{{ $invoice->status }}">
                Status: {{ ucfirst($invoice->status) }}
            </div>
            @if($invoice->notes)
            <div style="margin-bottom: 15px; padding: 10px; background: #fff3cd; border-left: 3px solid #ffc107; text-align: left; font-size: 11px;">
                <strong>Notes:</strong> {{ $invoice->notes }}
            </div>
            @endif
            <div>
                Thank you for choosing {{ $companySettings['name'] }}!<br>
                <small style="color: #999;">For inquiries, contact us at {{ $companySettings['email'] }} or {{ $companySettings['phone'] }} | Website: {{ $companySettings['website'] }}</small>
            </div>
        </div>
    </div>
</body>
</html>
