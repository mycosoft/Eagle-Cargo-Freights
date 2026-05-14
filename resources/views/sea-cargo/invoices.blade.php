@extends('adminlte::page')

@section('title', 'Sea Cargo Invoices')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-file-invoice mr-2"></i>Sea Cargo Invoices</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('admin.sea-cargo.dashboard') }}" class="btn btn-primary float-right"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4"><div class="info-box"><span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span><div class="info-box-content"><span class="info-box-text">Total Invoiced</span><span class="info-box-number">${{ number_format($totalInvoiced, 0) }}</span></div></div></div>
        <div class="col-md-4"><div class="info-box"><span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span><div class="info-box-content"><span class="info-box-text">Total Paid</span><span class="info-box-number">${{ number_format($totalPaid, 0) }}</span></div></div></div>
        <div class="col-md-4"><div class="info-box"><span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span><div class="info-box-content"><span class="info-box-text">Outstanding</span><span class="info-box-number">${{ number_format($totalBalance, 0) }}</span></div></div></div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Client</th>
                        <th>Tracking</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td><strong>{{ $inv->invoice_number }}</strong></td>
                            <td>{{ $inv->shipment->client->name ?? 'N/A' }}</td>
                            <td><a href="{{ route('admin.sea-cargo.show', $inv->shipment) }}">{{ $inv->shipment->tracking_number ?? 'N/A' }}</a></td>
                            <td>${{ number_format($inv->total, 0) }}</td>
                            <td class="text-success">${{ number_format($inv->amount_paid, 0) }}</td>
                            <td class="text-danger">${{ number_format($inv->balance, 0) }}</td>
                            <td>
                                @if($inv->status == 'paid') <span class="badge badge-success">Paid</span>
                                @elseif($inv->status == 'partial') <span class="badge badge-warning">Partial</span>
                                @elseif($inv->status == 'overdue') <span class="badge badge-danger">Overdue</span>
                                @else <span class="badge badge-secondary">{{ ucfirst($inv->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $inv->issue_date->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No invoices found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong> All rights reserved.
@stop