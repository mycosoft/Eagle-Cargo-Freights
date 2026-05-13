@extends('adminlte::page')

@section('title', 'Transactions')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-exchange-alt"></i> Transactions</h1>
        </div>
        <div class="col-sm-6">
            @can('create transactions')
            <a href="{{ route('admin.transactions.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Record Payment
            </a>
            @endcan
        </div>
    </div>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Collected</span>
                    <span class="info-box-number">{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($totalCollected, 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Transactions</span>
                    <span class="info-box-number">{{ $totalCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-calendar-day"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">This Month</span>
                    <span class="info-box-number">{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($thisMonthTotal, 0) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Today</span>
                    <span class="info-box-number">{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($todayTotal, 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card card-outline card-secondary mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filter Transactions</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.transactions.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Receipt #, Reference, Client..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="method" class="form-control">
                                <option value="">All Methods</option>
                                <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ request('method') == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile_money" {{ request('method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                            <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Clear</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> All Transactions ({{ $payments->total() }})</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Receipt #</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Invoice #</th>
                            <th>Shipment</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Recorded By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            @php
                                $invoice = $payment->invoice;
                                $shipment = $invoice?->shipment;
                                $client = $shipment?->client;
                                $sym = \App\Models\Setting::getCurrencySymbol($shipment?->currency ?? null);
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.transactions.show', $payment) }}" class="text-primary font-weight-bold">
                                        {{ $payment->receipt_number }}
                                    </a>
                                </td>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td>{{ $client?->name ?? '—' }}</td>
                                <td>
                                    @if($invoice)
                                        <a href="{{ route('admin.invoices.show', $invoice) }}">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    @else —
                                    @endif
                                </td>
                                <td>
                                    @if($shipment)
                                        <span class="badge badge-{{ $shipment->shipment_type === 'air' ? 'info' : 'primary' }}">
                                            <i class="fas fa-{{ $shipment->shipment_type === 'air' ? 'plane' : 'ship' }}"></i>
                                            {{ $shipment->tracking_number }}
                                        </span>
                                    @else —
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $methodColors = ['cash' => 'success', 'card' => 'info', 'bank_transfer' => 'primary', 'mobile_money' => 'warning'];
                                        $methodLabels = ['cash' => 'Cash', 'card' => 'Card', 'bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money'];
                                    @endphp
                                    <span class="badge badge-{{ $methodColors[$payment->payment_method] ?? 'secondary' }}">
                                        {{ $methodLabels[$payment->payment_method] ?? ucfirst($payment->payment_method) }}
                                    </span>
                                </td>
                                <td>{{ $payment->reference_number ?? '—' }}</td>
                                <td class="font-weight-bold text-success">{{ $sym }} {{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->recorder?->name ?? 'System' }}</td>
                                <td>
                                    <a href="{{ route('admin.transactions.show', $payment) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.payments.receipt', $payment) }}" class="btn btn-sm btn-secondary" title="Receipt PDF" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @can('delete transactions')
                                    <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No transactions found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $payments->withQueryString()->links() }}
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
