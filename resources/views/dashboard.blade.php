@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Welcome Back, {{ auth()->user()->name }}!</h1>
@stop

@section('content')
    <!-- Cargo Type Summary -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-plane mr-2"></i>Air Cargo</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.air-cargo.dashboard') }}" class="btn btn-sm btn-info">Dashboard</a>
                        <a href="{{ route('admin.air-cargo.index') }}" class="btn btn-sm btn-secondary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 text-center">
                            <h3 class="text-info">{{ $airCount }}</h3>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-4 text-center">
                            <h3 class="text-warning">{{ $airInTransit }}</h3>
                            <small class="text-muted">In Transit</small>
                        </div>
                        <div class="col-4 text-center">
                            <h3 class="text-success">{{ $airDelivered }}</h3>
                            <small class="text-muted">Delivered</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-ship mr-2"></i>Sea Cargo</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.sea-cargo.dashboard') }}" class="btn btn-sm btn-primary">Dashboard</a>
                        <a href="{{ route('admin.sea-cargo.index') }}" class="btn btn-sm btn-secondary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 text-center">
                            <h3 class="text-info">{{ $seaCount }}</h3>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-4 text-center">
                            <h3 class="text-warning">{{ $seaInTransit }}</h3>
                            <small class="text-muted">In Transit</small>
                        </div>
                        <div class="col-4 text-center">
                            <h3 class="text-success">{{ $seaDelivered }}</h3>
                            <small class="text-muted">Delivered</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $clientCount }}</h3>
                    <p>Total Clients</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="{{ url('admin/clients') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalShipments }}</h3>
                    <p>All Shipments</p>
                </div>
                <div class="icon"><i class="fas fa-shipping-fast"></i></div>
                <a href="{{ url('admin/shipments') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $batchCount }}</h3>
                    <p>Batches / Containers</p>
                </div>
                <div class="icon"><i class="fas fa-layer-group"></i></div>
                <a href="{{ url('admin/batches') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $userCount }}</h3>
                    <p>System Users</p>
                </div>
                <div class="icon"><i class="fas fa-user-shield"></i></div>
                <a href="{{ url('admin/users') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Revenue -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <a href="{{ route('admin.air-cargo.dashboard') }}" style="color: inherit; text-decoration: none;">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-plane"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Air Cargo Revenue</span>
                        <span class="info-box-number">{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($airRevenue, 0) }}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-6">
            <a href="{{ route('admin.sea-cargo.dashboard') }}" style="color: inherit; text-decoration: none;">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-ship"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sea Cargo Revenue</span>
                        <span class="info-box-number">{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($seaRevenue, 0) }}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-6">
            <a href="{{ route('admin.reports.revenue') }}" style="color: inherit; text-decoration: none;">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-calendar-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Monthly Revenue</span>
                        <span class="info-box-number">{{ \App\Models\Setting::getCurrencySymbol() }} {{ number_format($monthlyRevenue, 0) }}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-6">
            <a href="{{ url('admin/invoices?status=paid') }}" style="color: inherit; text-decoration: none;">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Paid Invoices</span>
                        <span class="info-box-number">{{ $paidInvoices }}</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Air vs Sea Status</h3>
                </div>
                <div class="card-body">
                    <canvas id="cargoStatusChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shipments Trend (Last 6 Months)</h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyLineChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-plane"></i> Recent Air Shipments</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tracking #</th>
                                <th>Client</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAir as $s)
                                <tr>
                                    <td><a href="{{ route('admin.air-cargo.show', $s) }}">{{ $s->tracking_number }}</a></td>
                                    <td>{{ $s->client->name ?? 'N/A' }}</td>
                                    <td><span class="badge badge-info">{{ $s->current_status }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-ship"></i> Recent Sea Shipments</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tracking #</th>
                                <th>Client</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSea as $s)
                                <tr>
                                    <td><a href="{{ route('admin.sea-cargo.show', $s) }}">{{ $s->tracking_number }}</a></td>
                                    <td>{{ $s->client->name ?? 'N/A' }}</td>
                                    <td><span class="badge badge-primary">{{ $s->current_status }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
                <div class="card-body">
                    <a href="{{ route('admin.air-cargo.create') }}" class="btn btn-info"><i class="fas fa-plane"></i> New Air Shipment</a>
                    <a href="{{ route('admin.sea-cargo.create') }}" class="btn btn-primary"><i class="fas fa-ship"></i> New Sea Shipment</a>
                    <a href="{{ route('admin.air-cargo.dashboard') }}" class="btn btn-info"><i class="fas fa-tachometer-alt"></i> Air Dashboard</a>
                    <a href="{{ route('admin.sea-cargo.dashboard') }}" class="btn btn-primary"><i class="fas fa-tachometer-alt"></i> Sea Dashboard</a>
                    <a href="{{ url('admin/clients/create') }}" class="btn btn-success"><i class="fas fa-user-plus"></i> Add Client</a>
                    <a href="{{ url('admin/users/create') }}" class="btn btn-warning"><i class="fas fa-user-plus"></i> Add User</a>
                    <a href="{{ url('admin/reports') }}" class="btn btn-secondary"><i class="fas fa-chart-bar"></i> Reports</a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eagle Cargo Freights</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Support Call</b> +256 200 991 118
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var cargoCtx = document.getElementById('cargoStatusChart');
if (cargoCtx) {
    new Chart(cargoCtx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'In Transit', 'Delivered', 'On Hold'],
            datasets: [
                {
                    label: 'Air Cargo',
                    data: [{{ \App\Models\Shipment::where('shipment_type','air')->where('current_status','Pending')->count() }}, {{ $airInTransit }}, {{ $airDelivered }}, {{ \App\Models\Shipment::where('shipment_type','air')->whereIn('current_status',['On Hold','Cancelled'])->count() }}],
                    backgroundColor: 'rgba(23, 162, 184, 0.8)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Sea Cargo',
                    data: [{{ \App\Models\Shipment::where('shipment_type','sea')->where('current_status','Pending')->count() }}, {{ $seaInTransit }}, {{ $seaDelivered }}, {{ \App\Models\Shipment::where('shipment_type','sea')->whereIn('current_status',['On Hold','Cancelled'])->count() }}],
                    backgroundColor: 'rgba(0, 123, 255, 0.8)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
}

var monthlyCtx = document.getElementById('monthlyLineChart');
if (monthlyCtx) {
    var monthLabels = [];
    var today = new Date();
    for (var i = 5; i >= 0; i--) {
        var d = new Date(today.getFullYear(), today.getMonth() - i, 1);
        monthLabels.push(d.toLocaleString('default', { month: 'short', year: 'numeric' }));
    }
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [
                {
                    label: 'Air',
                    data: {!! json_encode($airMonthly) !!},
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Sea',
                    data: {!! json_encode($seaMonthly) !!},
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
}
</script>
@stop