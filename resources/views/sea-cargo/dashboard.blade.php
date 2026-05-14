@extends('adminlte::page')

@section('title', 'Sea Cargo Dashboard')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-ship mr-2"></i>Sea Cargo Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('admin.sea-cargo.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> New Sea Shipment
            </a>
            <a href="{{ route('admin.sea-cargo.index') }}" class="btn btn-info float-right mr-2">
                <i class="fas fa-list"></i> All Sea Shipments
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\Setting::getCurrencySymbol(null) }} {{ number_format($revenueUgx, 0) }}</h3>
                    <p>Revenue (UGX)</p>
                </div>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>${{ number_format($revenueUsd, 0) }}</h3>
                    <p>Revenue (USD)</p>
                </div>
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\Setting::getCurrencySymbol(null) }} {{ number_format($totalInvoiced, 0) }}</h3>
                    <p>Total Invoiced</p>
                </div>
                <div class="icon"><i class="fas fa-file-invoice"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \App\Models\Setting::getCurrencySymbol(null) }} {{ number_format($outstanding, 0) }}</h3>
                    <p>Outstanding</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Shipments by Status</h3>
                </div>
                <div class="card-body">
                    <canvas id="seaPieChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Sea Shipments Trend</h3>
                </div>
                <div class="card-body">
                    <canvas id="seaTrendChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> Recent Sea Shipments</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($recentShipments as $s)
                            <li class="list-group-item">
                                <a href="{{ route('admin.sea-cargo.show', $s) }}">
                                    <strong>{{ $s->tracking_number }}</strong>
                                </a>
                                <br><small class="text-muted">{{ $s->client->name ?? 'N/A' }} - {{ $s->current_status }}</small>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center">No shipments yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-credit-card"></i> Recent Payments (Sea Cargo)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Invoice</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $p)
                                <tr>
                                    <td><strong>{{ $p->receipt_number }}</strong></td>
                                    <td>{{ $p->invoice->invoice_number ?? 'N/A' }}</td>
                                    <td>{{ $p->invoice->shipment->client->name ?? 'N/A' }}</td>
                                    <td>{{ \App\Models\Setting::getCurrencySymbol($p->invoice->shipment->currency ?? null) }} {{ number_format($p->amount, 0) }}</td>
                                    <td>{{ $p->payment_date->format('d M Y') }}</td>
                                    <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $p->payment_method)) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">No payments yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
                <div class="card-body">
                    <a href="{{ route('admin.sea-cargo.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Sea Shipment</a>
                    <a href="{{ route('admin.sea-cargo.index') }}" class="btn btn-info"><i class="fas fa-list"></i> All Sea Shipments</a>
                    <a href="{{ url('admin/invoices') }}" class="btn btn-success"><i class="fas fa-file-invoice"></i> Invoices</a>
                    <a href="{{ url('admin/batches') }}" class="btn btn-warning"><i class="fas fa-layer-group"></i> Batches</a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('seaPieChart'), {
    type: 'pie',
    data: {
        labels: ['Pending', 'In Transit', 'Delivered', 'On Hold'],
        datasets: [{
            data: [{{ $pending }}, {{ $inTransit }}, {{ $delivered }}, {{ $onHold }}],
            backgroundColor: ['#ffc107', '#007bff', '#28a745', '#6c757d']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('seaTrendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode(array_keys($monthlyData)) !!},
        datasets: [{
            label: 'Sea Shipments',
            data: {!! json_encode(array_values($monthlyData)) !!},
            backgroundColor: 'rgba(23, 162, 184, 0.2)',
            borderColor: 'rgba(23, 162, 184, 1)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
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