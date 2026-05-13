@extends('adminlte::page')

@section('title', 'Analytics Dashboard')

@section('content_header')
    <h1>Analytics Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <!-- Shipments by Status -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shipments by Status</h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Shipments Trend -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shipments Trend (Last 6 Months)</h3>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Clients -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Top 10 Clients</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Client Name</th>
                        <th>Email</th>
                        <th>Total Shipments</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topClients as $index => $client)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->email }}</td>
                            <td><span class="badge badge-success">{{ $client->shipments_count }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Shipments by Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($shipmentsByStatus->pluck('current_status')) !!},
        datasets: [{
            data: {!! json_encode($shipmentsByStatus->pluck('total')) !!},
            backgroundColor: [
                '#f39c12',
                '#00c0ef',
                '#00a65a',
                '#dd4b39',
                '#605ca8'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Shipments Trend Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($shipmentsByMonth->pluck('month')) !!},
        datasets: [{
            label: 'Shipments',
            data: {!! json_encode($shipmentsByMonth->pluck('total')) !!},
            borderColor: '#00c0ef',
            backgroundColor: 'rgba(0, 192, 239, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
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

