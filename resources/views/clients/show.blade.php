@extends('adminlte::page')

@section('title', 'Client: ' . $client->name)

@section('content_header')
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1><i class="fas fa-user-circle"></i> {{ $client->name }}</h1>
            @if($client->company)
                <small class="text-muted"><i class="fas fa-building"></i> {{ $client->company }}</small>
            @endif
        </div>
        <div class="col-sm-6 text-right">
            @can('edit clients')
            <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Client
            </a>
            @endcan
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')

@php
    $currency = \App\Models\Setting::getCurrencySymbol();
    $statusColors = [
        'Pending'             => 'warning',
        'Picked Up'           => 'info',
        'In Transit'          => 'primary',
        'Arrived at Facility' => 'secondary',
        'Out for Delivery'    => 'info',
        'Delivered'           => 'success',
        'On Hold'             => 'dark',
        'Cancelled'           => 'danger',
    ];
    $invoiceStatusColors = [
        'draft'     => 'secondary',
        'sent'      => 'info',
        'paid'      => 'success',
        'partial'   => 'warning',
        'overdue'   => 'danger',
        'cancelled' => 'dark',
    ];
@endphp

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    {{-- ─── Financial Summary Cards ─── --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-shipping-fast"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Shipments</span>
                    <span class="info-box-number">{{ $client->shipments->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Invoiced</span>
                    <span class="info-box-number">{{ $currency }} {{ number_format($totalInvoiced, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Paid</span>
                    <span class="info-box-number">{{ $currency }} {{ number_format($totalPaid, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box {{ $totalOutstanding > 0 ? 'bg-danger' : 'bg-success' }}">
                <span class="info-box-icon"><i class="fas fa-{{ $totalOutstanding > 0 ? 'exclamation-triangle' : 'check' }}"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Outstanding Balance</span>
                    <span class="info-box-number">{{ $currency }} {{ number_format($totalOutstanding, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- ─── Left column: Client Info ─── --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-id-card"></i> Client Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name</dt>
                        <dd class="col-sm-8">{{ $client->name }}</dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">
                            @if($client->email)
                                <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Phone</dt>
                        <dd class="col-sm-8">
                            @if($client->phone)
                                <a href="tel:{{ $client->phone }}">{{ $client->phone }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Company</dt>
                        <dd class="col-sm-8">{{ $client->company ?? '—' }}</dd>

                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8">{{ $client->address ?? '—' }}</dd>

                        <dt class="col-sm-4">Client Since</dt>
                        <dd class="col-sm-8">{{ $client->created_at->format('M d, Y') }}</dd>
                    </dl>
                </div>
            </div>

            {{-- ─── Quick Actions ─── --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="card-body p-2">
                    @can('edit clients')
                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-warning btn-block mb-1">
                        <i class="fas fa-edit"></i> Edit Client
                    </a>
                    @endcan
                    @can('create shipments')
                    <a href="{{ route('admin.shipments.create') }}?client_id={{ $client->id }}" class="btn btn-primary btn-block mb-1">
                        <i class="fas fa-plus"></i> New Shipment
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- ─── Right column: Shipments + Invoices + Payments ─── --}}
        <div class="col-md-8">

            {{-- Tabs --}}
            <div class="card">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="clientTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-shipments">
                                <i class="fas fa-box"></i> Shipments
                                <span class="badge badge-info">{{ $client->shipments->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-invoices">
                                <i class="fas fa-file-invoice"></i> Invoices
                                <span class="badge badge-primary">{{ $client->shipments->filter(fn($s) => $s->invoice)->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-payments">
                                <i class="fas fa-money-bill-wave"></i> Payments
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content p-0">

                    {{-- ── Tab: Shipments ── --}}
                    <div class="tab-pane active" id="tab-shipments">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Tracking #</th>
                                        <th>Type</th>
                                        <th>Route</th>
                                        <th>Status</th>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($client->shipments->sortByDesc('created_at') as $shipment)
                                        <tr>
                                            <td><strong>{{ $shipment->tracking_number }}</strong></td>
                                            <td>
                                                @if($shipment->shipment_type === 'air')
                                                    <span class="badge badge-info"><i class="fas fa-plane"></i> Air</span>
                                                @else
                                                    <span class="badge badge-primary"><i class="fas fa-ship"></i> Sea</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $shipment->origin }}</small>
                                                <i class="fas fa-arrow-right text-muted mx-1" style="font-size:10px"></i>
                                                <small>{{ $shipment->destination }}</small>
                                            </td>
                                            <td>
                                                @php $sc = $statusColors[$shipment->current_status] ?? 'secondary'; @endphp
                                                <span class="badge badge-{{ $sc }}">{{ $shipment->current_status }}</span>
                                            </td>
                                            <td>
                                                @if($shipment->invoice)
                                                    @php $ic = $invoiceStatusColors[$shipment->invoice->status] ?? 'secondary'; @endphp
                                                    <a href="{{ route('admin.invoices.show', $shipment->invoice) }}">
                                                        <span class="badge badge-{{ $ic }}">{{ ucfirst($shipment->invoice->status) }}</span>
                                                    </a>
                                                @else
                                                    <span class="badge badge-light text-muted">No Invoice</span>
                                                @endif
                                            </td>
                                            <td><small>{{ $shipment->created_at->format('M d, Y') }}</small></td>
                                            <td>
                                                <a href="{{ route('admin.shipments.show', $shipment) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i><br>No shipments yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ── Tab: Invoices ── --}}
                    <div class="tab-pane" id="tab-invoices">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Tracking #</th>
                                        <th>Date</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">Paid</th>
                                        <th class="text-right">Balance</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $hasInvoice = false; @endphp
                                    @foreach($client->shipments->sortByDesc('created_at') as $shipment)
                                        @if($shipment->invoice)
                                            @php
                                                $hasInvoice = true;
                                                $inv = $shipment->invoice;
                                                $invCur = \App\Models\Setting::getCurrencySymbol($shipment->currency ?? null);
                                                $ic = $invoiceStatusColors[$inv->status] ?? 'secondary';
                                                $hasStorage = $inv->items->contains(fn($item) => str_contains(strtolower($item->description), 'storage'));
                                            @endphp
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.invoices.show', $inv) }}"><strong>{{ $inv->invoice_number }}</strong></a>
                                                    @if($hasStorage)
                                                        <span class="badge badge-warning" title="Has storage fee"><i class="fas fa-warehouse"></i></span>
                                                    @endif
                                                </td>
                                                <td><small>{{ $shipment->tracking_number }}</small></td>
                                                <td><small>{{ $inv->issue_date->format('M d, Y') }}</small></td>
                                                <td class="text-right">{{ $invCur }} {{ number_format($inv->total, 2) }}</td>
                                                <td class="text-right text-success">{{ $invCur }} {{ number_format($inv->amount_paid, 2) }}</td>
                                                <td class="text-right {{ $inv->balance > 0 ? 'text-danger font-weight-bold' : 'text-success' }}">
                                                    {{ $invCur }} {{ number_format($inv->balance, 2) }}
                                                </td>
                                                <td><span class="badge badge-{{ $ic }}">{{ ucfirst($inv->status) }}</span></td>
                                                <td>
                                                    <a href="{{ route('admin.invoices.show', $inv) }}" class="btn btn-sm btn-info" title="View Invoice">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.invoices.pdf', $inv) }}" class="btn btn-sm btn-danger" title="Download PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @if(!$hasInvoice)
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="fas fa-file-invoice fa-2x mb-2"></i><br>No invoices yet.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                @if($hasInvoice && $totalInvoiced > 0)
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="3" class="text-right">TOTALS:</th>
                                        <th class="text-right">{{ $currency }} {{ number_format($totalInvoiced, 2) }}</th>
                                        <th class="text-right text-success">{{ $currency }} {{ number_format($totalPaid, 2) }}</th>
                                        <th class="text-right {{ $totalOutstanding > 0 ? 'text-danger' : 'text-success' }}">{{ $currency }} {{ number_format($totalOutstanding, 2) }}</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>

                    {{-- ── Tab: Payments ── --}}
                    <div class="tab-pane" id="tab-payments">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th class="text-right">Amount</th>
                                        <th>Reference</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $allPayments = collect();
                                        foreach($client->shipments as $shipment) {
                                            if ($shipment->invoice) {
                                                foreach($shipment->invoice->payments as $payment) {
                                                    $payment->_invoice = $shipment->invoice;
                                                    $payment->_shipment = $shipment;
                                                    $allPayments->push($payment);
                                                }
                                            }
                                        }
                                        $allPayments = $allPayments->sortByDesc('payment_date');
                                    @endphp
                                    @forelse($allPayments as $payment)
                                        @php
                                            $paymentCur = \App\Models\Setting::getCurrencySymbol($payment->_shipment->currency ?? null);
                                            $methodColors = ['cash' => 'success', 'card' => 'info', 'bank_transfer' => 'primary', 'mobile_money' => 'warning'];
                                            $methodLabels = ['cash' => 'Cash', 'card' => 'Card', 'bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money'];
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $payment->receipt_number }}</strong></td>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $payment->_invoice) }}">
                                                    <small>{{ $payment->_invoice->invoice_number }}</small>
                                                </a>
                                            </td>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge badge-{{ $methodColors[$payment->payment_method] ?? 'secondary' }}">
                                                    {{ $methodLabels[$payment->payment_method] ?? ucfirst($payment->payment_method) }}
                                                </span>
                                            </td>
                                            <td class="text-right font-weight-bold text-success">
                                                {{ $paymentCur }} {{ number_format($payment->amount, 2) }}
                                            </td>
                                            <td><small>{{ $payment->reference_number ?? '—' }}</small></td>
                                            <td>
                                                <a href="{{ route('admin.payments.receipt', $payment) }}" class="btn btn-sm btn-secondary" title="Receipt PDF" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-receipt fa-2x mb-2"></i><br>No payments recorded yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                    @if($allPayments->count() > 0)
                                    <tr class="bg-light font-weight-bold">
                                        <td colspan="4" class="text-right">Total Paid:</td>
                                        <td class="text-right text-success">{{ $currency }} {{ number_format($totalPaid, 2) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>{{-- end tab-content --}}
            </div>{{-- end card --}}
        </div>
    </div>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Bryan Logistics</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop
