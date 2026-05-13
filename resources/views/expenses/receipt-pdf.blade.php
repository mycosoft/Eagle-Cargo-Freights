<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Receipt - {{ $expense->expense_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .company-info {
            margin-top: 10px;
            font-size: 12px;
            color: #666;
        }
        .receipt-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            color: #333;
        }
        .receipt-number {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table th {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            width: 40%;
        }
        .info-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .amount-section {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
        }
        .amount-label {
            font-size: 16px;
            color: #666;
        }
        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
        }
        .category-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-pending { background-color: #ffc107; color: #333; }
        .status-approved { background-color: #007bff; color: white; }
        .status-rejected { background-color: #dc3545; color: white; }
        .status-paid { background-color: #28a745; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ $companySettings['name'] }}</div>
            <div class="company-info">
                {{ $companySettings['address'] }}<br>
                Call: {{ $companySettings['phone'] }} | WhatsApp: {{ $companySettings['whatsapp'] }}<br>
                China: {{ $companySettings['china'] }} | Website: {{ $companySettings['website'] }}<br>
                Email: {{ $companySettings['email'] }}
            </div>
        </div>

        <div class="receipt-title">EXPENSE RECEIPT</div>
        <div class="receipt-number">{{ $expense->expense_number }}</div>
        <div style="text-align: center; margin-bottom: 20px;">
            <span class="status-badge status-{{ $expense->status }}">
                {{ ucfirst($expense->status) }}
            </span>
        </div>

        <table class="info-table">
            <tr>
                <th>Expense Date</th>
                <td>{{ $expense->expense_date->format('d M Y') }}</td>
            </tr>
            <tr>
                <th>Category</th>
                <td>
                    <span class="category-badge" style="background-color: {{ $expense->category->color }};">
                        {{ $expense->category->name }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Payment Method</th>
                <td>{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</td>
            </tr>
            @if($expense->reference_number)
            <tr>
                <th>Reference Number</th>
                <td>{{ $expense->reference_number }}</td>
            </tr>
            @endif
            <tr>
                <th>Recorded By</th>
                <td>{{ $expense->recorder->name ?? 'N/A' }}</td>
            </tr>
            @if($expense->approved_by)
            <tr>
                <th>Approved By</th>
                <td>{{ $expense->approver->name }}</td>
            </tr>
            <tr>
                <th>Approved On</th>
                <td>{{ $expense->approved_at->format('d M Y H:i') }}</td>
            </tr>
            @endif
        </table>

        @if($expense->description)
        <div style="margin: 20px 0;">
            <strong>Description:</strong>
            <p>{{ $expense->description }}</p>
        </div>
        @endif

        @if($expense->notes)
        <div style="margin: 20px 0;">
            <strong>Notes:</strong>
            <p>{{ $expense->notes }}</p>
        </div>
        @endif

        <div class="amount-section">
            <div class="amount-label">Total Amount</div>
            <div class="amount-value">UGX {{ number_format($expense->amount, 0) }}</div>
        </div>

        @if($expense->rejection_reason)
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Rejection Reason:</strong> {{ $expense->rejection_reason }}
        </div>
        @endif

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <strong>Recorded By</strong><br>
                    {{ $expense->recorder->name ?? 'N/A' }}<br>
                    {{ $expense->created_at->format('d M Y H:i') }}
                </div>
            </div>
            @if($expense->approved_by)
            <div class="signature-box">
                <div class="signature-line">
                    <strong>Approved By</strong><br>
                    {{ $expense->approver->name }}<br>
                    {{ $expense->approved_at->format('d M Y H:i') }}
                </div>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>This is a computer-generated expense receipt.</p>
            <p>Generated on: {{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
