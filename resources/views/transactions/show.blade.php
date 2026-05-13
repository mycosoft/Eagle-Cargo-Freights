@extends('adminlte::page')

@section('title', 'Transaction: ' . $payment->receipt_number)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-receipt"></i> Transaction {{ $payment->receipt_number }}</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary float-right ml-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.payments.receipt', $payment) }}" class="btn btn-primary float-right ml-2" target="_blank">
                <i class="fas fa-file-pdf"></i> Download Receipt
            </a>
            @if($payment->invoice)
            <a href="{{ route('admin.invoices.show', $payment->invoice) }}" class="btn btn-info float-right">
                <i class="fas fa-file-invoice-dollar"></i> View Invoice
            </a>
            @endif
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-money-check-alt"></i> Payment Details</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="45%"><strong>Receipt Number</strong></td>
                        <td><strong class="text-success">{{ $payment->receipt_number }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Payment Date</strong></td>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Amount Paid</strong></td>
                        <td>
                            @php $sym = match($payment->invoice?->shipment?->currency ?? 'UGX') { 'USD' => '$', 'EUR' => '€', 'GBP' => '£', default => 'UGX' }; @endphp
                            <strong class="text-success h5">{{ $sym }} {{ number_format($payment->amount, 2) }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Payment Method</strong></td>
                        <td>
                            @php
                                $methodColors = ['cash' => 'success', 'card' => 'info', 'bank_transfer' => 'primary', 'mobile_money' => 'warning'];
                                $methodLabels = ['cash' => 'Cash', 'card' => 'Card', 'bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money'];
                            @endphp
                            <span class="badge badge-{{ $methodColors[$payment->payment_method] ?? 'secondary' }}">
                                {{ $methodLabels[$payment->payment_method] ?? ucfirst($payment->payment_method) }}
                            </span>
                        </td>
                    </tr>
                    @if($payment->reference_number)
                    <tr>
                        <td class="text-muted"><strong>Reference #</strong></td>
                        <td>{{ $payment->reference_number }}</td>
                    </tr>
                    @endif
                    @if($payment->notes)
                    <tr>
                        <td class="text-muted"><strong>Notes</strong></td>
                        <td>{{ $payment->notes }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted"><strong>Recorded By</strong></td>
                        <td>{{ $payment->recorder?->name ?? 'System' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Recorded At</strong></td>
                        <td>{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                    @if($payment->receipt_sent_at)
                    <tr>
                        <td class="text-muted"><strong>Receipt Sent</strong></td>
                        <td>{{ $payment->receipt_sent_at->format('d M Y, h:i A') }} via {{ $payment->receipt_sent_via }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        @if($payment->invoice)
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Invoice Details</h3>
            </div>
            <div class="card-body">
                @php $invoice = $payment->invoice; $shipment = $invoice->shipment; @endphp
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="45%"><strong>Invoice #</strong></td>
                        <td><a href="{{ route('admin.invoices.show', $invoice) }}"><strong>{{ $invoice->invoice_number }}</strong></a></td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Status</strong></td>
                        <td>
                            @php $statusColors = ['paid' => 'success', 'partial' => 'warning', 'overdue' => 'danger', 'draft' => 'secondary', 'sent' => 'info']; @endphp
                            <span class="badge badge-{{ $statusColors[$invoice->status] ?? 'secondary' }}">{{ ucfirst($invoice->status) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Invoice Total</strong></td>
                        <td>{{ $sym }} {{ number_format($invoice->total, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Total Paid</strong></td>
                        <td class="text-success"><strong>{{ $sym }} {{ number_format($invoice->amount_paid, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Balance</strong></td>
                        <td class="{{ $invoice->balance > 0 ? 'text-danger' : 'text-success' }}">
                            <strong>{{ $sym }} {{ number_format($invoice->balance, 2) }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if($shipment)
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-{{ $shipment->shipment_type === 'air' ? 'plane' : 'ship' }}"></i> Shipment Details</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="45%"><strong>Tracking #</strong></td>
                        <td><strong>{{ $shipment->tracking_number }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Client</strong></td>
                        <td>{{ $shipment->client?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Route</strong></td>
                        <td>{{ $shipment->origin }} → {{ $shipment->destination }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Type</strong></td>
                        <td>
                            <span class="badge badge-{{ $shipment->shipment_type === 'air' ? 'info' : 'primary' }}">
                                {{ ucfirst($shipment->shipment_type) }} Cargo
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Status</strong></td>
                        <td><span class="badge badge-secondary">{{ $shipment->current_status }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="d-inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete This Transaction
                    </button>
                </form>
                <a href="{{ route('admin.payments.send', $payment) }}" class="btn btn-outline-info ml-2" onclick="return confirm('Send receipt to client?')">
                    <i class="fas fa-paper-plane"></i> Send Receipt to Client
                </a>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop
